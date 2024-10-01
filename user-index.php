<?php
require_once __DIR__ . '/includes/authentication.php';
require_once __DIR__ . '/includes/dbconnect.php';

/** @var mysqli $db */
$tags = fetchTags($db);  // Fetch all tags

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

$expertId = $_SESSION['user_id'];

// Fetch questions submitted by the logged-in expert
function fetchUserQuestions($db, $userId)
{
    $sql = "SELECT id, content FROM questions WHERE user_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $questions = [];
    while ($row = $result->fetch_assoc()) {
        $questions[] = $row; // Store questions submitted by the user
    }
    return $questions;
}

// Fetch questions submitted by the logged-in expert
$questions = fetchUserQuestions($db, $expertId);
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
    <h2>Your Submitted Questions:</h2>
    <?php foreach ($questions as $question): ?>
        <a href="question_details.php?question_id=<?php echo $question['id']; ?>"><?php echo htmlspecialchars($question['content']); ?></a>
    <?php endforeach; ?>
    <!-- Show All Questions Button -->
    <div class="mt-3">
<!--        <a href="all_questions.php" class="btn btn-info">Show All Questions</a>-->
    </div>
</div>
<div class="sidebarProfile">
    <div class="profile">
        <div>
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
    <div>
            <p class="warning">je moet verplicht een tag invullen</p>
        </div>
        <!-- Category Buttons -->
        <div>
            <?php
            // Loop through the tags and display them with correct category ID
            foreach ($tags as $tag) {
                echo '<button type="button" class="category-btn" data-category-id="' . $tag['id'] . '">' . htmlspecialchars($tag['tag_name']) . '</button>';
            }
            ?>
        </div>
        

        <!-- Live Session Content -->
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
