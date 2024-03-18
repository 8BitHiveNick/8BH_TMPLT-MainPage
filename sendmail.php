<?php
// Process form data after reCAPTCHA validation
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verify reCAPTCHA response with Google
    $recaptchaResponse = $_POST['g-recaptcha-response'];
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6LfXzJApAAAAAD_ByIeAfIecMnrlFVzxiJ4J6EVn&response=$recaptchaResponse");
    $responseKeys = json_decode($response, true);

    if ($responseKeys["success"]) {
        // Form is valid, process the email
        $to = 'your@example.com';
        $subject = 'Contact Form Submission';
        $message = "Name: " . $_POST['fullName'] . "\n" .
                   "Email: " . $_POST['emailAddress'] . "\n" .
                   "Phone: " . $_POST['phone'] . "\n" .
                   "Message: " . $_POST['message'];
        $headers = 'From: marketing@8bithive.com';
        
        // Send the email (ensure
