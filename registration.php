<?php
$errors = [];
$success = '';

// Include necessary files
include_once 'includes/dbconnect.php';
include_once 'includes/validatecredentials.php';

// Fetch tags from the database and sort them alphabetically by tag_name
$tagsQuery = "SELECT * FROM tags ORDER BY tag_name ASC";
$tagsResult = mysqli_query($db, $tagsQuery);

$tags = [];
if ($tagsResult && mysqli_num_rows($tagsResult) > 0) {
    while ($row = mysqli_fetch_assoc($tagsResult)) {
        $tags[] = $row;
    }
}

if (isset($_POST['submit'])) {
    $firstName = mysqli_real_escape_string($db, $_POST['username']);
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $password = mysqli_real_escape_string($db, $_POST['password']);
    $selectedTags = $_POST['tags'] ?? []; // Get selected tags (if any)

    // Check if the "expert" checkbox is ticked
    $role = isset($_POST['mycheckbox']) ? 'expert' : 'user'; // Set role based on the checkbox state

    // Validate Username
    if (empty($firstName)) {
        $errors['username'] = 'Fill in your Username';
    } elseif (checkUsernameExists($db, $firstName)) {
        $errors['username'] = 'Deze gebruikersnaam is al bezet.';
    }

    // Validate Email
    if (empty($email)) {
        $errors['email'] = 'Fill in your E-Mail adress';
    } elseif (checkEmailExists($db, $email)) {
        $errors['email'] = 'Dit e-mailadres is al in gebruik.';
    }

    // Validate Password
    if (empty($password)) {
        $errors['password'] = 'Fill in your Password';
    } elseif (!isPasswordStrong($password)) {
        $errors['password'] = 'Password must be at least 8 characters, and include both lowercase and uppercase letters, symbols, and numbers';
    }

    // If no errors, insert the user into the database
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO users (username, email, password, role) VALUES ('$firstName', '$email', '$hashedPassword', '$role')";
        $result = mysqli_query($db, $query);

        if ($result) {
            $userId = mysqli_insert_id($db); // Get the newly inserted user's ID
            
            // Now insert selected tags into usertags table
            if (!empty($selectedTags)) {
                foreach ($selectedTags as $tagId) {
                    $tagId = (int) $tagId; // Sanitize tag ID
                    $insertTagQuery = "INSERT INTO usertags (user_id, tag_id) VALUES ('$userId', '$tagId')";
                    mysqli_query($db, $insertTagQuery); // Execute insert query for each tag
                }
            }

            // Redirect to index.php after successful registration
            header('Location: index.php');
            exit(); // Important: Ensure no further code is executed after redirection
        } else {
            $errors['db'] = 'Databasefout: ' . mysqli_error($db);
        }
    }

    // Close the database connection
    mysqli_close($db);
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Registration</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="styles/style.css">
    <style>
        .error { color: red; }
        .strength { font-weight: bold; margin-top: 10px; }
        .strength.weak { color: red; }
        .strength.medium { color: orange; }
        .strength.strong { color: green; }
    </style>
</head>
<body class="registration-page">
<header>
</header>
<main>
    <h1>Register Account</h1>
    <section>
        <form class="formcontainer" id="RegistrationForm" action="" method="post">

            <!-- Username Field -->
            <label for="username">Username</label>
            <input class="inputfield" type="text" id="username" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES); ?>" oninput="checkCredentials('username', this.value)">
            <p id="usernameMessage" class="error"><?php echo $errors['username'] ?? ''; ?></p>

            <!-- Email Field -->
            <label for="email">E-mail</label>
            <input class="inputfield" type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES); ?>" oninput="checkCredentials('email', this.value)">
            <p id="emailMessage" class="error"><?php echo $errors['email'] ?? ''; ?></p>

            <!-- Password Field -->
            <label for="password">Password</label>
            <input class="inputfield" type="password" id="password" name="password" oninput="checkPasswordStrength(this.value)">
            <p id="passwordMessage" class="strength"><?php echo $errors['password'] ?? ''; ?></p>

            Ben jij een expert?
            <input type="checkbox" name="mycheckbox" id="mycheckbox" value="1" /><br>
            <div id="mycheckboxdiv" style="display:none">
                <?php if (!empty($tags)): ?>
                <?php 
                $counter = 0; // Initialize a counter
                foreach ($tags as $tag): ?>
                    <input type="checkbox" name="tags[]" value="<?php echo htmlspecialchars($tag['id']); ?>">
                    <?php echo htmlspecialchars($tag['tag_name']); ?>
                <?php
                $counter++; // Increment the counter
                if ($counter % 5 == 0) {
                    echo "<br>"; // Insert a line break after every 5 checkboxes
                }
                ?>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Geen tags beschikbaar.</p>
            <?php endif; ?>
            </div>

            <script type="text/javascript">
                $('#mycheckbox').change(function() {
                    $('#mycheckboxdiv').toggle(this.checked);
                });
            </script>

            <input class="submitButton" type="submit" name="submit" value="Submit">

            <span>Heb je al een account? <a href="login.php">Log</a> nu in of ga naar de <a href="index.php">home</a> pagina</span>
        </form>
    </section>
</main>
<script>
    // Check username and email via AJAX
    function checkCredentials(field, value) {
        const messageElement = document.getElementById(field + 'Message');
        if (value === '') {
            messageElement.textContent = '';
            return;
        }

        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'includes/validate_credentials.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onload = function () {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.status === 'success') {
                    messageElement.textContent = response.message;
                    messageElement.style.color = 'green';
                } else {
                    messageElement.textContent = response.message;
                    messageElement.style.color = 'red';
                }
            }
        };

        xhr.send(`${field}=${encodeURIComponent(value)}`);
    }

    // Password strength checker
    function checkPasswordStrength(password) {
    const passwordMessage = document.getElementById('passwordMessage');
    const hasLowerCase = /[a-z]/.test(password);
    const hasUpperCase = /[A-Z]/.test(password);
    const hasNumber = /[0-9]/.test(password);
    const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);
    const isLongEnough = password.length >= 8;

    if (isLongEnough && hasLowerCase && hasUpperCase && hasNumber && hasSpecialChar) {
        passwordMessage.textContent = 'Strong Password';
        passwordMessage.className = 'strength strong';
    } else if (isLongEnough && ((hasLowerCase && hasUpperCase) || (hasLowerCase && hasNumber) || (hasUpperCase && hasNumber))) {
        passwordMessage.textContent = 'Medium Password';
        passwordMessage.className = 'strength medium';
    } else {
        passwordMessage.textContent = 'Weak Password. Password must be at least 8 characters, and include both lowercase and uppercase letters, symbols, and numbers';
        passwordMessage.className = 'strength weak';
    }
}

</script>
</body>
</html>
