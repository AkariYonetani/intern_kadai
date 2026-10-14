<aside class="sidebar" data-categories="<?php echo $categories_json; ?>" data-base-url="<?php echo Uri::create('main/index/'); ?>" data-create-url="<?php echo Uri::create('category/create'); ?>">
    <nav>
        <h2>カテゴリ</h2>
        <ul class="category-list" data-bind="foreach: categories">
            <li class="category-item">
                <a data-bind="attr: { href:$root.baseUrl + id }">
                    <span class="category-name" data-bind="text:name"></span>
                    <span class="category-count" data-bind="text:item_count"></span>
                </a>
                <!-- ko if: is_default !=='1' -->
                <button type="button" class="category-menu-button" >⋯</button>
                <div class="category-menu" data-bind="visible:$root==">
                    <button type="button" class="category-edit" aria-label="カテゴリの編集">編集</button>
                </div>
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