<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Ride</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        #map {
            height: 100%;
            min-height: 600px;
            width: 100%;
        }
        .map-container {
            position: sticky;
            top: 20px;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid py-4">
        <div class="row">
            <!-- Form Column -->
            <div class="col-md-6 pr-md-2">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h1 class="card-title h2 mb-4 text-primary">Book a Ride</h1>
                        <?php if (isset($_GET['success'])): ?>
                            <div class="alert alert-success" role="alert">
                                Ride booked successfully! Ride ID: <?php echo htmlspecialchars($_GET['ride_id']); ?>, Fare: ₹ <?php echo htmlspecialchars($_GET['fare']); ?>
                            </div>
                        <?php elseif (isset($_GET['error'])): ?>
                            <div class="alert alert-danger" role="alert">
                                Error: <?php echo htmlspecialchars($_GET['error']); ?>
                            </div>
                        <?php endif; ?>
                        
                        <form action="../../controllers/RideController.php" method="POST">
                            <div class="form-group">
                                <label for="customer_id">Customer ID:</label>
                                <input type="text" class="form-control bg-light" id="customer_id" name="customer_id" 
                                       value="<?php echo htmlspecialchars($_SESSION['customer_id']); ?>" readonly>
                            </div>
                            <div class="form-group">
                                <label for="pickup_location">Pickup Location:</label>
                                <input type="text" class="form-control" id="pickup_location" name="pickup_location" required>
                            </div>
                            <div class="form-group">
                                <label for="dropoff_location">Dropoff Location:</label>
                                <input type="text" class="form-control" id="dropoff_location" name="dropoff_location" required>
                            </div>
                            
                            <div id="route-info" class="alert alert-info d-none mb-4">
                                <div id="distance-time"></div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="distance">Distance (km):</label>
                                        <input type="text" class="form-control bg-light" id="distance" name="distance" readonly required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="fare">Fare (₹):</label>
                                        <input type="text" class="form-control bg-light" id="fare" name="fare" readonly required>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success btn-lg btn-block mt-4" id="bookRideBtn" disabled>
                                Book Ride
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Map Column -->
            <div class="col-md-6 pl-md-2">
                <div class="card shadow-sm h-100">
                    <div class="card-body p-0">
                        <div id="map" class="rounded"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script async defer
        src="https://maps.gomaps.pro/maps/api/js?key={apikey}&libraries=geometry,places&callback=initMap">
    </script>


    <script>
        let map, directionsService, directionsRenderer;
        let autocomplete1, autocomplete2;
        let marker1, marker2;

        function initMap() {
            // Ganjam, Odisha coordinates
            const ganjamBounds = {
                north: 20.017,
                south: 19.059,
                east: 85.090,
                west: 84.108
            };

            const ganjamCenter = { 
                lat: 19.588, 
                lng: 84.578  // Coordinates for Ganjam district center
            };

            map = new google.maps.Map(document.getElementById('map'), {
                center: ganjamCenter,
                zoom: 10,
                restriction: {
                    latLngBounds: ganjamBounds,
                    strictBounds: false
                },
                mapTypeControl: true,
                mapTypeControlOptions: {
                    style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
                }
            });

            // Create a boundary rectangle for Ganjam
            const ganjamRectangle = new google.maps.Rectangle({
                bounds: ganjamBounds,
                strokeColor: '#FF0000',
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: '#FF0000',
                fillOpacity: 0.1
            });
            ganjamRectangle.setMap(map);

            directionsService = new google.maps.DirectionsService();
            directionsRenderer = new google.maps.DirectionsRenderer({
                map: map,
                suppressMarkers: true
            });

            const input1 = document.getElementById('pickup_location');
            const input2 = document.getElementById('dropoff_location');

            // Set autocomplete restrictions to Ganjam area
            const options = {
                bounds: new google.maps.LatLngBounds(
                    new google.maps.LatLng(ganjamBounds.south, ganjamBounds.west),
                    new google.maps.LatLng(ganjamBounds.north, ganjamBounds.east)
                ),
                strictBounds: true,
                componentRestrictions: { country: 'IN' }
            };

            autocomplete1 = new google.maps.places.Autocomplete(input1, options);
            autocomplete2 = new google.maps.places.Autocomplete(input2, options);

            autocomplete1.addListener('place_changed', () => {
                const place = autocomplete1.getPlace();
                setMarker(place, 'marker1');
                calculateRoute();
            });

            autocomplete2.addListener('place_changed', () => {
                const place = autocomplete2.getPlace();
                setMarker(place, 'marker2');
                calculateRoute();
            });
        }

        function setMarker(place, markerType) {
            if (!place.geometry) {
                console.log("No details available for: " + place.name);
                return;
            }

            const position = place.geometry.location;

            // Check if location is within Ganjam boundaries
            const ganjamBounds = {
                north: 20.017,
                south: 19.059,
                east: 85.090,
                west: 84.108
            };

            if (!isLocationInGanjam(position, ganjamBounds)) {
                const locationType = markerType === 'marker1' ? 'pickup' : 'dropoff';
                document.getElementById(locationType === 'pickup' ? 'pickup_location' : 'dropoff_location').value = '';
                
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-danger alert-dismissible fade show mt-2';
                alertDiv.innerHTML = `
                    <strong>Invalid Location!</strong> Selected ${locationType} location is outside Ganjam district.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                `;
                
                // Remove any existing alert
                const existingAlert = document.querySelector('.alert-danger:not([role="alert"])');
                if (existingAlert) {
                    existingAlert.remove();
                }
                
                // Insert alert after the input field
                const inputField = document.getElementById(locationType === 'pickup' ? 'pickup_location' : 'dropoff_location');
                inputField.parentNode.insertBefore(alertDiv, inputField.nextSibling);
                
                resetFormState();
                return;
            }

            const icon = {
                url: markerType === 'marker1' ? 'https://maps.google.com/mapfiles/ms/icons/green-dot.png' : 'https://maps.google.com/mapfiles/ms/icons/red-dot.png',
                scaledSize: new google.maps.Size(40, 40)
            };

            if (markerType === 'marker1') {
                if (marker1) marker1.setMap(null);
                marker1 = new google.maps.Marker({ 
                    position, 
                    map,
                    icon: icon,
                    title: 'Pickup Location'
                });
            } else {
                if (marker2) marker2.setMap(null);
                marker2 = new google.maps.Marker({ 
                    position, 
                    map,
                    icon: icon,
                    title: 'Dropoff Location'
                });
            }
        }

        // Function to check if location is within Ganjam boundaries
        function isLocationInGanjam(position, bounds) {
            return position.lat() >= bounds.south &&
                   position.lat() <= bounds.north &&
                   position.lng() >= bounds.west &&
                   position.lng() <= bounds.east;
        }

        function calculateRoute() {
            if (!marker1 || !marker2) return;

            // Disable the button while calculating
            document.getElementById('bookRideBtn').disabled = true;
            document.getElementById('bookRideBtn').innerHTML = 'Calculating Route...';

            const request = {
                origin: marker1.getPosition(),
                destination: marker2.getPosition(),
                travelMode: google.maps.TravelMode.DRIVING
            };

            directionsService.route(request, (result, status) => {
                if (status === 'OK') {
                    directionsRenderer.setDirections(result);
                    
                    const route = result.routes[0];
                    const distance = route.legs[0].distance.value / 1000; // Convert to km
                    const duration = route.legs[0].duration.text;
                    
                    document.getElementById('distance').value = distance.toFixed(2);
                    document.getElementById('fare').value = (distance * 10).toFixed(2); // ₹10 per km
                    
                    const routeInfo = document.getElementById('route-info');
                    routeInfo.classList.remove('d-none');
                    document.getElementById('distance-time').innerHTML = `
                        <strong>Distance:</strong> ${route.legs[0].distance.text}<br>
                        <strong>Estimated Time:</strong> ${duration}
                    `;

                    // Fit bounds to show the entire route
                    const bounds = new google.maps.LatLngBounds();
                    bounds.extend(marker1.getPosition());
                    bounds.extend(marker2.getPosition());
                    map.fitBounds(bounds);

                    // Enable the button after successful calculation
                    document.getElementById('bookRideBtn').disabled = false;
                    document.getElementById('bookRideBtn').innerHTML = 'Book Ride';
                } else {
                    // If route calculation fails
                    document.getElementById('bookRideBtn').disabled = true;
                    document.getElementById('bookRideBtn').innerHTML = 'Route Not Available';
                    
                    const routeInfo = document.getElementById('route-info');
                    routeInfo.classList.remove('d-none');
                    document.getElementById('distance-time').innerHTML = `
                        <strong>Error:</strong> Unable to calculate route. Please try different locations.
                    `;
                }
            });
        }

        function resetFormState() {
            document.getElementById('bookRideBtn').disabled = true;
            document.getElementById('bookRideBtn').innerHTML = 'Book Ride';
            document.getElementById('route-info').classList.add('d-none');
            document.getElementById('distance').value = '';
            document.getElementById('fare').value = '';
            
            // Clear any existing route from the map
            if (directionsRenderer) {
                directionsRenderer.setDirections({routes: []});
            }
            
            // Clear markers
            if (marker1) marker1.setMap(null);
            if (marker2) marker2.setMap(null);
        }

        // Add function to remove alerts when input changes
        function removeLocationAlerts() {
            const alerts = document.querySelectorAll('.alert-danger:not([role="alert"])');
            alerts.forEach(alert => alert.remove());
        }

        // Update event listeners
        document.getElementById('pickup_location').addEventListener('input', () => {
            resetFormState();
            removeLocationAlerts();
        });
        document.getElementById('dropoff_location').addEventListener('input', () => {
            resetFormState();
            removeLocationAlerts();
        });
    </script>
</body>
</html>
