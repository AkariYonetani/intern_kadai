<section class="item-section"
    data-items="<?php echo $items_json; ?>"
    data-desire-max="<?php echo $desire_max; ?>"
    data-detail-url="<?php echo Uri::create('item/detail/'); ?>"
    data-image-url="<?php echo Uri::create('api/item/image/'); ?>"
    data-edit-url="<?php echo Uri::create('item/edit/'); ?>"
    data-delete-url="<?php echo Uri::create('api/item/delete/'); ?>">
    <!-- 見出し・並び替え・追加ボタンを1行に並べる -->
    <div class="item-section-header">
        <h1 class="category-title" data-category-id="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></h1>
        <div class="item-section-tools">
            <label for="item-sort" class="item-sort-label">並び替え</label>
            <select id="item-sort" data-bind="value: sortKey">
                <option value="default">デフォルト（新しい順）</option>
                <option value="desire_desc">欲しい度合いが高い順</option>
                <option value="price_asc">金額が安い順</option>
                <option value="price_desc">金額が高い順</option>
            </select>
            <a class="button button-primary" href="<?php echo Uri::create('item/create/' . $category['id']); ?>">＋欲しいものを追加</a>
        </div>
    </div>

    <p class="form-alert" role="alert" data-bind="visible: listError, text: listError"></p>

    <!-- ko if: items().length === 0 -->
    <p class="empty-message">このカテゴリにはまだ欲しいものがありません</p>
    <!-- /ko -->

    <ul class="item-list" data-bind="foreach: sortedItems">
        <li class="item-card">
            <!-- 画像も詳細へのリンクにする。画像がないときは灰色の枠を出して、カードの形をそろえる -->
            <a class="item-image-link" tabindex="-1" aria-hidden="true" data-bind="attr: { href: $root.detailUrl + id }">
                <!-- ko if: image_path !== null -->
                <img class="item-image" alt="" loading="lazy" data-bind="attr: { src: $root.imageUrl + id + '?v=' + encodeURIComponent(image_path) }">
                <!-- /ko -->
                <!-- ko if: image_path === null -->
                <span class="item-image item-image-empty"></span>
                <!-- /ko -->
            </a>
            <p class="item-name"><a data-bind="attr: { href: $root.detailUrl + id }, text: name"></a></p>
            <div class="item-meta">
                <!-- 星は見た目用。読み上げソフトには aria-label の「欲しい度合い 3/5」を読ませる -->
                <p class="item-desire" role="img" data-bind="text: $root.stars(desire_level), attr: { 'aria-label': '欲しい度合い ' + desire_level + '/' + $root.desireMax }"></p>
                <!-- ko if: price !== null -->
                <p class="item-price" data-bind="text: $root.formatPrice(price)"></p>
                <!-- /ko -->
            </div>
            <div class="item-actions">
                <a class="button" data-bind="attr: { href: $root.editUrl + id }">編集</a>
                <button type="button" class="button button-danger" data-bind="click: $root.deleteItem, disable: $root.isDeleting">削除</button>
            </div>
        </li>
    </ul>
</section>
