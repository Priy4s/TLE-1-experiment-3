<?php
session_start(); // Start de sessie

require_once "includes/dbconnect.php";

/** @var mysqli $db */

// Controleer of de gebruiker is ingelogd
if (!isset($_SESSION['user_id'])) {
    // Als er geen gebruiker is ingelogd, omleiden naar login pagina
    header("Location: login.php");
    exit();
}

// Haal de user_id uit de sessie
$user_id = $_SESSION['user_id'];

// Haal de rol van de gebruiker op
$userRoleQuery = "SELECT role FROM users WHERE id = $user_id";
$userRoleResult = mysqli_query($db, $userRoleQuery);
$userRole = mysqli_fetch_assoc($userRoleResult)['role'];

// Controleer of de gebruiker een expert of admin is
if ($userRole !== 'expert' && $userRole !== 'admin') {
    // Redirect naar een foutpagina of terug naar login als ze niet gemachtigd zijn
    header("Location: unauthorized.php");
    exit();
}
$queryUsername = "SELECT username FROM users WHERE id = '$user_id'";

$resultUsername = mysqli_query($db, $queryUsername);

if ($resultUsername && mysqli_num_rows($resultUsername) > 0) {
    $userData = mysqli_fetch_assoc($resultUsername);
    $username = $userData['username'];
} else {
    $username = "Onbekende gebruiker";
}
// Add flair to user
if (isset($_POST['add_flair']) && !empty($_POST['flair_name'])) {
    $flair_name = mysqli_real_escape_string($db, $_POST['flair_name']);

    // Check if flair exists in the `tags` table
    $checkFlair = "SELECT id FROM tags WHERE tag_name = '$flair_name'";
    $flairResult = mysqli_query($db, $checkFlair);

    if (mysqli_num_rows($flairResult) == 0) {
        // Insert new flair if it doesn't exist
        $insertFlair = "INSERT INTO tags (tag_name) VALUES ('$flair_name')";
        mysqli_query($db, $insertFlair);
        $flair_id = mysqli_insert_id($db);
    } else {
        // Get the existing flair id
        $flair = mysqli_fetch_assoc($flairResult);
        $flair_id = $flair['id'];
    }

    // Controleer of de flair al is toegewezen aan de gebruiker
    $checkIfAssigned = "SELECT * FROM usertags WHERE user_id = $user_id AND tag_id = $flair_id";
    $assignedResult = mysqli_query($db, $checkIfAssigned);

    if (mysqli_num_rows($assignedResult) == 0) {
        // Flair is nog niet toegewezen, voeg deze toe
        $assignFlair = "INSERT INTO usertags (user_id, tag_id) VALUES ($user_id, $flair_id)";
        mysqli_query($db, $assignFlair);
    } else {
        // Flair is al toegewezen, geef eventueel een melding
        echo "Flair is al toegevoegd.";
    }

    // Redirect to avoid form resubmission on refresh
    header("Location: expert-profile.php");
    exit();
}

// Remove newest flair for user
if (isset($_POST['remove_flair'])) {
    // Find the latest flair assigned to the user by ID
    $latestFlairQuery = "SELECT usertags.tag_id FROM usertags 
                         WHERE usertags.user_id = $user_id
                         ORDER BY usertags.id DESC LIMIT 1";
    $latestFlairResult = mysqli_query($db, $latestFlairQuery);

    if (mysqli_num_rows($latestFlairResult) > 0) {
        $latestFlair = mysqli_fetch_assoc($latestFlairResult);
        $latest_flair_id = $latestFlair['tag_id'];

        // Remove the latest flair assignment
        $removeFlair = "DELETE FROM usertags WHERE user_id = $user_id AND tag_id = $latest_flair_id LIMIT 1";
        mysqli_query($db, $removeFlair);
    }

    // Redirect to avoid form resubmission on refresh
    header("Location: expert-profile.php");
    exit();
}

// Fetch flairs assigned to the user
$query = "SELECT tags.tag_name FROM usertags
          JOIN tags ON usertags.tag_id = tags.id
          WHERE usertags.user_id = $user_id";
$result = mysqli_query($db, $query);
$assigned_flairs = [];
while ($row = mysqli_fetch_assoc($result)) {
    $assigned_flairs[] = $row['tag_name'];
}

// Calculate average rating
$avgRatingQuery = "
    SELECT 
        SUM(points) / NULLIF(SUM(user_votes), 0) AS average_rating 
    FROM 
        rating 
    WHERE 
        user_id = '$user_id'
";
$avgRatingResult = mysqli_query($db, $avgRatingQuery);
$averageRating = mysqli_fetch_assoc($avgRatingResult)['average_rating'];
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Expert Profile Management</title>
    <link rel="stylesheet" href="styles/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

<main class="profileMain">
<!--    Sidebar section-->
    <section>
        <div class="sidebar">
            <h2>Your Assigned Flairs</h2>
            <?php if (!empty($assigned_flairs)) {
                foreach ($assigned_flairs as $flair) {
                    echo "<span class='topic'>$flair</span>";
                }
                // Adding the form after the foreach loop
                ?>
                <!-- Separate form for removing the newest flair -->
                <form method="post" class="removeFlairForm">
                    <button type="submit" name="remove_flair">Remove Newest Flair</button>
                </form>
                <?php
            } else {
                echo "<span class='topic'>No flairs assigned</span>";
            } ?>


        </div>
        <div class="sidebarProfile">
            <div class="profile">
                <div>
                    <span id="star-rating-display"><?php echo $averageRating !== null ? number_format($averageRating, 2) : 'No ratings available.'; ?></span>
                </div>
                <div class="username">
                    <?php echo htmlspecialchars($username). "'s profile"; ?>
                </div>
            </div>
        </div>
    </section>
<!--    Main page content-->
    <section class="mainPageContent">
        <!-- Form for adding flairs -->
        <form method="post" class="flairForm">
            <input type="text" name="flair_name" placeholder="Enter Flair Name">
            <button type="submit" name="add_flair">Add Flair</button>
        </form>

    </section>
</main>

<!--<h1>Expert Profile Management --><?php //echo htmlspecialchars($username); ?><!--</h1>-->


<!-- Display average rating -->

<script>
    // PHP variabele naar JavaScript omzetten
    const averageRating = <?php echo round($averageRating ?? 0, 1); ?>; // Rond af op 1 decimaal

    // Functie om sterren weer te geven
    function displayStars(rating) {
        const starRatingDisplay = document.getElementById('star-rating-display');
        starRatingDisplay.innerHTML = ''; // Leegmaken van de div voordat we de sterren toevoegen

        // Voeg maximaal 5 sterren toe
        for (let i = 1; i <= 5; i++) {
            const star = document.createElement('span');
            if (i <= rating) {
                star.classList.add('filled-star'); // Gevulde sterren
                star.innerHTML = '&#9733;'; // Unicode voor een gevulde ster
            } else {
                star.classList.add('empty-star'); // Lege sterren
                star.innerHTML = '&#9734;'; // Unicode voor een lege ster
            }
            starRatingDisplay.appendChild(star);
        }

        // Optioneel: Toon de numerieke waarde van de beoordeling
        document.getElementById('rating-text').innerText = `Gemiddelde rating: ${rating}/5`;
    }

    // Roep de functie aan om de sterren te tonen
    displayStars(averageRating);
</script>
</body>
</html>
