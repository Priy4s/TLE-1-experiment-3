<?php
require_once __DIR__ . '/includes/authentication.php';
require_once 'includes/dbconnect.php';

// Fetch question details
$question_id = $_GET['question_id'];
$stmt = $db->prepare("SELECT content FROM questions WHERE id = ?");
$stmt->bind_param('i', $question_id);
$stmt->execute();
$question = $stmt->get_result()->fetch_assoc();

// Get the logged-in user's ID (who is acting as an expert)
$user_id = $_SESSION['user_id'];

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
