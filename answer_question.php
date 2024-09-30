<?php
require_once __DIR__ . '/includes/authentication.php';
require_once 'includes/dbconnect.php';

$question_id = $_GET['question_id'];
$stmt = $db->prepare("SELECT content FROM questions WHERE id = ?");
$stmt->bind_param('i', $question_id);
$stmt->execute();
$question = $stmt->get_result()->fetch_assoc();

$user_id = $_SESSION['user_id'];


// Handle answer submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['answer_content'])) {
    $answer_content = $_POST['answer_content'];

    $stmt = $db->prepare("INSERT INTO answers (question_id, user_id, answer_content) VALUES (?, ?, ?)");
    $stmt->bind_param('iis', $question_id, $user_id, $answer_content);

    if ($stmt->execute()) {
        $show_rating = true; // Show the rating section
    } else {
        echo "Error: Could not submit the answer. " . $stmt->error;
    }
}

// Handle rating submission
if (isset($_POST['rating_value']) && isset($_POST['question_id'])) {
    $rating_value = $_POST['rating_value'];
    $question_id = $_POST['question_id'];

    // Use a prepared statement to insert or update the rating
    $stmt = $db->prepare("
        INSERT INTO rating (user_id, points, user_votes)
        VALUES (?, ?, 1)
        ON DUPLICATE KEY UPDATE points = points + ?, user_votes = user_votes + 1
    ");
    $stmt->bind_param('iii', $user_id, $rating_value, $rating_value);

    if ($stmt->execute()) {
        $rating_message = "Rating submitted successfully!";
    } else {
        $rating_message = "Error: " . $stmt->error;
    }
}

$userRoleQuery = "SELECT role FROM users WHERE id = $user_id";
$userRoleResult = mysqli_query($db, $userRoleQuery);
$userRole = mysqli_fetch_assoc($userRoleResult)['role'];


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
    <style>
        body {
            background-color: #f8f9fa; /* Light background color */
        }

        .container {
            margin-top: 50px;
        }
        .star {
            cursor: pointer;
            font-size: 2rem;
            color: #FFD700; /* Gold color */
        }
       main {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            margin-top: 50px;
        }

        section {
            display: flex; /* Enable Flexbox */
            flex-direction: column; /* Align children in a column */
            justify-content: center; /* Center vertically */
            align-items: center; /* Center horizontally */
            text-align: center; /* Center text */
        }

        #container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }

        video {
            width: 500px; /* Width of the video element */
            height: auto; /* Maintain aspect ratio */
            border-radius: 8px; /* Rounded corners */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Light shadow effect */
            margin: 0 10px; /* Space between videos */
        }

        button {
            margin-top: 20px; /* Adjust margin as needed */
            width: 200px; /* Set a fixed width for the button */
            height: 50px; /* Set a fixed height for the button */
        }

        /* Style for the End Call button */
        .end-call-btn {
            background-color: #dc3545; /* Bootstrap red color */
            color: white; /* Text color */
            border: none; /* Remove border */
            transition: background-color 0.3s ease; /* Smooth transition for background color */
        }

        .end-call-btn:hover {
            background-color: #c82333; /* Darker red on hover */
        }



        /* Placeholder for future video fields */
        .placeholder {
            width: 500px;
            height: 370px;
            background-color: #e9ecef;
            border: 2px dashed #6c757d; /* Dashed border for the placeholder */
            border-radius: 8px; /* Rounded corners */
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.2em;
            color: #6c757d;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card p-4" style="background-color: var(--light-color);">
        <h1 class="text-center" style="color: var(--primary-color);">Answer the Question</h1>
        <p><?php echo htmlspecialchars($question['content']); ?></p>

        <form method="post">
            <div class="mb-3">
                <label for="answer_content" class="form-label" style="color: var(--primary-color);">Your Answer</label>
                <textarea name="answer_content" id="answer_content" class="form-control form-field" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit Answer</button>
        </form>

        <!-- Show rating system if answer is submitted -->
        <?php if (isset($show_rating) && $show_rating): ?>
            <div id="rating-section" class="mt-4">
                <h3>Rate the Answer</h3>
                <div id="star-rating">
                    <span class="star" data-value="1">&#9734;</span>
                    <span class="star" data-value="2">&#9734;</span>
                    <span class="star" data-value="3">&#9734;</span>
                    <span class="star" data-value="4">&#9734;</span>
                    <span class="star" data-value="5">&#9734;</span>
                </div>
                <input type="hidden" id="rating-value" value="0">
                <input type="hidden" id="question-id" value="<?php echo htmlspecialchars($question_id); ?>"> <!-- Add question ID input -->
                <button id="submit-rating" class="btn btn-success mt-2">Submit Rating</button>
                <div id="rating-message" class="mt-2"><?php if (isset($rating_message)) echo $rating_message; ?></div>
            </div>
        <?php endif; ?>
    </div>
</div>
=======

       
</head>
<body>
<main>
    <section>
        <span class="fs-5"><?php echo 'Active question: ' . htmlspecialchars($question['content']); ?></span>
        <div id="container">
            <video autoplay="true" id="videoElement"></video>
            <div class="placeholder">Placeholder for Second Camera</div>
        </div>
        <?php
        if ($userRole == 'expert' || $userRole == 'admin') {
            echo '<button id="endCallToHome" class="btn end-call-btn">End Call</button>';
        } else {
            echo '<button id="endCallToRating" class="btn btn-warning">End Call and rate expert</button>';
        }
        ?>
    </section>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const stars = document.querySelectorAll('#star-rating .star');
        const ratingValue = document.getElementById('rating-value');
        const submitRatingButton = document.getElementById('submit-rating');
        const ratingMessage = document.getElementById('rating-message');

        stars.forEach(star => {
            star.addEventListener('click', function () {
                const rating = this.getAttribute('data-value');
                ratingValue.value = rating;

                stars.forEach(s => s.innerHTML = '&#9734;'); // Reset all stars

                for (let i = 0; i < rating; i++) {
                    stars[i].innerHTML = '&#9733;'; // Fill stars
                }
            });
        });

        submitRatingButton.addEventListener('click', function () {
            const rating = ratingValue.value;
            const questionId = document.getElementById('question-id').value; // Get question ID

            if (rating === '0') {
                ratingMessage.textContent = 'Please select a rating before submitting!';
                return;
            }

            const xhr = new XMLHttpRequest();
            xhr.open('POST', '', true); // Submit to the same page
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            // Send the rating and question ID
            xhr.send(`rating_value=${rating}&question_id=${questionId}`);

            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    ratingMessage.textContent = 'Rating submitted successfully!';
                }
            };
        });
    });
</script>

</body>
</html>
<script>
    let video = document.querySelector("#videoElement");

    if (navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(function (stream) {
                video.srcObject = stream;
            })
            .catch(function (error) {
                console.log("Something went wrong!");
            });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const endCallToHome = document.getElementById("endCallToHome");
        const endCallToRating = document.getElementById("endCallToRating");

        if (endCallToHome) {
            endCallToHome.addEventListener('click', function () {
                window.location.href = "index.php";
            });
        }

        if (endCallToRating) {
            endCallToRating.addEventListener('click', function () {
                window.location.href = "rating.php";  //
            });
        }
    });
</script>
