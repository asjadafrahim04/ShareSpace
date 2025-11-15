<?php
use App\Core\Session;
use App\Models\Post;
$title = 'Posts | ShareSpace';
ob_start();

// Helper to generate full URL for uploads
function uploadUrl($filename) {
    return '/uploads/' . $filename; // Assuming 'public' is your web root
}
?>

<?php if ($msg = Session::get('success')): ?>
    <div class="message success">
        <?= htmlspecialchars($msg) ?>
        <?php Session::remove('success'); ?>
    </div>
<?php endif; ?>

<?php if ($msg = Session::get('error')): ?>
    <div class="message error">
        <?= htmlspecialchars($msg) ?>
        <?php Session::remove('error'); ?>
    </div>
<?php endif; ?>

<!-- <div class="page-header">
    <h2>Posts</h2>
    <a href="/create-post" class="create-post-btn" style="padding:8px 16px;background:#2563eb;color:white;border-radius:6px;text-decoration:none;">
        + Create Post
    </a>
</div> -->

<?php if (empty($posts)): ?>
    <div class="text-center" style="padding: 40px;">
        <p style="color: #6b7280;">No posts yet. Be the first to create a post!</p>
    </div>
<?php else: ?>
    <div style="display:flex;flex-direction:column;gap:16px;">
        <?php foreach ($posts as $post): 
            $isLiked = Post::userLiked($post['id'], $user['id']);
            $comments = Post::getComments($post['id']);
        ?>
            <div class="post-container" style="border:1px solid #ddd;padding:12px;border-radius:6px;background:white;">
                <div class="post-header-section" style="display:flex;justify-content:space-between;align-items:center;">
                    <div class="post-user-info">
                        <strong><?= htmlspecialchars($post['user_name']) ?></strong>
                        <span style="color:#888;font-size:12px;margin-left:6px;"><?= date('M j, Y g:i A', strtotime($post['created_at'])) ?></span>
                    </div>

                    <?php if ($post['user_id'] == $user['id']): ?>
                        <div class="post-actions">
                            <form method="POST" action="/delete-post" style="display:inline;">
                                <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                <button type="submit" style="background:#ef4444;color:white;border:none;border-radius:4px;padding:2px 6px;cursor:pointer;" onclick="return confirm('Delete this post?')">
                                    Delete
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>

                <p style="margin-top:8px;"><?= nl2br(htmlspecialchars($post['content'])) ?></p>

                <?php if (!empty($post['image'])): ?>
                    <div style="margin-top:8px;">
                        <img src="http://localhost/metro_wb_lab-main/public/uploads/<?= htmlspecialchars($post['image']) ?>" 
                             alt="Post Image" 
                             style="max-width:100%;border-radius:6px;" 
                             onerror="this.style.display='none'">
                    </div>
                <?php endif; ?>  

                <!-- Like / Comment Section -->
                <div class="like-comment-section" style="margin-top:8px;">
                    <form method="POST" action="/toggle-like" style="display:inline;">
                        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                        <button type="submit" style="background:none;border:none;color:<?= $isLiked ? '#ef4444' : '#2563eb' ?>;cursor:pointer;">
                            <?= $isLiked ? '♥' : '♡' ?> Like (<?= Post::getLikesCount($post['id']) ?>)
                        </button>
                    </form>

                    <!-- Add Comment -->
                    <form method="POST" action="/add-comment" style="margin-top:6px;">
                        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                        <input type="text" name="comment" placeholder="Write a comment..." style="width:70%;padding:4px;" required>
                        <button type="submit" style="padding:4px 8px;">Comment</button>
                    </form>

                    <!-- Display Comments -->
                    <?php if (!empty($comments)): ?>
                        <div style="margin-top:8px;background:#f3f4f6;border-radius:6px;padding:8px;">
                            <?php foreach ($comments as $comment): ?>
                                <div style="margin-bottom:6px;">
                                    <strong><?= htmlspecialchars($comment['user_name']) ?>:</strong>
                                    <?= nl2br(htmlspecialchars($comment['comment'])) ?>
                                    <?php if ($comment['user_id'] == $user['id']): ?>
                                        <form method="POST" action="/delete-comment" style="display:inline;">
                                            <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                                            <button type="submit" style="background:none;border:none;color:red;cursor:pointer;" onclick="return confirm('Delete this comment?')">×</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
