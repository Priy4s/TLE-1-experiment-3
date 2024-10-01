<?php
session_start();
require_once 'includes/dbconnect.php';

// Fetch notifications for the logged-in user
$user_id = $_SESSION['user_id'];
$stmt = $db->prepare("SELECT id, notification_message FROM notifications WHERE user_id = ? AND is_read = 0");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$notifications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<div class="notifications">
    <h2>Notifications:</h2>
    <?php foreach ($notifications as $notification): ?>
        <p><?php echo htmlspecialchars($notification['notification_message']); ?>
            <a href="accept_notification.php?notification_id=<?php echo $notification['id']; ?>" class="btn btn-success">Accept</a>
        </p>
    <?php endforeach; ?>
</div>
