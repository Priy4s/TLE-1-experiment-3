<?php
require_once __DIR__ . '/includes/authentication.php';
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
    //header("Location: decline_question.php?question_id=$question_id");
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
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-body text-center">
            <h2 class="text-primary fw-normal"><?php echo 'Video call request'?></h2><br><br>
            <h3 class="fs-3"><?php echo htmlspecialchars($question['content'])?></h3>
            <p class="lead"><strong>Question ID:</strong> <?php echo $question['id']; ?></p>

            <form method="post">
                <button type="submit" name="answer" class="btn btn-primary me-2">Answer</button>
                <button type="submit" name="decline" class="btn btn-danger">Decline</button>
            </form>

            <div class="mt-3">
                <button id="back-to-questions" class="btn btn-outline-primary">Back to Questions</button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for handling the back-to-questions logic -->
<script>
    document.getElementById('back-to-questions').addEventListener('click', function () {
        // Use PHP to get the user role based on is_expert
        const isExpert = "<?php echo isset($_SESSION['is_expert']) && $_SESSION['is_expert'] == 1 ? 'expert' : 'user'; ?>";

        if (isExpert === 'expert') {
            window.location.href = 'index.php'; // Redirect to expert index
        } else if (isExpert === 'user') {
            window.location.href = 'user-index.php'; // Redirect to user index
        } else {
            alert('Unauthorized access.'); // Handle unauthorized access
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>