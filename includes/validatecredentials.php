<?php
require_once __DIR__ . '/includes/authentication.php';
require_once 'includes/dbconnect.php';

// Function to check if a username exists in the database
function checkUsernameExists($db, $username) {
    $username = mysqli_real_escape_string($db, $username);
    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($db, $query);
    return mysqli_fetch_assoc($result) ? true : false;
}

// Function to check if an email exists in the database
function checkEmailExists($db, $email) {
    $email = mysqli_real_escape_string($db, $email);
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($db, $query);
    return mysqli_fetch_assoc($result) ? true : false;
}

// Function to check if a password is strong
function isPasswordStrong($password) {
    $hasLowerCase = preg_match('/[a-z]/', $password);
    $hasUpperCase = preg_match('/[A-Z]/', $password);
    $hasNumber = preg_match('/[0-9]/', $password);
    $hasSpecialChar = preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password);
    $isLongEnough = strlen($password) >= 8;

    return $isLongEnough && $hasLowerCase && $hasUpperCase && $hasNumber && $hasSpecialChar;
}

// Handle AJAX request for username validation
if (isset($_POST['username'])) {
    $username = $_POST['username'];
    if (checkUsernameExists($db, $username)) {
        $response = ('Gebruikersnaam is al in gebruik. Kies een andere.');
    } else {
        $response = ('Gebruikersnaam is beschikbaar.');
    }
}

// Handle AJAX request for email validation
$email = $_POST['email'] ?? '';

if (empty($email)) {
    $response = 'Voer je email in.';
} elseif (checkEmailExists($db, $email)) {
    $response = 'Email is al in gebruik. Kies een andere.';
} else {
    $response = 'Email is beschikbaar.';
}


// Handle password strength validation
if (isset($_POST['password'])) {
    $password = $_POST['password'];
    if (isPasswordStrong($password)) {

    } else {
        $response = ('Zwak wachtwoord. Gebruik minimaal 8 karakters, inclusief hoofdletters, kleine letters, nummers, en speciale tekens.');
    }
}
