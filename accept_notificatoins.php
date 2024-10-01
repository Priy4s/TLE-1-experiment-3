<?php
require_once 'includes/dbconnect.php';

$notification_id = $_GET['notification_id'];

// Mark the notification as read
$stmt = $db->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?");
$stmt->bind_param('i', $notification_id);
$stmt->execute();

// Redirect to video call page
header("Location: video_call.php");
exit();
