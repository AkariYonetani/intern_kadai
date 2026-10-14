<aside class="sidebar" data-categories="<?php echo $categories_json; ?>" data-base-url="<?php echo Uri::create('main/index/'); ?>" data-create-url="<?php echo Uri::create('category/create'); ?>" data-update-url="<?php echo Uri::create('category/update/'); ?>">
    <nav>
        <h2>カテゴリ</h2>
        <ul class="category-list" data-bind="foreach: categories">
            <li class="category-item" data-bind="css: { 'is-menu-open': $root.openMenuId() === id }">
                <!-- ko ifnot: $root.editingId() === id -->
                <a data-bind="attr: { href: $root.baseUrl + id }">
                    <span class="category-name" data-bind="text: name"></span>
                    <span class="category-count" data-bind="text: item_count"></span>
                </a>
                <!-- ko if: is_default !== '1' -->
                <button type="button" class="category-menu-button" data-bind="click: $root.toggleMenu, attr: { 'aria-label': name() + 'のメニュー', 'aria-expanded': String($root.openMenuId() === id) }">⋯</button>
                <div class="category-menu" data-bind="visible: $root.openMenuId() === id">
                    <button type="button" class="category-edit" data-bind="click: $root.startEdit">編集</button>
                </div>
                <!-- /ko -->
                <!-- /ko -->
                <!-- ko if: $root.editingId() === id -->
                <form class="category-edit-form" data-bind="submit: $root.saveEdit">
                    <input type="text" name="name" aria-label="カテゴリ名" maxlength="50" data-bind="textInput: $root.editName, hasFocus: true" />
                    <p class="form-alert" role="alert" data-bind="visible: $root.editError, text: $root.editError"></p>
                    <button type="button" data-bind="click: $root.cancelEdit">キャンセル</button>
                    <button type="submit" data-bind="disable: $root.isUpdating">保存</button>
                </form>
                <!-- /ko -->
            </li>
        </ul>
    </nav>
    <form data-bind="submit: addCategory">
        <?php echo Form::csrf(); ?>
        <input type="text" name="name" aria-label="カテゴリ名" maxlength="50" data-bind="textInput: newCategoryName" />
        <p class="form-alert" role="alert" data-bind="visible: categoryError, text: categoryError"></p>
        <button type="submit" data-bind="disable: isSubmitting">追加</button>
    </form>
</aside>
