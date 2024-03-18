<?php
header('Content-Type: application/json'); // Important: Specify the response type as JSON

// Only process POST requests.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verify reCAPTCHA response with Google
    $recaptchaResponse = filter_input(INPUT_POST, 'g-recaptcha-response', FILTER_SANITIZE_STRING);
    $secretKey = "6LfXzJApAAAAAD_ByIeAfIecMnrlFVzxiJ4J6EVn"; // Replace with your actual secret key
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $secretKey . "&response=" . $recaptchaResponse);
    $responseKeys = json_decode($response, true);

    if ($responseKeys["success"]) {
        // Sanitize form data to prevent injection attacks
        $name = strip_tags(trim($_POST["fullName"]));
        $name = str_replace(array("\r","\n"),array(" "," "),$name);
        $email = filter_var(trim($_POST["emailAddress"]), FILTER_SANITIZE_EMAIL);
        $phone = trim($_POST["phone"]);
        $message = trim($_POST["message"]);

        // Check that data was sent to the mailer.
        if (empty($name) OR empty($message) OR !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Please complete the form and try again."]);
            exit;
        }

        // Set the recipient email address.
        $to = 'your@example.com'; // Note: Update this to your desired email address.

        // Set the email subject.
        $subject = 'Contact Form Submission from ' . $name;

        // Build the email content.
        $email_content = "Name: $name\nEmail: $email\n";
        if (!empty($phone)) {
            $email_content .= "Phone: $phone\n";
        }
        $email_content .= "Message:\n$message\n";

        // Build the email headers.
        $email_headers = "From: $name <$email>";

        // Attempt to send the email.
        if (mail($to, $subject, $email_content, $email_headers)) {
            http_response_code(200);
            echo json_encode(["success" => true, "message" => "Thank You! Your message has been sent."]);
        } else {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Oops! Something went wrong, and we couldn't send your message."]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "reCAPTCHA verification failed. Please try again."]);
    }
} else {
    // Not a POST request, set a 403 (forbidden) response code.
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "There was a problem with your submission. Please try again."]);
}
?>
