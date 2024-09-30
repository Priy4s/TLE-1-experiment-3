<?php
require_once 'includes/dbconnect.php';
session_start(); 

$question_id = $_GET['question_id'];
$stmt = $db->prepare("SELECT content FROM questions WHERE id = ?");
$stmt->bind_param('i', $question_id);
$stmt->execute();
$question = $stmt->get_result()->fetch_assoc();

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['answer_content'])) {
    $answer_content = $_POST['answer_content'];

    $stmt = $db->prepare("INSERT INTO answers (question_id, user_id, answer_content) VALUES (?, ?, ?)");
    $stmt->bind_param('iis', $question_id, $user_id, $answer_content);

    if ($stmt->execute()) {
      
        $answer_user_id = $user_id; // Since you are using the current user_id
        echo "Answer submitted successfully!";
        $show_rating = true; // Show the rating section
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

        <!-- Show rating system if answer is submitted -->
        <?php if (isset($show_rating) && $show_rating): ?>
            <div id="rating-section">
                <h3>Rate the Answer</h3>
                <div id="star-rating">
                    <span class="star" data-value="1">&#9734;</span>
                    <span class="star" data-value="2">&#9734;</span>
                    <span class="star" data-value="3">&#9734;</span>
                    <span class="star" data-value="4">&#9734;</span>
                    <span class="star" data-value="5">&#9734;</span>
                </div>
                <input type="hidden" id="rating-value" value="0">
                <button id="submit-rating" class="btn btn-success">Submit Rating</button>
                <div id="rating-message"></div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const stars = document.querySelectorAll('#star-rating .star');
    const ratingValue = document.getElementById('rating-value');
    const submitRatingButton = document.getElementById('submit-rating');
    const ratingMessage = document.getElementById('rating-message');

    if (stars.length > 0) {
        stars.forEach(star => {
            star.addEventListener('click', function () {
                const rating = this.getAttribute('data-value');
                ratingValue.value = rating;

                stars.forEach(s => s.innerHTML = '&#9734;');

                for (let i = 0; i < rating; i++) {
                    stars[i].innerHTML = '&#9733;'; // Filled star
                }
            });
        });

        submitRatingButton.addEventListener('click', function () {
            const rating = ratingValue.value;
            if (rating === '0') {
                ratingMessage.textContent = 'Please select a rating before submitting!';
                return;
            }

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'submit_rating.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    ratingMessage.textContent = 'Rating submitted successfully!';
                }
            };
        });
    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
