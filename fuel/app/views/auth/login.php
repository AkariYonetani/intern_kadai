<h1>ログイン</h1>

<?php if (isset($errors['form'])): ?>
    <p class="form-alert" role="alert"><?php echo $errors['form']; ?></p>
<?php endif; ?>

<form action="<?php echo Uri::create('auth/login'); ?>" method="post" novalidate>
    <?php echo Form::csrf(); ?>

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

    <button type="submit">ログイン</button>
</form>
<p>アカウントをお持ちでない方は<a href="<?php echo Uri::create('auth/register'); ?>">ユーザー登録</a></p>