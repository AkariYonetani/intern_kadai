<section class="item-form-section">
    <h1>『<?php echo $item['name']; ?>』を編集</h1>

    <?php echo View::forge('item/_form', array(
        'input'        => $input,
        'errors'       => $errors,
        'desire_max'   => $desire_max,
        'action_url'   => Uri::create('item/edit/' . $item['id']),
        'cancel_url'   => Uri::create('main/index/' . $item['category_id']),
        'submit_label' => '保存する',
    )); ?>
</section>
