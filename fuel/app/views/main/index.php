<section class="item-section">
    <h1 class="category-title" data-category-id="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></h1>
    <a class="item-add-button" href="<?php echo Uri::create('item/create/' . $category['id']); ?>">＋欲しいものを追加</a>

    <?php if (empty($items)): ?>
        <p class="empty-message">このカテゴリにはまだ欲しいものがありません</p>
    <?php else: ?>
        <ul class="item-list">
            <?php foreach ($items as $item): ?>
                <li class="item-card">
                    <p class="item-name"><a href="<?php echo Uri::create('item/detail/' . $item['id']); ?>"><?php echo $item['name'] ?></a></p>
                    <p class="item-desire"><?php echo $item['desire_level'] ?></p>
                    <?php if ($item['price'] !== null): ?>
                        <p class="item-price"><?php echo $item['price'] ?>円</p>
                    <?php endif; ?>
                    <a class="item-edit-button" href="<?php echo Uri::create('item/edit/' . $item['id']); ?>">編集</a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>