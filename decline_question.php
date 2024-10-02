<?php
require_once __DIR__ . '/includes/authentication.php';
require_once __DIR__ . '/includes/dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check and sanitize the question_id
    if (isset($_POST['question_id'])) {
        $question_id = intval($_POST['question_id']);

        // Debugging: echo the question_id
        echo "Received Question ID: " . $question_id . "<br>";

        // If "Cancel" button is clicked
        if (isset($_POST['cancel'])) {
            // Redirect back to the question details page
            header("Location: question_details.php?question_id=$question_id");
            exit();
        }

        // If "Decline" button is clicked
        if (isset($_POST['decline'])) {
            // Debugging: echo a message indicating the decline process has started
            echo "Decline button clicked. Proceeding to delete Question ID: " . $question_id . "<br>";

            // Delete the question from the database
            $deleteQuery = "DELETE FROM questions WHERE id = ?";
            $stmt = $db->prepare($deleteQuery);
            $stmt->bind_param("i", $question_id);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                // Debugging: echo a success message
                echo "Question ID " . $question_id . " successfully deleted.<br>";

                // Redirect to index page after successful deletion
                header("Location: index.php");
            } else {
                // Debugging: echo a failure message
                echo "Failed to delete the question with ID: " . $question_id . "<br>";
            }
            exit();
        }
    } else {
        echo "No question ID provided.<br>";
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

        <form method="post">
            <!-- Pass question_id as a hidden input field -->
            <input type="hidden" name="question_id" value="<?php echo htmlspecialchars($_GET['question_id']); ?>">

            <!-- Debugging: echo the question ID in the form -->
            <p>Debug: The Question ID in the form is: <?php echo htmlspecialchars($_GET['question_id']); ?></p>

            <button type="submit" name="decline" class="btn btn-danger">Yes, decline</button>
            <button type="submit" name="cancel" class="btn btn-secondary">Cancel</button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
