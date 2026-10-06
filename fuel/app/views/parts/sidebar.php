<aside class="sidebar" data-categories="<?php echo $categories_json; ?>">
    <nav>
        <h2>カテゴリ</h2>
        <ul class="category-list" data-bind="foreach: categories">
            <li class="category-item">
                <a>
                    <span class="category-name" data-bind="text:name"></span>
                    <span class="category-count" data-bind="text:item_count"></span>
                </a>
                <!-- ko if: is_default !=='1' -->
                <button type="button" class="category-menu" aria-label="カテゴリのメニュー">⋯</button>
                <!-- /ko -->
            </li>
        </ul>
    </nav>
    <!-- 「カテゴリを追加」はF-05で作る -->
</aside>