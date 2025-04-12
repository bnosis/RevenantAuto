<?php
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;

// Load environment variables
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

// Initialize error array
$errors = [];

// Pricing table based on car type
$pricingTable = [
    "sedan" => [150, 200, 300, 300, 500],
    "suv" => [200, 250, 350, 400, 600]
];

// Package labels corresponding to the indices
$packageLabels = [
    "Interior",
    "Interior + Exterior",
    "Interior + Exterior + Wax",
    "Exterior + Polish + Wax",
    "Exterior + Paint Correction + Polish + Wax"
];

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $message = isset($_POST['message']) ? trim($_POST['message']) : ''; // Message is optional
    $carTypes = $_POST['carType']; // Array of car types
    $packageTypes = $_POST['packageType']; // Array of package types (indexes)

    // Validate form fields
    if (empty($name)) {
        $errors['name'] = 'Name is required.';
    } elseif (!preg_match('/^[A-Za-z\s]+$/', $name)) {
        $errors['name'] = 'Name must only contain letters and spaces.';
    }

    if (empty($email)) {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format.';
    }

    if (!empty($phone) && !preg_match('/^\(\d{3}\) \d{3}-\d{4}$/', $phone)) {
        $errors['phone'] = 'Phone number must be in the format (xxx) xxx-xxxx.';
    }

    if (empty($address)) {
        $errors['address'] = 'Address is required.';
    }

    if (strlen($message) > 0 && (strlen($message) < 10 || strlen($message) > 500)) {
        $errors['message'] = 'Message must be between 10 and 500 characters.';
    }

    // Calculate total price
    $totalPrice = 0;
    for ($i = 0; $i < count($carTypes); $i++) {
        $carType = $carTypes[$i];
        $packageIndex = $packageTypes[$i];
        // Add price from pricing table using the car type and package index
        $totalPrice += $pricingTable[$carType][$packageIndex];
    }

    // If there are no errors, send email
    if (empty($errors)) {
        $to = 'brendan@revenantauto.com';
        $subject = "Message from $name";

        $mail = new PHPMailer();

        $mail->isSMTP();
        $mail->Host = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['EMAIL_USER'];
        $mail->Password = $_ENV['EMAIL_PASSWORD'];
        $mail->SMTPSecure = $_ENV['SMTP_SECURE'];
        $mail->Port = $_ENV['SMTP_PORT'];

        $mail->setFrom($email, 'Revenent Auto Detailing');
        $mail->addAddress($to);
        $mail->addReplyTo($email);

        $mail->isHTML(true);
        $mail->Subject = $subject;

        $mail->Body = "
            <div style='font-family: Arial, sans-serif; color: black; line-height: 1.6;'>
                <h2 style='color: #DAA520; text-shadow: 0 0 2px #00000033;'>Revenent Auto Detailing</h2>
                <p><strong>Name:</strong> $name</p>
                <p><strong>Email:</strong> $email</p>
                <p><strong>Phone:</strong> $phone</p>
                <p><strong>Address:</strong> $address</p>
                <p><strong>Message:</strong> $message</p>
            </div>";

        // Add car information to email
        $mail->Body .= "<h3>Car Packages:</h3><ul>";
        for ($i = 0; $i < count($carTypes); $i++) {
            $carType = $carTypes[$i];
            $packageIndex = $packageTypes[$i];
            $packageLabel = $packageLabels[$packageIndex];
            $packagePrice = $pricingTable[$carType][$packageIndex];
            $mail->Body .= "<li><strong>Car Type:</strong> $carType, <strong>Package:</strong> $packageLabel - $$packagePrice</li>";
        }
        $mail->Body .= "</ul>";

        $mail->Body .= "<h3>Total Price: $$totalPrice</h3>";

        // Send the email
        if (!$mail->send()) {
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            echo 'Message has been sent';
        }
    } else {
        // Show form errors
        foreach ($errors as $field => $error) {
            echo htmlspecialchars($error);
        }
    }
} else {
    echo 'Invalid Request';
}
?>