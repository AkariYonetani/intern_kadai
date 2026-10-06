<aside class="sidebar">
    <nav>
        <h2>カテゴリ</h2>
        <ul class="category-list">
            <?php foreach ($categories as $category): ?>
                <li class="category-item">
                    <a href="<?php echo Uri::create('main/index/' . $category['id']); ?>">
                        <span class="category-name"><?php echo $category['name']; ?></span>
                        <span class="category-count"><?php echo $category['item_count']; ?></span>
                    </a>
                    <?php if ($category['is_default'] !== '1'): ?>
                        <button type="button" class="category-menu" aria-label="カテゴリのメニュー">⋯</button>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
    <!-- 「カテゴリを追加」はF-05で作る -->
</aside>