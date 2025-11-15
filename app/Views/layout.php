<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= $title ?? 'AuthBoard ' ?></title>
    <link rel="stylesheet" href="/assets/style.css">
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
        <?php echo $content ; ?>
    </main>

    <footer>
        <small>ShareSpace - teaching project</small>
    </footer>
</div>
</body>
</html>