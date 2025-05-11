<?php

require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../models/Admin.php';
require_once __DIR__ . '/../models/Customer.php';
require_once __DIR__ . '/../models/Driver.php';
require_once __DIR__ . '/../models/Ride.php';
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../models/PromoCodes.php';
require_once __DIR__ . '/../models/Messages.php';
require_once __DIR__ . '/../models/Notification.php';
require_once __DIR__ . '/../models/Blog.php';

$database = new Database();
$db = $database->getConnection();
$admin = new Admin($db);
$driver = new Driver($db);
$blog = new Blog($db);
$customer = new Customer($db);
$ride = new Ride($db);
$payment = new Payment($db);
$promoCodes = new PromoCodes($db);
$messages = new Messages($db);
$notification = new Notification($db);

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'updateProfile') {
        $adminId = $_SESSION['admin']['id']; // Fetch admin ID from session
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_BCRYPT) : null;
        $phone = $_POST['phone'];
        $profileImage = $_FILES['profile_image']['name'];

        // Handle profile image upload
        if (!empty($profileImage)) {
            $targetDir = __DIR__ . "/../assets/images/";
            $targetFile = $targetDir . basename($profileImage);
            move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFile);
        } else {
            $adminDetails = $admin->getAdminById($adminId);
            $profileImage = $adminDetails['profile_image'];
        }

        // Update admin details
        $updateData = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'profile_image' => $profileImage
        ];
        if ($password) {
            $updateData['password'] = $password;
        }

        $updateResult = $admin->updateAdmin($adminId, $updateData);
        if ($updateResult['success']) {
            $_SESSION['success_message'] = "Profile updated successfully.";
        } else {
            $_SESSION['error_message'] = "Error updating profile: " . $updateResult['error'];
        }

        // Redirect back to the edit profile page
        header("Location: ../views/admin/edit-profile.php");
        exit();
    }

    $data = json_decode(file_get_contents("php://input"), true);
    if (isset($data['action'])) {
        switch ($data['action']) {
            case 'update':
                $driverId = $data['driverId'];
                $driverData = $data['driverData'];
                $vehicleData = $data['vehicleData'];
                $updateResult = $driver->updateDriver($driverId, $driverData);
                if ($updateResult['success']) {
                    $vehicleResult = $driver->updateVehicle($driverId, $vehicleData);
                    if ($vehicleResult['success']) {
                        echo json_encode(['success' => true]);
                    } else {
                        echo json_encode(['error' => $vehicleResult['error']]);
                    }
                } else {
                    echo json_encode(['error' => $updateResult['error']]);
                }
                exit();
                break;

            case 'delete':
                $driverId = $data['driverId'];
                $deleteResult = $driver->deleteDriver($driverId);
                echo json_encode($deleteResult);
                exit();
                break;

            case 'deleteCustomer':
                $customerId = $data['customerId'];
                $deleteResult = $customer->deleteCustomer($customerId);
                echo json_encode($deleteResult);
                exit();
                break;

            case 'approve':
                $driverId = $data['driverId'];
                $approveResult = $driver->approveDriver($driverId);
                echo json_encode($approveResult);
                exit();
                break;

            case 'reject':
                $driverId = $data['driverId'];
                $rejectResult = $driver->rejectDriver($driverId);
                echo json_encode($rejectResult);
                exit();
                break;

            case 'viewDetails':
                $driverId = $data['driverId'];
                $driverDetails = $driver->getDriverById($driverId);
                if ($driverDetails['success']) {
                    $vehicleDetails = $driver->getVehicleById($driverId);
                    if ($vehicleDetails['success']) {
                        echo json_encode([
                            'driver' => $driverDetails['driver'],
                            'vehicle' => $vehicleDetails['vehicle']
                        ]);
                    } else {
                        echo json_encode(["success" => false, "error" => $vehicleDetails['error']]);
                    }
                } else {
                    echo json_encode(["success" => false, "error" => $driverDetails['error']]);
                }
                exit();
                break;

            case 'deleteBlog':
                $blogId = $data['blogId'];
                $deleteResult = $blog->deleteBlog($blogId);
                echo json_encode(["success" => $deleteResult]);
                exit();
                break;

            case 'viewBlog':
                $blogId = $data['blogId'];
                $blogDetails = $blog->getBlogById($blogId);
                echo json_encode($blogDetails);
                exit();
                break;

            case 'getAllRides':
                $ridesResult = $ride->getAllRides();
                echo json_encode($ridesResult);
                exit();
                break;

            case 'getRideDetails':
                $rideId = $data['rideId'];
                $rideDetails = $ride->getRideDetails($rideId);
                echo json_encode($rideDetails);
                exit();
                break;

            case 'getAllPayments':
                $paymentsResult = $payment->getAllPayments();
                echo json_encode($paymentsResult);
                exit();
                break;

            case 'getAllPromoCodes':
                $promoCodesResult = $promoCodes->getAllPromoCodes();
                echo json_encode($promoCodesResult);
                exit();
                break;

            case 'sendMessage':
                $senderEmail = $data['senderEmail'];
                $receiverEmail = $data['receiverEmail'];
                $content = $data['content'];
                $sendResult = $messages->sendMessage($senderEmail, $receiverEmail, $content);
                echo json_encode($sendResult);
                exit();
                break;

            case 'viewDetails':
                $messageId = $data['messageId'];
                $updateResult = $messages->updateMessageStatus($messageId, 'seen');
                echo json_encode($updateResult);
                exit();
                break;

            case 'addNotification':
                $message = $data['message'];
                $created_by = $_SESSION['admin']['id']; // Assuming admin ID is stored in the session
                $addResult = $notification->addNotification($created_by, $message);
                echo json_encode($addResult);
                exit();
                break;

            case 'updateNotification':
                $id = $data['id'];
                $message = $data['message'];
                $updateResult = $notification->updateNotification($id, $message);
                echo json_encode($updateResult);
                exit();
                break;

            case 'deleteNotification':
                $id = $data['id'];
                $deleteResult = $notification->deleteNotification($id);
                echo json_encode($deleteResult);
                exit();
                break;

            default:
                echo json_encode(["success" => false, "error" => "Invalid action"]);
                exit();
                break;
        }
    }
}


