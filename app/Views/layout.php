<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= $title ?? 'AuthBoard' ?></title>
    <link rel="stylesheet" href="/assets/style.css">
    <style>
        /* Button-style nav links */
        nav a {
            display: inline-block;
            padding: 8px 16px;
            margin-right: 8px;
            background-color: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
            transition: background 0.3s;
        }
        nav a:hover {
            background-color: #1e40af;
        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <header>
        <h1>ShareSpace</h1>
        <?php if (!empty($_SESSION['user'])): ?>
            <nav>
                <a href="/dashboard">Dashboard</a>
                <a href="/posts">Posts</a>
                <a href="/create-post">Create Post</a>
                <a href="/logout">Logout</a>
            </nav>
        <?php endif; ?>
    </header>

    <main>
        <?php echo $content; ?>
    </main>

    <footer>
        <small>ShareSpace - teaching project</small>
    </footer>
</div>
</body>
</html>
