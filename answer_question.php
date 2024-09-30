<?php
require_once 'includes/dbconnect.php';
session_start(); // Start session to access user_id

// Fetch question details
$question_id = $_GET['question_id'];
$stmt = $db->prepare("SELECT content FROM questions WHERE id = ?");
$stmt->bind_param('i', $question_id);
$stmt->execute();
$question = $stmt->get_result()->fetch_assoc();

// Get the logged-in user's ID (who is acting as an expert)
$user_id = $_SESSION['user_id'];

// Handle form submission (for answering)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $answer_content = $_POST['answer_content'];

    // Insert the answer into the answers table
    $stmt = $db->prepare("INSERT INTO answers (question_id, user_id, answer_content) VALUES (?, ?, ?)");
    $stmt->bind_param('iis', $question_id, $user_id, $answer_content);

    if ($stmt->execute()) {
        echo "Answer submitted successfully!";
    } else {
        echo "Error: Could not submit the answer. " . $stmt->error;
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Answer Question</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles/style.css" rel="stylesheet">
</head>

<body>

<div class="container my-5">
    <div class="card p-4" style="background-color: var(--light-color);">
        <h1 class="text-center" style="color: var(--primary-color);">Answer the Question</h1>
        <p><?php echo htmlspecialchars($question['content']); ?></p>

        <form method="post">
            <div class="mb-3">
                <label for="answer_content" class="form-label" style="color: var(--primary-color);">Your Answer</label>
                <textarea name="answer_content" id="answer_content" class="form-control form-field" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary submitButton">Submit Answer</button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
