<aside class="sidebar" data-categories="<?php echo $categories_json; ?>" data-base-url="<?php echo Uri::create('main/index/'); ?>">
    <nav>
        <h2>カテゴリ</h2>
        <ul class="category-list" data-bind="foreach: categories">
            <li class="category-item">
                <a data-bind="attr: { href:$root.baseUrl + id }">
                    <span class="category-name" data-bind="text:name"></span>
                    <span class="category-count" data-bind="text:item_count"></span>
                </a>
                <!-- ko if: is_default !=='1' -->
                <button type="button" class="category-menu" aria-label="カテゴリのメニュー">⋯</button>
                <!-- /ko -->
            </li>
        </ul>
    </nav>
    <form data-bind="submit: addCategory">
        <input type="text" aria-label="カテゴリ名" maxlength="50" data-bind="textInput: newCategoryName" />
        <button type="submit">追加</button>
    </form>
</aside>