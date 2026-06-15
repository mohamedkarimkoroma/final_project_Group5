<?php
// Sierra Leone SMS Integration using Africa's Talking
// To use this, you need an Africa's Talking account (free to start)

function sendSMS($phone, $message) {
    // Remove any non-digit characters from phone
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    // Ensure Sierra Leone format (starts with 232)
    if (substr($phone, 0, 1) == '0') {
        $phone = '232' . substr($phone, 1);
    }
    if (substr($phone, 0, 3) != '232') {
        $phone = '232' . $phone;
    }
    
    // Africa's Talking API Configuration
    $username = 'YOUR_USERNAME'; // Get from https://account.africastalking.com
    $apiKey = 'YOUR_API_KEY';     // Get from your dashboard
    
    // For now, return true (simulation)
    // In production, uncomment the code below
    
    /*
    $url = 'https://api.africastalking.com/version1/messaging';
    $data = array(
        'username' => $username,
        'to' => $phone,
        'message' => $message
    );
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('ApiKey: ' . $apiKey));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
    */
    
    // Simulated response for testing
    return ['success' => true];
}

// Function to send SOS alert to all contacts
function sendSOSAlert($passenger_name, $passenger_phone, $emergency_contacts, $location) {
    $message = "🚨 SALONE SAFE RIDE EMERGENCY 🚨\n\nPassenger: $passenger_name\nPhone: $passenger_phone\nLocation: $location\nTime: " . date('Y-m-d H:i:s') . "\n\nPlease contact authorities immediately. Police: 119, Ambulance: 999";
    
    // Send to all emergency contacts
    foreach ($emergency_contacts as $contact) {
        sendSMS($contact['phone'], $message);
    }
    
    // Send to police emergency line
    sendSMS('119', $message);
    
    return true;
}
?>