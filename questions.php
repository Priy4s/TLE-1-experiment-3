<?php
require_once __DIR__ . '/includes/authentication.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit();
}

require_once 'includes/dbconnect.php';  // Ensure correct database connection

// Fetch all questions from the database
function fetchQuestions($db) {
    $sql = "SELECT questions.id, questions.content, tags.tag_name 
            FROM questions 
            JOIN tags ON questions.tag_id = tags.id 
            WHERE questions.tag_id = ?";  // For filtering based on a specific category (if needed)

    $tag_id = $_GET['category_id'] ?? null; // Get category_id from URL or set to null

    if ($tag_id) {
        $stmt = $db->prepare($sql);
        $stmt->bind_param('i', $tag_id); // Bind category id to the query
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    } else {
        return [];  // If no category selected, return empty
    }
}

// Fetch questions
$questions = fetchQuestions($db);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['question_content'], $_POST['category_id'])) {
    $user_id = $_SESSION['user_id'];
    $question_content = $_POST['question_content'];
    $category_id = $_POST['category_id'];

    $sql = "INSERT INTO questions (user_id, tag_id, content) VALUES (?, ?, ?)";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("iis", $user_id, $category_id, $question_content);

    if ($stmt->execute()) {
        header("Location: index.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Questions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles/style.css" rel="stylesheet"> <!-- Linking your existing CSS -->
</head>

<body>

<div class="container my-5">
    <h1 class="text-center" style="color: var(--primary-color);">Questions List</h1>

    <div class="row">
        <?php foreach ($questions as $question): ?>
            <div class="col-md-4 mb-4">
                <div class="card p-3" style="background-color: var(--light-color);">
                    <h5 class="card-title" style="color: var(--primary-color);">Category: <?php echo htmlspecialchars($question['tag_name']); ?></h5>
                    <p class="card-text" style="color: var(--secondary-color);"><?php echo htmlspecialchars($question['content']); ?></p>

                    <!-- Accept Button -->
                    <a href="answer_question.php?question_id=<?php echo $question['id']; ?>" class="btn btn-primary">Accept</a>

                    <!-- Decline Button -->
                    <form method="post" action="decline_question.php" style="display:inline;">
                        <input type="hidden" name="question_id" value="<?php echo $question['id']; ?>">
                        <button type="submit" class="btn btn-danger">Decline</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
