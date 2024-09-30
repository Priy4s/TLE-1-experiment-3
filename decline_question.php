<?php
require_once __DIR__ . '/includes/authentication.php';
require_once 'includes/dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question_id = $_POST['question_id'];

    $user_id = $_SESSION['user_id'];

    $userRoleQuery = "SELECT role FROM users WHERE id = $user_id";
    $userRoleResult = mysqli_query($db, $userRoleQuery);
    $userRole = mysqli_fetch_assoc($userRoleResult)['role'];

    if (isset($_POST['cancel'])) {
        header("Location: question_details.php?question_id=$question_id");
        exit();
    }

// If "Decline" button is clicked
    if (isset($_POST['decline'])) {
        if ($userRole == 'expert' || $userRole == 'admin') {
            header("Location: index.php");
        } else {
            header("Location: user-index.php");
        }
        exit();
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
            <input type="hidden" name="question_id" value="<?php echo htmlspecialchars($_GET['question_id']); ?>">

            <button type="submit" name="decline" class="btn btn-danger">Yes, decline</button>
            <button type="submit" name="cancel" class="btn btn-secondary">Cancel</button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
