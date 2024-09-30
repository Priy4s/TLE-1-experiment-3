<?php

require_once __DIR__ . '/includes/authentication.php';
require_once "includes/dbconnect.php";

/** @var mysqli $db */

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page if the user is not logged in
    header("Location: login.php");
    exit();
}

// Get the user ID from the session
$user_id = $_SESSION['user_id'];

// Get the user's role
$userRoleQuery = "SELECT role FROM users WHERE id = $user_id";
$userRoleResult = mysqli_query($db, $userRoleQuery);
$userRole = mysqli_fetch_assoc($userRoleResult)['role'];

// Redirect if the user is not an expert or admin
if ($userRole !== 'expert' && $userRole !== 'admin') {
    header("Location: unauthorized.php");
    exit();
}

// Fetch the username based on the user ID
$queryUsername = "SELECT username FROM users WHERE id = '$user_id'";
$resultUsername = mysqli_query($db, $queryUsername);

if ($resultUsername && mysqli_num_rows($resultUsername) > 0) {
    $userData = mysqli_fetch_assoc($resultUsername);
    $username = $userData['username'];
} else {
    $username = "Unknown User";
}

// Fetch all available flairs (tags) from the database
$flairsQuery = "SELECT id, tag_name FROM tags";
$flairsResult = mysqli_query($db, $flairsQuery);
$all_flairs = [];

if ($flairsResult && mysqli_num_rows($flairsResult) > 0) {
    while ($row = mysqli_fetch_assoc($flairsResult)) {
        $all_flairs[] = $row; // Store all flairs as an associative array
    }
}

// Sort the all_flairs array alphabetically by tag name
usort($all_flairs, function ($a, $b) {
    return strcmp($a['tag_name'], $b['tag_name']);
});

// Fetch flairs assigned to the user
$query = "SELECT tags.tag_name FROM usertags
          JOIN tags ON usertags.tag_id = tags.id
          WHERE usertags.user_id = $user_id";
$result = mysqli_query($db, $query);
$assigned_flairs = [];
while ($row = mysqli_fetch_assoc($result)) {
    $assigned_flairs[] = $row['tag_name'];
}

// Calculate the average rating
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

// Handle adding and removing flairs
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_flairs']) && !empty($_POST['flairs'])) {
        foreach ($_POST['flairs'] as $flairId) {
            $insertQuery = "INSERT INTO usertags (user_id, tag_id) VALUES ('$user_id', '$flairId')";
            mysqli_query($db, $insertQuery);
        }
        header("Location: expert-profile.php"); // Refresh the page to see the changes
        exit();
    }

    if (isset($_POST['remove_flair']) && !empty($assigned_flairs)) {
        $removeQuery = "DELETE FROM usertags WHERE user_id = '$user_id' AND tag_id = (SELECT id FROM tags WHERE tag_name = '{$assigned_flairs[count($assigned_flairs) - 1]}')";
        mysqli_query($db, $removeQuery);
        header("Location: expert-profile.php"); // Refresh the page to see the changes
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
    <title>Expert Profile Management</title>
    <link rel="stylesheet" href="styles/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .flair-checkboxes {
            display: flex;
            justify-content: left;
            flex-wrap: wrap; /* Allow checkboxes to wrap to the next line */
            gap: 10px; /* Spacing between checkboxes */
            margin-left: -20%; /* Shift checkboxes 20% to the left */
            padding-left: 0px; /* Add some padding to keep it visually centered */
        }

        .flair-checkboxes label {
            display: flex;
            align-items: left;
            cursor: pointer;
            margin: 0; /* Ensure no margin around labels */
            width: calc(50% - 60px); /* Each label takes half of the space with reduced width for better alignment */
            padding-left: 0px; /* Adjust this padding to position the checkbox better */
            max-width: 70%; /* Set a maximum width for the label */
            white-space: nowrap; /* Prevent text from wrapping */
            overflow: hidden; /* Hide any overflow */
            text-overflow: ellipsis; /* Show ellipsis for overflow */
        }

        .flair-checkboxes input[type="checkbox"] {
            margin-right: 0px; /* Space between checkbox and label text */
        }

        .flair-header {
            margin-bottom: 20px; /* Space below the header */
        }

        .flairForm {
            display: flex;
            flex-direction: column; /* Arrange elements in a column */
            justify-content: center; /* Align items to the center */
            align-items: center; /* Center items horizontally */
            height: 100%; /* Ensure full height for spacing */
        }

        .flairForm button {
            margin-top: 20px;
            background-color: #002855;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            width: 200px; /* Set a consistent width for the button */
        }

        .flairForm button:hover {
            background-color: #004080;
            cursor: pointer;
        }

        .mainPageContent {
            padding-left: 10px; /* Slight left padding for main content */
        }

        .sidebar {
            padding: 20px;
            background-color: #002855;
            color: white;
            width: 250px;
        }

        .topic {
            display: block;
            background-color: #004080;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<main class="profileMain">
    <section>
        <div class="sidebar">
            <h2>Your Assigned Flairs</h2>
            <?php if (!empty($assigned_flairs)) {
                foreach ($assigned_flairs as $flair) {
                    echo "<span class='topic'>$flair</span>";
                }
                ?>
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
                    <?php echo htmlspecialchars($username) . "'s profile"; ?>
                </div>
            </div>
        </div>
    </section>

    <section class="mainPageContent">
        <div class="flair-header">
            <h3>Select Flairs to Add</h3>
        </div>
        <form method="post" class="flairForm">
            <div class="flair-checkboxes">
                <?php if (!empty($all_flairs)): ?>
                    <?php foreach ($all_flairs as $flair): ?>
                        <label>
                            <input type="checkbox" name="flairs[]" value="<?php echo htmlspecialchars($flair['id']); ?>">
                            <?php echo htmlspecialchars($flair['tag_name']); ?>
                        </label>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No flairs available.</p>
                <?php endif; ?>
            </div>
            <button type="submit" name="add_flairs">Add Selected Flairs</button>
        </form>
    </section>
</main>

<script>
    const averageRating = <?php echo round($averageRating ?? 0, 1); ?>;

    function displayStars(rating) {
        const starRatingDisplay = document.getElementById('star-rating-display');
        starRatingDisplay.innerHTML = '';

        for (let i = 1; i <= 5; i++) {
            const star = document.createElement('span');
            star.className = 'star';
            star.innerHTML = i <= rating ? '&#9733;' : '&#9734;'; // Filled star or empty star
            starRatingDisplay.appendChild(star);
        }
    }

    displayStars(averageRating);
</script>
</body>
</html>
