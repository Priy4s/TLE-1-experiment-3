<?php
require_once 'includes/dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question_id = $_POST['question_id'];
    $user_id = $_POST['user_id'];  
    $rating = $_POST['rating'];

    $stmt = $db->prepare("INSERT INTO ratings (question_id, user_id, user_votes) VALUES (?, ?, ?)");
    $stmt->bind_param('iii', $question_id, $user_id, $rating);

    if ($stmt->execute()) {
        echo "Rating saved successfully!";
    } else {
        echo "Error: Could not save the rating. " . $stmt->error;
    }
} else {
    echo "Invalid request method.";
}
?>
