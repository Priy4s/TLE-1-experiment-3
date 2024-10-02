<?php
require_once __DIR__ . '/includes/authentication.php';
require_once 'includes/dbconnect.php';

// Start session if not done in authentication.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user_id is set in session
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    die('User not logged in. Redirecting...');
}

// Get question content by question_id from GET parameter
$question_id = $_GET['question_id'];
$stmt = $db->prepare("SELECT content FROM questions WHERE id = ?");
$stmt->bind_param('i', $question_id);
$stmt->execute();
$question = $stmt->get_result()->fetch_assoc();

// Hardcode user_id to 15 for rating purposes
$hardcoded_user_id = 15;

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
    $stmt->bind_param('iii', $hardcoded_user_id, $rating_value, $rating_value);

    if ($stmt->execute()) {
        $rating_message = "Rating submitted successfully!";
    } else {
        $rating_message = "Error: " . $stmt->error;
    }
}

// Fetch user role
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
            height: 300px; /* Set a fixed height for consistency */
            border-radius: 8px; /* Rounded corners */
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
    </style>
</head>
<body>
<main>
    <section>
        <span class="fs-5"><?php echo 'Active question: ' . htmlspecialchars($question['content']); ?></span>
        <div id="container">
            <?php
            // Set video source based on user role
            $videoSource = ($userRole == 'expert' || $userRole == 'admin') ? 'images/user_video.mp4' : 'images/expert_explaining.mp4';
            ?>
            <video autoplay loop muted id="videoElement">
                <source src="<?php echo $videoSource; ?>" type="video/mp4">
                Your browser does not support the video tag.
            </video>
            <video autoplay="true" id="userVideoElement"></video>
        </div>
        <?php
        if ($userRole == 'expert' || $userRole == 'admin') {
            echo '<button id="endCallToHome" class="btn end-call-btn">End Call</button>';
        } else {
            echo '<button id="endCallToRating" class="btn btn-warning">End Call and rate expert</button>';
        }
        ?>
        <!-- Show rating system -->
        <div id="rating-section" class="mt-4" style="display: none;">
            <h3>Rate the Answer</h3>
            <div id="star-rating">
                <span class="star" data-value="1">&#9734;</span>
                <span class="star" data-value="2">&#9734;</span>
                <span class="star" data-value="3">&#9734;</span>
                <span class="star" data-value="4">&#9734;</span>
                <span class="star" data-value="5">&#9734;</span>
            </div>
            <input type="hidden" id="rating-value" value="0">
            <input type="hidden" id="question-id" value="<?php echo htmlspecialchars($question_id); ?>">
            <button id="submit-rating" class="btn btn-success mt-2">Submit Rating</button>
            <div id="rating-message" class="mt-2"><?php if (isset($rating_message)) echo $rating_message; ?></div>
        </div>
    </section>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const stars = document.querySelectorAll('#star-rating .star');
        const ratingValue = document.getElementById('rating-value');
        const submitRatingButton = document.getElementById('submit-rating');
        const ratingMessage = document.getElementById('rating-message');
        const ratingSection = document.getElementById('rating-section');
        const endCallToRating = document.getElementById("endCallToRating");
        const userVideoElement = document.getElementById('userVideoElement');
        const endCallToHome = document.getElementById("endCallToHome");

        // Initialize video stream
        async function startUserVideoStream() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: true });
                userVideoElement.srcObject = stream;
            } catch (error) {
                console.error("Error accessing the webcam: ", error);
            }
        }

        // Start user video stream on page load
        startUserVideoStream();

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
            const questionId = document.getElementById('question-id').value;

            if (rating === '0') {
                ratingMessage.textContent = 'Please select a rating before submitting!';
                return;
            }

            const xhr = new XMLHttpRequest();
            xhr.open('POST', '', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.send(`rating_value=${rating}&question_id=${questionId}`);

            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    ratingMessage.textContent = 'Rating submitted successfully!';

                    setTimeout(function () {
                        window.location.href = 'user-index.php';
                    }, 0);
                }
            };
        });

        if (endCallToRating) {
            endCallToRating.addEventListener('click', function () {
                ratingSection.style.display = 'block'; // Show the rating section
            });
        }

        if (endCallToHome) {
            endCallToHome.addEventListener('click', function () {
                window.location.href = "index.php";
            });
        }
    });
</script>
</body>
</html>
