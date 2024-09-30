<?php
require_once 'includes/dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question_id = $_POST['question_id'];

    // Mark question as declined or remove from the database
    $stmt = $db->prepare("DELETE FROM questions WHERE id = ?");
    $stmt->bind_param('i', $question_id);

    if ($stmt->execute()) {
        header("Location: questions.php");
        exit;
    } else {
        echo "Error: Could not decline the question.";
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Decline Question</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles/style.css" rel="stylesheet">
</head>

<body>

<div class="container my-5">
    <div class="card p-4" style="background-color: var(--light-color);">
        <h1 class="text-center" style="color: var(--primary-color);">Are you sure you want to decline this question?</h1>
        <p><?php echo htmlspecialchars($question['content']); ?></p>

        <form method="post">
            <button type="submit" class="btn btn-danger">Yes, Decline</button>
            <a href="questions.php" class="btn btn-secondary" style="background-color: var(--accent-color);">Cancel</a>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
