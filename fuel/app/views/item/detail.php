<section class="item-detail-section">
    <a class="back-link" href="<?php echo Uri::create('main/index/' . $item['category_id']); ?>">戻る</a>

    <p class="item-detail-category"><?php echo $item['category_name']; ?></p>
    <h1><?php echo $item['name']; ?></h1>

    <p class="item-detail-desire" aria-label="欲しい度合い <?php echo $item['desire_level']; ?>/<?php echo $desire_max; ?>">
        <?php for ($i = 1; $i <= $desire_max; $i++): ?>
            <span aria-hidden="true"><?php echo $i <= $item['desire_level'] ? '★' : '☆'; ?></span>
        <?php endfor; ?>
    </p>

    <?php if ($item['price'] !== null): ?>
        <p class="item-detail-price">¥<?php echo number_format($item['price']); ?></p>
    <?php endif; ?>

    <?php if ($item['reason'] !== null): ?>
        <h2>理由</h2>
        <p class="item-detail-reason"><?php echo $item['reason']; ?></p>
    <?php endif; ?>

    <?php if ($item['url'] !== null): ?>
        <h2>URL</h2>
        <?php if ($url_is_link): ?>
            <p><a href="<?php echo $item['url']; ?>" target="_blank" rel="noopener noreferrer"><?php echo $item['url']; ?></a></p>
        <?php else: ?>
            <p><?php echo $item['url']; ?></p>
        <?php endif; ?>
    <?php endif; ?>
</section>
