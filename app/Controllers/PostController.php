<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Post;

class PostController extends Controller {

    public function showPosts() {
        $user = Session::get('user');
        if (!$user) {
            header('Location: /login');
            exit;
        }

        $posts = Post::getAllWithUsers();
        $this->view('posts/posts.php', ['user' => $user, 'posts' => $posts]);
    }

    public function showCreatePost() {
        $user = Session::get('user');
        if (!$user) {
            header('Location: /login');
            exit;
        }

        $this->view('posts/create.php', ['user' => $user]);
    }

    public function createPost() {
        $user = Session::get('user');
        if (!$user) {
            header('Location: /login');
            exit;
        }

        $content = trim($_POST['content'] ?? '');
        $image = null;

        if (empty($content)) {
            Session::set('error', 'Post content is required.');
            header('Location: /create-post');
            exit;
        }

        if (strlen($content) > 255) {
            Session::set('error', 'Post content must be less than 255 characters.');
            header('Location: /create-post');
            exit;
        }

        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $fileType = $_FILES['image']['type'];

            if (in_array($fileType, $allowedTypes)) {
                $uploadDir = __DIR__ . '/../../public/uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $filename = uniqid('post_') . '_' . $user['id'] . '.' . $extension;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename)) {
                    $image = $filename;
                } else {
                    Session::set('error', 'Failed to upload image.');
                    header('Location: /create-post');
                    exit;
                }
            } else {
                Session::set('error', 'Invalid image format. Only JPG, PNG, GIF allowed.');
                header('Location: /create-post');
                exit;
            }
        }

        Post::create($user['id'], $content, $image);
        Session::set('success', 'Post created successfully!');
        header('Location: /posts');
        exit;
    }

    public function toggleLike() {
        $user = Session::get('user');
        if (!$user || !isset($_POST['post_id'])) {
            exit;
        }

        Post::toggleLike((int)$_POST['post_id'], $user['id']);
        header('Location: /posts');
        exit;
    }

    public function addComment() {
        $user = Session::get('user');
        if (!$user || !isset($_POST['post_id'], $_POST['comment'])) {
            exit;
        }

        $parentId = $_POST['parent_id'] ?? null;
        Post::addComment(
            (int)$_POST['post_id'],
            $user['id'],
            trim($_POST['comment']),
            $parentId ? (int)$parentId : null
        );

        header('Location: /posts');
        exit;
    }

    public function deletePost() {
        $user = Session::get('user');
        if (!$user || !isset($_POST['post_id'])) {
            exit;
        }

        Post::delete((int)$_POST['post_id'], $user['id']);
        Session::set('success', 'Post deleted successfully!');
        header('Location: /posts');
        exit;
    }
}
