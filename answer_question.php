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

</head>
<body>
    <main>
        <section>
            <span><?php echo 'Video call for: ' . htmlspecialchars($question['content']); ?></span>
            <div id="container">
                <video autoplay="true" id="videoElement"></video>
            </div>
            <?php
                if ($userRole == 'expert' || $userRole == 'admin') {
                    echo '<button id="endCallToHome">End Call</button>';
                } else {
                    echo '<button id="endCallToRating">End Call and rate expert</button>';
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
