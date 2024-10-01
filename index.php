<?php
require_once __DIR__ . '/includes/authentication.php';
require_once __DIR__ . '/includes/dbconnect.php';

/** @var mysqli $db */
$tags = fetchTags($db);  // Ensure that $conn is passed correctly

// Fetch the tags
function fetchTags($db)
{
    $sql = "SELECT id, tag_name FROM tags";
    $result = $db->query($sql);

    $tags = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $tags[] = ['id' => $row['id'], 'tag_name' => $row['tag_name']]; // Return both id and name
        }
    }

    return $tags; // Return an array of tag data
}

// Fetch notifications for the logged-in user
function fetchNotifications($db, $userId)
{
    $sql = "SELECT id, question_id, notification_message 
            FROM notifications 
            WHERE user_id = ? AND status = 'pending'";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $notifications = [];
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
    return $notifications;
}

// Fetch tags associated with the logged-in expert
function fetchExpertTags($db, $expertId)
{
    $sql = "SELECT t.id, t.tag_name 
            FROM tags t 
            JOIN usertags ut ON t.id = ut.tag_id 
            WHERE ut.user_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $expertId);
    $stmt->execute();
    $result = $stmt->get_result();
    $tags = [];
    while ($row = $result->fetch_assoc()) {
        $tags[] = ['id' => $row['id'], 'tag_name' => $row['tag_name']];
    }
    return $tags;
}

// Fetch questions associated with the expert's tags
function fetchQuestionsForExpert($db, $expertTags)
{
    if (empty($expertTags)) {
        return [];
    }
    $tagIds = array_column($expertTags, 'id');
    $placeholders = implode(',', array_fill(0, count($tagIds), '?'));
    $sql = "SELECT id, content 
            FROM questions 
            WHERE tag_id IN ($placeholders)";
    $stmt = $db->prepare($sql);
    $stmt->bind_param(str_repeat('i', count($tagIds)), ...$tagIds);
    $stmt->execute();
    $result = $stmt->get_result();
    $questions = [];
    while ($row = $result->fetch_assoc()) {
        $questions[] = $row;
    }
    return $questions;
}

$expertId = $_SESSION['user_id'];
$expertTags = fetchExpertTags($db, $expertId);
$questions = fetchQuestionsForExpert($db, $expertTags);
$notifications = fetchNotifications($db, $expertId); // Fetch notifications for the current user
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="./styles/style.css" rel="stylesheet">
</head>

<body>
    <div class="sidebar">
        <h2>Questions:</h2>
        <?php foreach ($questions as $question): ?>
            <a href="question_details.php?question_id=<?php echo $question['id']; ?>"><?php echo htmlspecialchars($question['content']); ?></a>
        <?php endforeach; ?>
        
        <!-- Show All Questions Button -->
        <div class="mt-3">
            <a href="all_questions.php" class="btn btn-info">Show All Questions</a>
        </div>

        <!-- Notifications Section -->
        <div class="notifications mt-4">
            <h2>Notifications:</h2>
            <?php if (!empty($notifications)): ?>
                <?php foreach ($notifications as $notification): ?>
                    <div class="notification-item">
                        <p><?php echo htmlspecialchars($notification['notification_message']); ?></p>
                        <a href="accept_notification.php?notification_id=<?php echo $notification['id']; ?>" class="btn btn-success">Accept</a>
                        <a href="decline_notification.php?notification_id=<?php echo $notification['id']; ?>" class="btn btn-danger">Decline</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No new notifications</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="sidebarProfile">
        <div class="profile">
            <div>
                <a href="expert-profile.php" class="username">Profile</a>
                <a href="logout.php" class="username">Logout</a>
            </div>
        </div>
    </div>

    <!-- Sidebar Layout -->
    <div class="d-flex">
        <!-- Main Content -->
        <div class="content-container">
            <form method="post" action="questions.php">
                <!-- Search Section -->
                <div class="searchDiv align-items-center justify-content-between">
                    <input type="text" name="question_content" class="search-bar" placeholder="Ask a question" required> <!-- Added name for question_content -->
                    <button type="submit" class="send-btn justify-content-center align-items-center">
                        <i>➲</i>
                    </button>
                </div>

                <!-- Hidden field for category selection -->
                <input type="hidden" id="selected-category" name="category_id" value="" required>
            </form>

            <!-- Category Buttons -->
            <div>
                <?php
                // Loop through the tags and display them with correct category ID
                foreach ($tags as $tag) {
                    echo '<button type="button" class="category-btn" data-category-id="' . $tag['id'] . '">' . htmlspecialchars($tag['tag_name']) . '</button>';
                }
                ?>
            </div>
            <div class="content-box">
                <span class="live-label">• Live</span> sessions public that you can join:
                <p><strong>Math question:</strong> Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                <p><strong>Programming question:</strong> Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
            </div>

            <div class="content-box">
                <span class="live-label">• Live</span> Community discussions you can join:
                <div class="side-by-side">
                    <p><strong>Science discussion:</strong> Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                    <img src="./images/live.jpg" alt="Video thumbnail" class="img-fluid">
                </div>

            </div>
        </div>
    </div>
    
    <!-- JavaScript for handling the active category state -->
    <script>
        const categoryButtons = document.querySelectorAll('.category-btn');
        const categoryInput = document.getElementById('selected-category');

        categoryButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Remove active class from all buttons
                categoryButtons.forEach(btn => btn.classList.remove('active'));

                // Add active class to the clicked button
                this.classList.add('active');

                // Update hidden input with the selected category ID
                categoryInput.value = this.getAttribute('data-category-id');
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
