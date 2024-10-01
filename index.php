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
require_once __DIR__ . '/includes/dbconnect.php';
// Ensure the user is logged in and has the 'expert' role
//if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'expert') {
//    http_response_code(403);
//    exit();
//}
$expertId = $_SESSION['user_id'];
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
// Fetch expert tags and associated questions
$expertTags = fetchExpertTags($db, $expertId);
$questions = fetchQuestionsForExpert($db, $expertTags);
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