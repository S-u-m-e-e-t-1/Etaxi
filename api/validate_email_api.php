<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $access_key = 'ff63c65ef314a52109f115e180250505';
    $email_address = $_POST['email'];

    // Initialize CURL:
    $ch = curl_init('http://apilayer.net/api/check?access_key=' . $access_key . '&email=' . urlencode($email_address));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Store the data:
    $json = curl_exec($ch);
    curl_close($ch);

    // Decode JSON response:
    $validationResult = json_decode($json, true);

    // Return validation result
    if ($validationResult['format_valid'] && $validationResult['smtp_check']) {
        echo json_encode(['success' => true, 'message' => 'Email is valid.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>