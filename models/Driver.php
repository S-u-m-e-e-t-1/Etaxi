<?php
class Driver {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register($data) {
        $name = $data['name'];
        $email = $data['email'];
        $password = password_hash($data['password'], PASSWORD_BCRYPT);
        $phone = $data['phone'];
        $id_number = $data['id_number'];
        $status = 'pending';
        $created_at = date('Y-m-d H:i:s');

        // Check if email or phone already exists in drivers table
        $check_driver_sql = "SELECT email, phone FROM drivers WHERE email = ? OR phone = ?";
        $stmt = $this->conn->prepare($check_driver_sql);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return ["success" => false, "error" => "Database error"];
        }
        $stmt->bind_param("ss", $email, $phone);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            return ["success" => false, "error" => "Email or phone number already exists!"];
        }

        // Generate unique filenames based on user's credentials
        $profile_image_name = $name . '_' . substr($phone, -4) . '_profile_' . time() . '.' . pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
        $id_image_name = $name . '_' . substr($id_number, -4) . '_id_' . time() . '.' . pathinfo($_FILES['id_image']['name'], PATHINFO_EXTENSION);

        // Upload profile image
        $profile_image_tmp = $_FILES['profile_image']['tmp_name'];
        $profile_image_folder = '../uploads/drivers/profile/' . $profile_image_name;
        move_uploaded_file($profile_image_tmp, $profile_image_folder);

        // Upload Aadhaar proof
        $id_image_tmp = $_FILES['id_image']['tmp_name'];
        $id_image_folder = '../uploads/drivers/id/' . $id_image_name;
        move_uploaded_file($id_image_tmp, $id_image_folder);

        // Insert driver data into the database
        $sql_driver = "INSERT INTO drivers (name, email, password, phone, id_number, id_image, status, profile_image, created_at) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql_driver);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return ["success" => false, "error" => "Database error"];
        }
        $stmt->bind_param("sssssssss", $name, $email, $password, $phone, $id_number, $id_image_name, $status, $profile_image_name, $created_at);

        if ($stmt->execute()) {
            $driver_id = $stmt->insert_id;

            $vehicle_type = $data['vehicle_type'];
            $vehicle_model = $data['vehicle_model'];
            $vehicle_number = $data['vehicle_number'];
            $license_number = $data['license_number'];

            // Check if vehicle number already exists in vehicles table
            $check_vehicle_sql = "SELECT vehicle_number FROM vehicles WHERE vehicle_number = ?";
            $stmt = $this->conn->prepare($check_vehicle_sql);
            if (!$stmt) {
                error_log("Prepare failed: " . $this->conn->error);
                return ["success" => false, "error" => "Database error"];
            }
            $stmt->bind_param("s", $vehicle_number);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                return ["success" => false, "error" => "Vehicle number already exists!"];
            }

            // Generate unique filenames based on user's credentials
            $vehicle_image_name = $vehicle_number . '_vehicle_' . time() . '.' . pathinfo($_FILES['vehicle_image']['name'], PATHINFO_EXTENSION);
            $license_image_name = $license_number . '_license_' . time() . '.' . pathinfo($_FILES['license_image']['name'], PATHINFO_EXTENSION);

            // Upload vehicle image
            $vehicle_image_tmp = $_FILES['vehicle_image']['tmp_name'];
            $vehicle_image_folder = '../uploads/drivers/vehicle/' . $vehicle_image_name;
            move_uploaded_file($vehicle_image_tmp, $vehicle_image_folder);

            // Upload license proof
            $license_image_tmp = $_FILES['license_image']['tmp_name'];
            $license_image_folder = '../uploads/drivers/license/' . $license_image_name;
            move_uploaded_file($license_image_tmp, $license_image_folder);

            // Insert vehicle data into the database
            $sql_vehicle = "INSERT INTO vehicles (driver_id, license_number, license_image, vehicle_type, vehicle_model, vehicle_number, vehicle_image, created_at) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql_vehicle);
            if (!$stmt) {
                error_log("Prepare failed: " . $this->conn->error);
                return ["success" => false, "error" => "Database error"];
            }
            $stmt->bind_param("isssssss", $driver_id, $license_number, $license_image_name, $vehicle_type, $vehicle_model, $vehicle_number, $vehicle_image_name, $created_at);

            if ($stmt->execute()) {
                return ["success" => true];
            } else {
                error_log("Execute failed: " . $stmt->error);
                return ["success" => false, "error" => "Error: " . $stmt->error];
            }
        } else {
            error_log("Execute failed: " . $stmt->error);
            return ["success" => false, "error" => "Error: " . $stmt->error];
        }
    }

    public function login($email, $password) {
        $sql = "SELECT * FROM drivers WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return ["success" => false, "error" => "Database error"];
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $driver = $result->fetch_assoc();

        if ($driver && password_verify($password, $driver['password'])) {
            return ["success" => true, "driver" => $driver, "role" => "driver"];
        } else {
            return ["success" => false, "error" => "Invalid email or password"];
        }
    }

    public function loginWithOtp($phone, $otp) {
        $sql = "SELECT * FROM drivers WHERE phone = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return ["success" => false, "error" => "Database error"];
        }
        $stmt->bind_param("s", $phone);
        $stmt->execute();
        $result = $stmt->get_result();
        $driver = $result->fetch_assoc();

        if ($driver && isset($_SESSION['otp']) && isset($_SESSION['otp_phone']) && $_SESSION['otp'] == $otp && $_SESSION['otp_phone'] == $phone) {
            return ["success" => true, "id" => $driver['id'], "driver" => $driver, "role" => "driver"];
        } else {
            return ["success" => false, "error" => "Invalid OTP"];
        }
    }
    
    public function getAllDrivers() {
        $sql = "SELECT * FROM drivers";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return ["success" => false, "error" => "Database error"];
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $drivers = $result->fetch_all(MYSQLI_ASSOC);

        return ["success" => true, "drivers" => $drivers];
    }

    public function getDriverById($driver_id) {
        $sql = "SELECT * FROM drivers WHERE id = ?";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            error_log("Prepare failed (getDriverById): " . $this->conn->error);
            return ["success" => false, "error" => "Database error"];
        }

        $stmt->bind_param("i", $driver_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $driver = $result->fetch_assoc();

        if (!$driver) {
            error_log("No driver found for ID: $driver_id");
            return ["success" => false, "error" => "Driver not found"];
        }

        return ["success" => true, "driver" => $driver];
    }

    public function getDriverVehicles($driver_id) {
        $sql = "SELECT * FROM vehicles WHERE driver_id = ?";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            error_log("Prepare failed (getDriverVehicles): " . $this->conn->error);
            return ["success" => false, "error" => "Database error"];
        }

        $stmt->bind_param("i", $driver_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $vehicles = $result->fetch_all(MYSQLI_ASSOC);

        if (!$vehicles) {
            error_log("No vehicles found for driver ID: $driver_id");
        }

        return ["success" => true, "vehicles" => $vehicles];
    }

    public function updateDriver($driverId, $driverData) {
        $sql = "UPDATE drivers SET name = ?, email = ?, phone = ?, id_number = ?, status = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            error_log("Prepare failed (updateDriver): " . $this->conn->error);
            return ["success" => false, "error" => "Database error"];
        }

        $stmt->bind_param("sssssi", $driverData['name'], $driverData['email'], $driverData['phone'], $driverData['id_number'], $driverData['status'], $driverId);

        if ($stmt->execute()) {
            return ["success" => true];
        } else {
            error_log("Execute failed (updateDriver): " . $stmt->error);
            return ["success" => false, "error" => "Error: " . $stmt->error];
        }
    }

    public function deleteDriver($driverId) {
        $sql = "DELETE FROM drivers WHERE id = ?";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            error_log("Prepare failed (deleteDriver): " . $this->conn->error);
            return ["success" => false, "error" => "Database error"];
        }

        $stmt->bind_param("i", $driverId);

        if ($stmt->execute()) {
            return ["success" => true];
        } else {
            error_log("Execute failed (deleteDriver): " . $stmt->error);
            return ["success" => false, "error" => "Error: " . $stmt->error];
        }
    }

    public function approveDriver($driverId) {
        try {
            $this->conn->begin_transaction();

            // Update driver status to 'approved'
            $sql = "UPDATE drivers SET status = 'approved' WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Prepare failed (approveDriver): " . $this->conn->error);
            }
            $stmt->bind_param("i", $driverId);
            $stmt->execute();

            // Check if driver availability already exists
            $check_sql = "SELECT id FROM driver_availability WHERE driver_id = ?";
            $stmt = $this->conn->prepare($check_sql);
            if (!$stmt) {
                throw new Exception("Prepare failed (check availability): " . $this->conn->error);
            }
            $stmt->bind_param("i", $driverId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                // Insert new availability record
                $insert_sql = "INSERT INTO driver_availability (driver_id, is_available, updated_at) VALUES (?, 1, NOW())";
                $stmt = $this->conn->prepare($insert_sql);
                if (!$stmt) {
                    throw new Exception("Prepare failed (insert availability): " . $this->conn->error);
                }
                $stmt->bind_param("i", $driverId);
                $stmt->execute();
            }

            $this->conn->commit();
            return ["success" => true];
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Approve driver failed: " . $e->getMessage());
            return ["success" => false, "error" => $e->getMessage()];
        }
    }

    public function rejectDriver($driverId) {
        $sql = "UPDATE drivers SET status = 'rejected' WHERE id = ?";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            error_log("Prepare failed (rejectDriver): " . $this->conn->error);
            return ["success" => false, "error" => "Database error"];
        }

        $stmt->bind_param("i", $driverId);

        if ($stmt->execute()) {
            return ["success" => true];
        } else {
            error_log("Execute failed (rejectDriver): " . $stmt->error);
            return ["success" => false, "error" => "Error: " . $stmt->error];
        }
    }

    public function updateDriverAvailability($driver_id, $is_available) {
        $sql = "UPDATE driver_availability SET is_available = ?, updated_at = NOW() WHERE driver_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed (updateDriverAvailability): " . $this->conn->error);
            return ["success" => false, "error" => "Database error"];
        }
        $stmt->bind_param("ii", $is_available, $driver_id);
        if ($stmt->execute()) {
            return ["success" => true];
        } else {
            error_log("Execute failed (updateDriverAvailability): " . $stmt->error);
            return ["success" => false, "error" => $stmt->error];
        }
    }

    public function checkAndUpdateAvailability($driver_id, $status) {
        // First check if record exists
        $check_sql = "SELECT id FROM driver_availability WHERE driver_id = ?";
        $stmt = $this->conn->prepare($check_sql);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return ["success" => false, "error" => "Database error"];
        }
        
        $stmt->bind_param("i", $driver_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            // Insert new record
            $insert_sql = "INSERT INTO driver_availability (driver_id, is_available, updated_at) VALUES (?, ?, NOW())";
            $stmt = $this->conn->prepare($insert_sql);
            $stmt->bind_param("ii", $driver_id, $status);
        } else {
            // Update existing record
            $update_sql = "UPDATE driver_availability SET is_available = ?, updated_at = NOW() WHERE driver_id = ?";
            $stmt = $this->conn->prepare($update_sql);
            $stmt->bind_param("ii", $status, $driver_id);
        }
        
        if ($stmt->execute()) {
            return ["success" => true];
        } else {
            error_log("Execute failed: " . $stmt->error);
            return ["success" => false, "error" => "Error updating availability"];
        }
    }

    public function getDriverProfile($driver_id) {
        $sql = "SELECT d.*, v.* FROM drivers d 
                LEFT JOIN vehicles v ON d.id = v.driver_id 
                WHERE d.id = ?";
        $stmt = $this->conn->prepare($sql);
        
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return ["success" => false, "error" => "Database error"];
        }
        
        $stmt->bind_param("i", $driver_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $profile = $result->fetch_assoc();
        
        return ["success" => true, "profile" => $profile];
    }

    public function updateProfile($driver_id, $data, $files) {
        try {
            $this->conn->begin_transaction();

            // Update driver information
            $sql_driver = "UPDATE drivers SET 
                          name = ?, email = ?, phone = ?, id_number = ? 
                          WHERE id = ?";
            $stmt = $this->conn->prepare($sql_driver);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            $stmt->bind_param("ssssi", 
                $data['name'], 
                $data['email'], 
                $data['phone'], 
                $data['id_number'], 
                $driver_id
            );
            $stmt->execute();

            // Handle driver images
            if (!empty($files['profile_image']['name'])) {
                $profile_image = $this->uploadImage($files['profile_image'], 'profile', $data['name']);
                $sql = "UPDATE drivers SET profile_image = ? WHERE id = ?";
                $stmt = $this->conn->prepare($sql);
                if (!$stmt) {
                    throw new Exception("Prepare failed: " . $this->conn->error);
                }
                $stmt->bind_param("si", $profile_image, $driver_id);
                $stmt->execute();
            }

            if (!empty($files['id_image']['name'])) {
                $id_image = $this->uploadImage($files['id_image'], 'id', $data['id_number']);
                $sql = "UPDATE drivers SET id_image = ? WHERE id = ?";
                $stmt = $this->conn->prepare($sql);
                if (!$stmt) {
                    throw new Exception("Prepare failed: " . $this->conn->error);
                }
                $stmt->bind_param("si", $id_image, $driver_id);
                $stmt->execute();
            }

            $this->conn->commit();
            return ["success" => true];
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Profile update failed: " . $e->getMessage());
            return ["success" => false, "error" => $e->getMessage()];
        }
    }

    private function uploadImage($file, $type, $prefix) {
        $upload_dir = '../uploads/drivers/' . $type . '/';
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $prefix . '_' . $type . '_' . time() . '.' . $extension;
        $target_path = $upload_dir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $target_path)) {
            throw new Exception("Failed to upload " . $type . " image");
        }

        return $filename;
    }

    public function updateVehicle($driver_id, $data, $files) {
        try {
            $this->conn->begin_transaction();

            // Check if vehicle exists
            $check_sql = "SELECT id FROM vehicles WHERE driver_id = ?";
            $stmt = $this->conn->prepare($check_sql);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            $stmt->bind_param("i", $driver_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Update existing vehicle
                $sql = "UPDATE vehicles SET 
                        license_number = ?, vehicle_type = ?, vehicle_model = ?, vehicle_number = ? 
                        WHERE driver_id = ?";
                $stmt = $this->conn->prepare($sql);
                if (!$stmt) {
                    throw new Exception("Prepare failed: " . $this->conn->error);
                }
                $stmt->bind_param("ssssi", 
                    $data['license_number'], 
                    $data['vehicle_type'], 
                    $data['vehicle_model'], 
                    $data['vehicle_number'], 
                    $driver_id
                );
            } else {
                // Insert new vehicle
                $sql = "INSERT INTO vehicles 
                        (driver_id, license_number, vehicle_type, vehicle_model, vehicle_number) 
                        VALUES (?, ?, ?, ?, ?)";
                $stmt = $this->conn->prepare($sql);
                if (!$stmt) {
                    throw new Exception("Prepare failed: " . $this->conn->error);
                }
                $stmt->bind_param("issss", 
                    $driver_id, 
                    $data['license_number'], 
                    $data['vehicle_type'], 
                    $data['vehicle_model'], 
                    $data['vehicle_number']
                );
            }
            $stmt->execute();

            // Handle vehicle images
            if (!empty($files['vehicle_image']['name'])) {
                $vehicle_image = $this->uploadImage($files['vehicle_image'], 'vehicle', $data['vehicle_number']);
                $sql = "UPDATE vehicles SET vehicle_image = ? WHERE driver_id = ?";
                $stmt = $this->conn->prepare($sql);
                if (!$stmt) {
                    throw new Exception("Prepare failed: " . $this->conn->error);
                }
                $stmt->bind_param("si", $vehicle_image, $driver_id);
                $stmt->execute();
            }

            if (!empty($files['license_image']['name'])) {
                $license_image = $this->uploadImage($files['license_image'], 'license', $data['license_number']);
                $sql = "UPDATE vehicles SET license_image = ? WHERE driver_id = ?";
                $stmt = $this->conn->prepare($sql);
                if (!$stmt) {
                    throw new Exception("Prepare failed: " . $this->conn->error);
                }
                $stmt->bind_param("si", $license_image, $driver_id);
                $stmt->execute();
            }

            $this->conn->commit();
            return ["success" => true];
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Vehicle update failed: " . $e->getMessage());
            return ["success" => false, "error" => $e->getMessage()];
        }
    }

    public function getVehicleById($driver_id) {
        $sql = "SELECT * FROM vehicles WHERE driver_id = ?";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            error_log("Prepare failed (getVehicleById): " . $this->conn->error);
            return ["success" => false, "error" => "Database error"];
        }

        $stmt->bind_param("i", $driver_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $vehicle = $result->fetch_assoc();

        if (!$vehicle) {
            // Not treating this as an error, just no vehicle found
            return ["success" => true, "vehicle" => null];
        }

        return ["success" => true, "vehicle" => $vehicle];
    }

    public function updateAvailabilityStatus($driver_id, $is_available) {
        $sql = "UPDATE driver_availability SET is_available = 0, updated_at = NOW() WHERE driver_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("i", $driver_id);
        return $stmt->execute();
    }

    public function getDriverAvailabilityStatus($driver_id) {
        $sql = "SELECT is_available FROM driver_availability WHERE driver_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("i", $driver_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $availability = $result->fetch_assoc();

        return $availability ? $availability['is_available'] : false;
    }
}
?>
