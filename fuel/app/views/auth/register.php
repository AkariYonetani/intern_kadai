<h1>ユーザー登録</h1>

<?php if (isset($errors['form'])): ?>
    <p class="error"><?php echo $errors['form']; ?></p>
<?php endif; ?>

<form action="<?php echo Uri::create('auth/register'); ?>" method="post" novalidate>
    <?php echo Form::csrf(); ?>

    <div>
        <label for="name">ユーザー名</label>
        <input type="text" id="name" name="name" value="<?php echo $input['name']; ?>" maxlength="50">
        <?php echo View::forge('parts/field_err', array('message' => $errors['name'] ?? '')); ?>
    </div>

    <div>
        <label for="email">メールアドレス</label>
        <input type="email" id="email" name="email" value="<?php echo $input['email']; ?>" maxlength="255">
        <?php echo View::forge('parts/field_err', array('message' => $errors['email'] ?? '')); ?>
    </div>

    <div>
        <label for="password">パスワード</label>
        <input type="password" id="password" name="password">
        <?php echo View::forge('parts/field_err', array('message' => $errors['password'] ?? '')); ?>
    </div>

    <button type="submit">登録する</button>
</form>

<p><a href="<?php echo Uri::create('auth/login'); ?>">ログインはこちら</a></p>