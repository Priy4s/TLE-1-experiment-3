<?php
require_once __DIR__ . '/includes/dbconnect.php';

// Check if question_id is provided in the URL
if (isset($_GET['question_id'])) {
    $question_id = intval($_GET['question_id']);  // Sanitize the input

    // Fetch question details from the database
    $sql = "SELECT * FROM questions WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $question_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $question = $result->fetch_assoc();
    } else {
        echo "Question not found.";
        exit();
    }
} else {
    echo "No question ID provided.";
    exit();
}

// If "Answer" button is clicked
if (isset($_POST['answer'])) {
    // Redirect to the answer page, for example
    header("Location: answer_question.php?question_id=$question_id");
    exit();
}

// If "Decline" button is clicked
if (isset($_POST['decline'])) {
    // Redirect to decline logic, or handle it here
    header("Location: decline_question.php?question_id=$question_id");
    exit();
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Question Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="./styles/style.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2><?php echo 'Video call for '. htmlspecialchars($question['content']); ?></h2>
        <p><strong>Question ID:</strong> <?php echo $question['id']; ?></p>

        <form method="post">
            <button type="submit" name="answer" class="btn btn-success">Answer</button>
            <button type="submit" name="decline" class="btn btn-danger">Decline</button>
        </form>

        <a href="index.php" class="btn btn-primary mt-3">Back to Questions</a>
    </div>
</body>
</html>
