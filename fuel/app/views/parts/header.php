<header>
    <form action="<?php echo Uri::create('auth/logout'); ?>" method="post">
        <?php echo Form::csrf(); ?>
        <button type="submit">ログアウト</button>
    </form>
</header>