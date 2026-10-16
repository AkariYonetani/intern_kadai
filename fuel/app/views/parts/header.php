<header class="header">
    <a class="header-title" href="<?php echo Uri::create('main/index'); ?>">欲しいものリスト</a>
    <div class="header-user">
        <span class="header-user-name"><?php echo $user_name; ?></span>
        <form action="<?php echo Uri::create('auth/logout'); ?>" method="post">
            <?php echo Form::csrf(); ?>
            <button type="submit" class="button">ログアウト</button>
        </form>
    </div>
</header>
