<?php if (isset($errors['form'])): ?>
    <p class="form-alert" role="alert"><?php echo $errors['form']; ?></p>
<?php endif; ?>

<!-- 画像を送るため enctype="multipart/form-data"（文字の項目とファイルを、区切って送る形式） -->
<form class="item-form" action="<?php echo $action_url; ?>" method="post" enctype="multipart/form-data" novalidate
    data-initial-category-id="<?php echo $input['category_id']; ?>"
    data-initial-desire-level="<?php echo $input['desire_level']; ?>"
    data-current-image-url="<?php echo $current_image_url; ?>"
    data-image-error="<?php echo $errors['image'] ?? ''; ?>"
    data-image-max-size="<?php echo $image_max_size; ?>"
    data-bind="submit: submitForm">
    <?php echo Form::csrf(); ?>

    <div>
        <span class="form-label" id="item-image-label">画像</span>
        <!-- input を label の前に置くのは、キーボードで input にフォーカスしたとき CSS（+ セレクタ）で枠に印を付けるため -->
        <input type="file" id="item-image" class="visually-hidden" name="image" accept=".jpg,.jpeg,.png,image/jpeg,image/png"
            aria-labelledby="item-image-label" data-bind="event: { change: selectImage }">
        <!-- 点線の枠全体を label にして、見えない input type="file" とつなぐ。枠のどこを押してもファイルを選べる -->
        <label class="image-picker" for="item-image" data-bind="css: { 'is-error': imageError }">
            <!-- ko if: imagePreviewUrl -->
            <img class="image-preview" alt="選んだ画像" data-bind="attr: { src: imagePreviewUrl }, event: { error: previewFailed }">
            <span class="image-picker-hint">クリックして画像を変える</span>
            <!-- /ko -->
            <!-- ko ifnot: imagePreviewUrl -->
            <span class="image-picker-hint">クリックして画像を選ぶ（jpg・png、<?php echo $image_max_size / 1024 / 1024; ?>MBまで）</span>
            <!-- /ko -->
        </label>
        <p class="error" data-bind="visible: imageError, text: imageError"></p>
    </div>

    <div>
        <label for="item-name">アイテム名</label>
        <span class="char-count" data-bind="text: nameCount"></span>
        <input type="text" id="item-name" name="name" maxlength="100" value="<?php echo $input['name']; ?>" data-bind="textInput: name">
        <?php echo View::forge('parts/field_err', array('message' => $errors['name'] ?? '')); ?>
    </div>

    <div>
        <label for="item-category">カテゴリ</label>
        <select id="item-category" name="category_id" data-bind="options: categories, optionsText: categoryLabel, optionsValue: 'id', value: categoryId"></select>
        <?php echo View::forge('parts/field_err', array('message' => $errors['category_id'] ?? '')); ?>
    </div>

    <fieldset>
        <legend>欲しい度合い</legend>
        <?php for ($i = 1; $i <= $desire_max; $i++): ?>
            <label>
                <input type="radio" name="desire_level" value="<?php echo $i; ?>" aria-label="<?php echo $i; ?>" data-bind="checked: desireLevel">
                <span aria-hidden="true" data-bind="text: Number(desireLevel()) >= <?php echo $i; ?> ? '★' : '☆'">☆</span>
            </label>
        <?php endfor; ?>
        <?php echo View::forge('parts/field_err', array('message' => $errors['desire_level'] ?? '')); ?>
    </fieldset>

    <div>
        <label for="item-price">金額</label>
        <input type="text" id="item-price" name="price" inputmode="numeric" value="<?php echo $input['price']; ?>">
        <?php echo View::forge('parts/field_err', array('message' => $errors['price'] ?? '')); ?>
    </div>

    <div>
        <label for="item-reason">理由</label>
        <textarea id="item-reason" name="reason" maxlength="500" rows="5"><?php echo $input['reason']; ?></textarea>
        <?php echo View::forge('parts/field_err', array('message' => $errors['reason'] ?? '')); ?>
    </div>

    <div>
        <label for="item-url">URL</label>
        <input type="url" id="item-url" name="url" maxlength="2048" value="<?php echo $input['url']; ?>">
        <?php echo View::forge('parts/field_err', array('message' => $errors['url'] ?? '')); ?>
    </div>

    <a href="<?php echo $cancel_url; ?>">キャンセル</a>
    <button type="submit"><?php echo $submit_label; ?></button>
</form>
