<section class="item-form-section">
    <h1 data-bind="text: title">『<?php echo $category['name']; ?>』に欲しいものを登録</h1>

    <?php if (isset($errors['form'])): ?>
        <p class="form-alert" role="alert"><?php echo $errors['form']; ?></p>
    <?php endif; ?>

    <form class="item-form" action="<?php echo Uri::create('item/create/' . $category['id']); ?>" method="post" novalidate
        data-initial-category-id="<?php echo $input['category_id']; ?>"
        data-initial-desire-level="<?php echo $input['desire_level']; ?>"
        data-bind="submit: submitForm">
        <?php echo Form::csrf(); ?>

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

        <a href="<?php echo Uri::create('main/index/' . $category['id']); ?>">キャンセル</a>
        <button type="submit">登録する</button>
    </form>
</section>
