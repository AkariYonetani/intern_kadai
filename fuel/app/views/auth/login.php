<h1 class="guest-title">ログイン</h1>

<?php if (isset($errors['form'])): ?>
    <p class="form-alert" role="alert"><?php echo $errors['form']; ?></p>
<?php endif; ?>

<form action="<?php echo Uri::create('auth/login'); ?>" method="post" novalidate>
    <?php echo Form::csrf(); ?>

    <!-- エラーのある欄は is-error で枠を赤くし、aria-invalid で読み上げソフトにも伝える -->
    <div class="field">
        <label for="email">メールアドレス</label>
        <input type="email" id="email" name="email" value="<?php echo $input['email']; ?>" maxlength="255"
            <?php if (isset($errors['email'])): ?>class="is-error" aria-invalid="true"<?php endif; ?>>
        <?php echo View::forge('parts/field_err', array('message' => $errors['email'] ?? '')); ?>
    </div>

    <div class="field">
        <label for="password">パスワード</label>
        <input type="password" id="password" name="password"
            <?php if (isset($errors['password'])): ?>class="is-error" aria-invalid="true"<?php endif; ?>>
        <?php echo View::forge('parts/field_err', array('message' => $errors['password'] ?? '')); ?>
    </div>

    <button type="submit" class="button button-primary button-block">ログイン</button>
</form>
<p class="guest-link">アカウントをお持ちでない方は <a href="<?php echo Uri::create('auth/register'); ?>">ユーザー登録</a></p>
