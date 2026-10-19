<section class="item-detail-section">
    <a class="back-link" href="<?php echo Uri::create('main/index/' . $item['category_id']); ?>"><?php echo View::forge('parts/icon_back'); ?>戻る</a>

    <div class="panel">
        <!-- 上の段：左に画像、右にカテゴリ・名前・欲しい度合い・金額（狭い画面では1列） -->
        <div class="item-detail-top">
            <?php if ($item['image_path'] !== null): ?>
                <img class="item-detail-image" src="<?php echo ItemImage::url($item['id'], $item['image_path']); ?>" alt="<?php echo $item['name']; ?>">
            <?php else: ?>
                <!-- 画像がないときも一覧のカードと同じ灰色の枠を出して、配置をそろえる -->
                <div class="item-detail-image item-image-empty" aria-hidden="true"></div>
            <?php endif; ?>

            <div class="item-detail-info">
                <p class="item-detail-category"><?php echo $item['category_name']; ?></p>
                <h1 class="item-detail-name"><?php echo $item['name']; ?></h1>
                <div class="item-detail-meta">
                    <p class="item-detail-desire" role="img" aria-label="欲しい度合い <?php echo $item['desire_level']; ?>/<?php echo $desire_max; ?>">
                        <?php for ($i = 1; $i <= $desire_max; $i++): ?>
                            <span aria-hidden="true"><?php echo $i <= $item['desire_level'] ? '★' : '☆'; ?></span>
                        <?php endfor; ?>
                    </p>
                    <?php if ($item['price'] !== null): ?>
                        <p class="item-detail-price">¥<?php echo number_format($item['price']); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- 未入力の項目は見出しごと出さない -->
        <?php if ($item['reason'] !== null): ?>
            <h2 class="item-detail-heading">理由</h2>
            <p class="item-detail-reason"><?php echo $item['reason']; ?></p>
        <?php endif; ?>

        <?php if ($item['url'] !== null): ?>
            <h2 class="item-detail-heading">URL</h2>
            <?php if ($url_is_link): ?>
                <p class="item-detail-url"><a href="<?php echo $item['url']; ?>" target="_blank" rel="noopener noreferrer"><?php echo $item['url']; ?></a></p>
            <?php else: ?>
                <p class="item-detail-url"><?php echo $item['url']; ?></p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>
