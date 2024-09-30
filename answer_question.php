<?php
require_once 'includes/dbconnect.php';
session_start(); // Start session to access user_id

// Fetch question details
$question_id = $_GET['question_id'];
$stmt = $db->prepare("SELECT content FROM questions WHERE id = ?");
$stmt->bind_param('i', $question_id);
$stmt->execute();
$question = $stmt->get_result()->fetch_assoc();

// Get the logged-in user's ID (who is acting as an expert)
$user_id = $_SESSION['user_id'];
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
            <button id="endCall">End Call</button>
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

    document.getElementById("endCall").onclick = function () {
        window.location.href = "rating.php";
    };
</script>
