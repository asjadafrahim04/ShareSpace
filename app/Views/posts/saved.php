<?php
use App\Core\Session;
use App\Models\Post;
$title = 'Saved Posts | ShareSpace';
ob_start();
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

<!-- Navigation Links -->
<div style="display:flex; gap:12px; margin-bottom:20px; flex-wrap:wrap;">
    <a href="/posts" style="padding:8px 16px;background:#2563eb;color:white;text-decoration:none;border-radius:6px;">All Posts</a>
    <a href="/saved-posts" style="padding:8px 16px;background:#10b981;color:white;text-decoration:none;border-radius:6px;">📌 Saved Posts</a>
    <a href="/pinned-posts" style="padding:8px 16px;background:#f59e0b;color:white;text-decoration:none;border-radius:6px;">⭐ Pinned Posts</a>
    <a href="/create-post" style="padding:8px 16px;background:#7c3aed;color:white;text-decoration:none;border-radius:6px;">+ Create Post</a>
</div>

<h2>📌 Your Saved Posts</h2>

<?php if (empty($posts)): ?>
    <div class="text-center" style="padding: 40px;">
        <p style="color: #6b7280;">No saved posts yet. Save posts to see them here!</p>
    </div>
<?php else: ?>
    <p style="color: #6b7280; margin-bottom: 16px;">You have <?= count($posts) ?> saved posts</p>
    <div style="display:flex;flex-direction:column;gap:16px;">
        <?php foreach ($posts as $post): 
            $isLiked = Post::userLiked($post['id'], $user['id']);
            $isSaved = Post::userSaved($post['id'], $user['id']);
            $isPinned = Post::userPinned($post['id'], $user['id']);
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

                <!-- Like / Save / Pin / Comment Section -->
                <div class="post-actions-section" style="margin-top:8px; display:flex; flex-wrap:wrap; gap:12px; align-items:center;">
                    <!-- Like Button -->
                    <form method="POST" action="/toggle-like" style="display:inline;">
                        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                        <button type="submit" style="background:none;border:none;color:<?= $isLiked ? '#ef4444' : '#2563eb' ?>;cursor:pointer;padding:4px 8px;">
                            <?= $isLiked ? '♥' : '♡' ?> Like (<?= Post::getLikesCount($post['id']) ?>)
                        </button>
                    </form>

                    <!-- Save Button -->
                    <form method="POST" action="/toggle-save" style="display:inline;">
                        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                        <button type="submit" style="background:none;border:none;color:<?= $isSaved ? '#10b981' : '#6b7280' ?>;cursor:pointer;padding:4px 8px;">
                            <?= $isSaved ? '📌 Saved' : '📎 Save' ?>
                        </button>
                    </form>

                    <!-- Pin Button -->
                    <form method="POST" action="/toggle-pin" style="display:inline;">
                        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                        <button type="submit" style="background:none;border:none;color:<?= $isPinned ? '#f59e0b' : '#6b7280' ?>;cursor:pointer;padding:4px 8px;">
                            <?= $isPinned ? '⭐ Pinned' : '📌 Pin' ?>
                        </button>
                    </form>
                </div>

                <!-- Add Comment -->
                <form method="POST" action="/add-comment" style="margin-top:8px;">
                    <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                    <input type="text" name="comment" placeholder="Write a comment..." style="width:70%;padding:6px;border:1px solid #ddd;border-radius:4px;" required>
                    <button type="submit" style="padding:6px 12px;background:#2563eb;color:white;border:none;border-radius:4px;cursor:pointer;">Comment</button>
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
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';