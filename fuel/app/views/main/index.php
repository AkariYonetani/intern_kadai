<section class="item-section"
    data-items="<?php echo $items_json; ?>"
    data-detail-url="<?php echo Uri::create('item/detail/'); ?>"
    data-edit-url="<?php echo Uri::create('item/edit/'); ?>"
    data-delete-url="<?php echo Uri::create('api/item/delete/'); ?>">
    <h1 class="category-title" data-category-id="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></h1>
    <label for="item-sort">並び替え</label>
    <select id="item-sort" data-bind="value: sortKey">
        <option value="default">デフォルト（登録が新しい順）</option>
        <option value="desire_desc">欲しい度合いが高い順</option>
        <option value="price_asc">金額が安い順</option>
        <option value="price_desc">金額が高い順</option>
    </select>
    <a class="item-add-button" href="<?php echo Uri::create('item/create/' . $category['id']); ?>">＋欲しいものを追加</a>

    <p class="form-alert" role="alert" data-bind="visible: listError, text: listError"></p>

    <!-- ko if: items().length === 0 -->
    <p class="empty-message">このカテゴリにはまだ欲しいものがありません</p>
    <!-- /ko -->

    <ul class="item-list" data-bind="foreach: sortedItems">
        <li class="item-card">
            <p class="item-name"><a data-bind="attr: { href: $root.detailUrl + id }, text: name"></a></p>
            <p class="item-desire" data-bind="text: desire_level"></p>
            <!-- ko if: price !== null -->
            <p class="item-price" data-bind="text: price + '円'"></p>
            <!-- /ko -->
            <a class="item-edit-button" data-bind="attr: { href: $root.editUrl + id }">編集</a>
            <button type="button" class="item-delete-button" data-bind="click: $root.deleteItem, disable: $root.isDeleting">削除</button>
        </li>
    </ul>
</section>
