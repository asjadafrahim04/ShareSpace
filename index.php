<?php
declare(strict_types=1);

// autoload
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/app/helpers.php';

// tiny .env loader (reads .env into getenv and $_ENV)
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        [$key, $val] = array_map('trim', explode('=', $line, 2) + [1=>null]);
        if ($key && $val !== null) {
            putenv("$key=$val");
            $_ENV[$key] = $val;
        }
    }
}

use App\Core\Router;
use App\Core\Session;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\PostController;

Session::start();

$router = new Router();
$auth = new AuthController();
$dash = new DashboardController();
$postController = new PostController();

// Authentication routes
$router->get('/', fn() => $auth->showLogin());
$router->get('/login', fn() => $auth->showLogin());
$router->get('/register', fn() => $auth->showRegister());
$router->post('/register', fn() => $auth->register());
$router->post('/login', fn() => $auth->login());
$router->get('/logout', fn() => $auth->logout());

// Dashboard routes
$router->get('/dashboard', fn() => $dash->index());
$router->get('/test-mail', fn() => $dash->testMail());

// Post routes
$router->get('/posts', fn() => $postController->showPosts());
$router->get('/create-post', fn() => $postController->showCreatePost());
$router->post('/create-post', fn() => $postController->createPost());

// Post interaction routes
$router->post('/toggle-like', fn() => $postController->toggleLike());
$router->post('/add-comment', fn() => $postController->addComment());
$router->post('/delete-post', fn() => $postController->deletePost());

// Save post routes
$router->post('/toggle-save', fn() => $postController->toggleSave());
$router->get('/saved-posts', fn() => $postController->showSavedPosts());

// Pin post routes  
$router->post('/toggle-pin', fn() => $postController->togglePin());
$router->get('/pinned-posts', fn() => $postController->showPinnedPosts());

$router->dispatch($_SERVER['REQUEST_URI'] ?? '/', $_SERVER['REQUEST_METHOD'] ?? 'GET');