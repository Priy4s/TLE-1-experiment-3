<?php
// required when working with sessions
session_start();
/** @var mysqli $db */

$login = false;
// Is user logged in?
if (isset($_SESSION['user_id'])) {
    $login = true;
    $errors = [];
}

// If data is valid
if (isset($_POST['submit'])) {
    require_once "includes/dbconnect.php";

    // Get form data
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $password = mysqli_real_escape_string($db, $_POST['password']);

    // SELECT the user from the database, based on the email address. (query)
    $query = "SELECT * FROM `users` WHERE email = '$email';";
    $result = mysqli_query($db, $query) or die('Error ' . mysqli_error($db) . ' with query ' . $query);

    // Server-side validation
    if (mysqli_num_rows($result) == 1) {
        // Get user data from result
        $user = mysqli_fetch_assoc($result);

        // Check if the provided password matches the stored password in the database
        if (password_verify($password, $user['password'])) {
            // Store the user in the session
            $_SESSION['user_id'] = $user['id'];
            $login = true;

            // Redirect to index
            header('Location: index.php');
            exit();
        } else {
            $errors['loginFailed'] = "Incorrect login credentials. Please try again.";
        }
    } else {
        $errors['loginFailed'] = "User not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="./styles/style.css">
</head>

<body>

<body class="login-page">

<div class="login-container">
    <h2 class="login-title">Login</h2>
    <form method="POST" action="">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="text" id="email" name="email" class="form-control" required>
        </div>

        <?php if (isset($errors['loginFailed'])) { ?>
            <div class="notification">
                <?= $errors['loginFailed'] ?>
            </div>
        <?php } ?>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary" name="submit">Login</button>
    </form>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
