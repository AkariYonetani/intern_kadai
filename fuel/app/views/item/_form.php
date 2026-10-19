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

    <!-- 上の段：左に画像、右にアイテム名・カテゴリ・欲しい度合い・金額（狭い画面では1列） -->
    <div class="item-form-grid">
        <div class="item-form-image">
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
                <svg class="image-picker-icon" viewBox="0 0 48 48" aria-hidden="true">
                    <rect x="4" y="6" width="40" height="36" rx="6" fill="none" stroke="currentColor" stroke-width="2.5" />
                    <circle cx="16" cy="17" r="4" fill="none" stroke="currentColor" stroke-width="2.5" />
                    <path d="M6 38 L20 24 L32 36 M28 32 L34 26 L42 34" fill="none" stroke="currentColor" stroke-width="2.5" />
                </svg>
                <span class="image-picker-hint">画像を選ぶ</span>
                <span class="image-picker-hint">jpg/png <?php echo $image_max_size / 1024 / 1024; ?>MBまで</span>
                <!-- /ko -->
            </label>
            <p class="error" data-bind="visible: imageError, text: imageError"></p>
        </div>

        <div class="item-form-main">
            <div class="field">
                <div class="field-label-row">
                    <label for="item-name">アイテム名</label>
                    <span class="char-count" data-bind="text: nameCount"></span>
                </div>
                <input type="text" id="item-name" name="name" maxlength="<?php echo $limits['name_max']; ?>" value="<?php echo $input['name']; ?>" data-bind="textInput: name">
                <?php echo View::forge('parts/field_err', array('message' => $errors['name'] ?? '')); ?>
            </div>

            <div class="field">
                <label for="item-category">カテゴリ</label>
                <select id="item-category" name="category_id" data-bind="options: categories, optionsText: categoryLabel, optionsValue: 'id', value: categoryId"></select>
                <?php echo View::forge('parts/field_err', array('message' => $errors['category_id'] ?? '')); ?>
            </div>

            <div class="item-form-row">
                <!-- 星そのものを押す形。ラジオボタンは見えなくするが、キーボード操作と読み上げでは使える -->
                <fieldset class="star-input">
                    <legend>欲しい度合い</legend>
                    <div class="star-input-box">
                        <?php for ($i = 1; $i <= $desire_max; $i++): ?>
                            <label class="star-input-item">
                                <input type="radio" class="visually-hidden" name="desire_level" value="<?php echo $i; ?>" aria-label="<?php echo $i; ?>" data-bind="checked: desireLevel">
                                <span aria-hidden="true" data-bind="text: Number(desireLevel()) >= <?php echo $i; ?> ? '★' : '☆'">☆</span>
                            </label>
                        <?php endfor; ?>
                    </div>
                    <?php echo View::forge('parts/field_err', array('message' => $errors['desire_level'] ?? '')); ?>
                </fieldset>

                <div class="field">
                    <label for="item-price">金額</label>
                    <input type="text" id="item-price" name="price" inputmode="numeric" value="<?php echo $input['price']; ?>">
                    <?php echo View::forge('parts/field_err', array('message' => $errors['price'] ?? '')); ?>
                </div>
            </div>
        </div>
    </div>

    <div class="field">
        <label for="item-reason">理由</label>
        <textarea id="item-reason" name="reason" maxlength="<?php echo $limits['reason_max']; ?>" rows="6"><?php echo $input['reason']; ?></textarea>
        <?php echo View::forge('parts/field_err', array('message' => $errors['reason'] ?? '')); ?>
    </div>

    <div class="field">
        <label for="item-url">URL</label>
        <input type="url" id="item-url" name="url" maxlength="<?php echo $limits['url_max']; ?>" value="<?php echo $input['url']; ?>">
        <?php echo View::forge('parts/field_err', array('message' => $errors['url'] ?? '')); ?>
    </div>

    <!-- ボタンの並びは「キャンセル」左・主操作右 -->
    <div class="form-actions">
        <a class="button" href="<?php echo $cancel_url; ?>">キャンセル</a>
        <button type="submit" class="button button-primary"><?php echo $submit_label; ?></button>
    </div>
</form>
