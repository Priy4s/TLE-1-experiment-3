<?php
require_once __DIR__ . '/includes/authentication.php';
require_once 'includes/dbconnect.php';

$notification_id = $_GET['notification_id'];

// Mark the notification as accepted
$stmt = $db->prepare("UPDATE notifications SET status = 'accepted' WHERE id = ?");
$stmt->bind_param('i', $notification_id);
$stmt->execute();

header("Location: index.php");
exit();
