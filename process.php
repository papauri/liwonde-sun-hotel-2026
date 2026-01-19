<?php
require_once 'includes/environment.php';

// Contact form handler
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['contact'])) {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $subject = htmlspecialchars($_POST['subject']);
    $message = htmlspecialchars($_POST['message']);

    // In a real implementation, you would process the form data here
    // For now, we'll just redirect back with a success message

    $to = defined('SITE_EMAIL') ? SITE_EMAIL : 'info@liwondesunhotel.com';
    $email_subject = "Contact Form: $subject";
    $email_body = "Name: $name\nEmail: $email\nSubject: $subject\nMessage:\n$message";
    $headers = "From: $email";

    // mail($to, $email_subject, $email_body, $headers); // Uncomment in production

    header("Location: ../pages/contact.php?message=success");
    exit();
}

// Booking form handler
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['booking'])) {
    $checkin = htmlspecialchars($_POST['checkin']);
    $checkout = htmlspecialchars($_POST['checkout']);
    $roomType = htmlspecialchars($_POST['room-type']);
    $guests = htmlspecialchars($_POST['guests']);
    $firstName = htmlspecialchars($_POST['first-name']);
    $lastName = htmlspecialchars($_POST['last-name']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);

    // In a real implementation, you would process the booking here
    // For now, we'll just redirect back with a success message

    header("Location: ../pages/booking.php?booking=confirmed");
    exit();
}

// Newsletter subscription handler
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['newsletter'])) {
    $email = htmlspecialchars($_POST['email']);

    // In a real implementation, you would add the email to your mailing list
    // For now, we'll just redirect back with a success message

    header("Location: ../index.php?subscribed=true");
    exit();
}
?>