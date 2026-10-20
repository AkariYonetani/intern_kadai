<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $title; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Zen+Kaku+Gothic+New:wght@400;500;700&display=swap" rel="stylesheet">
    <?php echo Asset::css('style.css'); ?>
</head>

<body class="guest">
    <!-- ログイン前の画面は、中身を画面中央の白いカードに入れる -->
    <main class="guest-card">
        <?php echo $content; ?>
    </main>
</body>

</html>
