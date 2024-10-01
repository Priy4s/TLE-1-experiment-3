<?php
require_once 'includes/dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question_id = $_POST['question_id'];
    $rating = $_POST['rating'];
    $hardcode_id = 15; // Hardcode the value 15 here

    // Prepare the SQL query
    $stmt = $db->prepare("INSERT INTO ratings (question_id, user_id, user_votes) VALUES (?, ?, ?)");
    // Bind the parameters: question_id, hardcoded user_id (15), and the rating
    $stmt->bind_param('iii', $question_id, $hardcode_id, $rating);

    // Execute the statement and check for errors
    if ($stmt->execute()) {
        echo "Rating saved successfully!";
    } else {
        echo "Error: Could not save the rating. " . $stmt->error;
    }
} else {
    echo "Invalid request method.";
}
?>
