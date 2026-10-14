<section class="item-section">
    <h1 class="category-title" data-category-id="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></h1>
    <a class="item-add-button" href="<?php echo Uri::create('item/create/' . $category['id']); ?>">＋欲しいものを追加</a>

    <?php if (empty($items)): ?>
        <p class="empty-message">このカテゴリにはまだ欲しいものがありません</p>
    <?php else: ?>
        <ul class="item-list">
            <?php foreach ($items as $item): ?>
                <li class="item-card">
                    <p class="item-name"><?php echo $item['name'] ?></p>
                    <p class="item-desire"><?php echo $item['desire_level'] ?></p>
                    <?php if ($item['price'] !== null): ?>
                        <p class="item-price"><?php echo $item['price'] ?>円</p>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>