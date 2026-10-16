<section class="item-form-section">
    <h1 data-bind="text: title">『<?php echo $category['name']; ?>』に欲しいものを登録</h1>

    <?php echo View::forge('item/_form', array(
        'input'             => $input,
        'errors'            => $errors,
        'desire_max'        => $desire_max,
        'image_max_size'    => $image_max_size,
        'current_image_url' => '',
        'action_url'        => Uri::create('item/create/' . $category['id']),
        'cancel_url'        => Uri::create('main/index/' . $category['id']),
        'submit_label'      => '登録する',
    )); ?>
</section>
