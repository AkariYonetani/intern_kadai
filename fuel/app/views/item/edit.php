<section class="item-form-section">
    <a class="back-link" href="<?php echo Uri::create('main/index/' . $item['category_id']); ?>"><?php echo View::forge('parts/icon_back'); ?>戻る</a>

    <div class="panel">
        <h1 class="panel-title">『<?php echo $item['name']; ?>』を編集</h1>

        <?php echo View::forge('item/_form', array(
            'input'             => $input,
            'errors'            => $errors,
            'desire_max'        => $desire_max,
            'image_max_size'    => $image_max_size,
            'limits'            => $limits,
            // 今の画像をプレビューの初期値にする（画像がなければ空）
            'current_image_url' => $item['image_path'] === null ? '' : ItemImage::url($item['id'], $item['image_path']),
            'action_url'        => Uri::create('item/edit/' . $item['id']),
            'cancel_url'        => Uri::create('main/index/' . $item['category_id']),
            'submit_label'      => '保存する',
        )); ?>
    </div>
</section>
