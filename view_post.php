<?php
include 'config.php';

if (!isset($_GET['post_id'])) {
    echo "Invalid post ID.";
    exit();
}

$post_id = $_GET['post_id'];

// Fetch the post
$query = "SELECT * FROM forum_posts WHERE post_id = ?";
$stmt = $conn->prepare($query);
if ($stmt) {
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $post = $stmt->get_result()->fetch_assoc();

    // Fetch comments
    $query = "SELECT * FROM forum_comments WHERE post_id = ? ORDER BY created_at ASC";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        $comments = $stmt->get_result();
    } else {
        echo "Error preparing query: " . $conn->error;
        exit();
    }
} else {
    echo "Error preparing query: " . $conn->error;
    exit();
}
?>

<div class="viewed-post-container">
    <h2><?php echo htmlspecialchars($post['title']); ?></h2>
    <p><?php echo htmlspecialchars($post['content']); ?></p>
    <div class="post-meta">
        <span>Posted by <?php echo htmlspecialchars($post['created_by']); ?></span>
        <span><?php echo htmlspecialchars($post['created_at']); ?></span>
    </div>

    <h3>Comments</h3>
    <div class="comments">
        <?php while ($comment = $comments->fetch_assoc()): ?>
            <div class="comment">
                <p><?php echo htmlspecialchars($comment['content']); ?></p>
                <div class="comment-meta">
                    <span>Posted by <?php echo htmlspecialchars($comment['created_by']); ?></span>
                    <span><?php echo htmlspecialchars($comment['created_at']); ?></span>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <div class="add-comment">
        <form action="add_comment.php" method="POST">
            <textarea name="content" placeholder="Add a comment..." required></textarea>
            <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
            <button type="submit">Submit</button>
        </form>
    </div>
</div>