$driversResult = $driver->getAllDrivers();
if ($driversResult['success']) {
    $drivers = $driversResult['drivers'];
} else {
    $error = $driversResult['error'];
}
$blogsResult = $blog->getAllBlogs();
if ($blogsResult['success']) {
    $blogs = $blogsResult['blogs'];
} else {
    $error = $blogsResult['error'];
}

$customersResult = $customer->getAllCustomers();
if ($customersResult['success']) {
    $customers = $customersResult['customers'];
} else {
    $error = $customersResult['error'];
}

$RidesResult = $ride->getAllRides();
if ($RidesResult['success']) {
    $rides = $RidesResult['rides'];
} else {
    $error = $RidesResult['error'];
}

$paymentsResult = $payment->getAllPayments();
if ($paymentsResult['success']) {
    $payments = $paymentsResult['payments'];
} else {
    $error = $paymentsResult['error'];
}

$promoCodesResult = $promoCodes->getAllPromoCodes();
if ($promoCodesResult['success']) {
    $promoCodes = $promoCodesResult['promoCodes'];
} else {
    $error = $promoCodesResult['error'];
}
$notificationsResult = $notification->getAllNotifications();
if ($notificationsResult['success']) {
    $notifications = $notificationsResult['notifications'];
} else {
    $error = $notificationsResult['error'];
}


$promoCodesCount = count($promoCodes);
$totalRides = count($rides);
$totalEarnings = array_sum(array_column($payments, 'amount'));
$activeDrivers = count($drivers);
$registeredCustomers =  count($customers);

function getAdminData() {
    global $admin;
    $adminData = $_SESSION['admin'];
    return $admin->getAdminById($adminData['id']);
}
$adminData = getAdminData();

function getAdminEmail() {
    global $admin;
    $adminData = $_SESSION['admin'];
    $adminDetails = $admin->getAdminById($adminData['id']);
    return $adminDetails['email'];
}

function getMessages() {
    global $messages;
    $adminEmail = getAdminEmail();
    $sentMessages = $messages->getMessagesBySender($adminEmail);
    $receivedMessages = $messages->getMessagesByReceiver($adminEmail);
    return ['sentMessages' => $sentMessages, 'receivedMessages' => $receivedMessages];
}

$messagesData = getMessages();
$sentMessages = $messagesData['sentMessages'];
$receivedMessages = $messagesData['receivedMessages'];
?>

