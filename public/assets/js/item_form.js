// 欲しいもの登録・編集フォーム（knockout と sidebar.js のあとに読み込む）
const itemFormSection = document.querySelector(".item-form-section");
const itemForm = itemFormSection.querySelector("form");
const itemNameInput = itemForm.querySelector('input[name="name"]');
const imageInput = itemForm.querySelector('input[name="image"]');

const itemFormModel = {
    // サイドバーと同じ配列を使う。サイドバーで追加・名前変更したカテゴリが、選択欄にもすぐ反映される
    categories: viewModel.categories,
    categoryId: ko.observable(itemForm.dataset.initialCategoryId),
    // 入力エラーで戻ってきたときは、サーバーが入れた値から始める
    name: ko.observable(itemNameInput.value),
    desireLevel: ko.observable(itemForm.dataset.initialDesireLevel),
    isSubmitting: false,

    // 画像欄の状態：プレビューのURLが空なら「選ぶ前」、あれば「プレビュー」、imageError があれば「エラー」
    // 編集画面では今の画像から始める。サーバーで入力エラーになったときは、そのメッセージから始める
    imagePreviewUrl: ko.observable(itemForm.dataset.currentImageUrl),
    imageError: ko.observable(itemForm.dataset.imageError),

    categoryLabel: function (category) {
        return category.name();
    },

    // ファイルを選んだとき。送信前に形式とサイズを確かめて、プレビューを出す
    // ここでの確認は使いやすさのため。本当の確認はサーバー（ItemImage::validate）で必ず行う
    selectImage: function () {
        const file = imageInput.files[0];
        // 「キャンセル」で閉じたときは、選ぶ前（編集画面なら今の画像）に戻す
        if (!file) {
            itemFormModel.resetImage();
            return;
        }

        let error = "";
        if (file.type !== "image/jpeg" && file.type !== "image/png") {
            error = "画像は jpg または png 形式のファイルを選んでください";
        } else if (file.size > Number(itemForm.dataset.imageMaxSize)) {
            error = "画像は " + (itemForm.dataset.imageMaxSize / 1024 / 1024) + "MB 以内のファイルを選んでください";
        }
        if (error) {
            itemFormModel.rejectImage(error);
            return;
        }

        itemFormModel.imageError("");
        itemFormModel.revokePreview();
        // createObjectURL：選んだファイルをアップロードせずに、ブラウザの中だけで表示するためのURL（blob:...）を作る
        itemFormModel.imagePreviewUrl(URL.createObjectURL(file));
    },

    // プレビューの画像を読み込めなかったとき（名前だけ .png で、中身が画像でないファイルなど）
    // file.type は名前から決まるので selectImage では見抜けない。ブラウザが実際に表示できるかで確かめる
    previewFailed: function () {
        // 選んだファイルのときだけ。今の画像（サーバーの画像）が読めないときは、選び直しを求めない
        if (!itemFormModel.imagePreviewUrl().startsWith("blob:")) {
            return;
        }
        itemFormModel.rejectImage("画像として読み込めないファイルです。jpg または png 形式のファイルを選んでください");
    },

    // 選んだファイルを外して（送信されないようにして）、元の表示に戻し、エラーを出す
    rejectImage: function (message) {
        imageInput.value = "";
        itemFormModel.resetImage();
        itemFormModel.imageError(message);
    },

    resetImage: function () {
        itemFormModel.revokePreview();
        itemFormModel.imagePreviewUrl(itemForm.dataset.currentImageUrl);
        itemFormModel.imageError("");
    },

    // 前に作ったプレビュー用のURLを手放す（作ったままにすると、ページを閉じるまでメモリに残る）
    revokePreview: function () {
        const url = itemFormModel.imagePreviewUrl();
        if (url && url.startsWith("blob:")) {
            URL.revokeObjectURL(url);
        }
    },

    // 連打で二重に登録しないようにする
    submitForm: function () {
        if (itemFormModel.isSubmitting) {
            return false;
        }
        itemFormModel.isSubmitting = true;
        // true を返すと、knockout が止めずに通常のフォーム送信を行う
        return true;
    },
};

// アイテム名の文字数（例：12/100）。絵文字なども1文字と数え、サーバー側の mb_strlen と数え方をそろえる
itemFormModel.nameCount = ko.pureComputed(function () {
    return Array.from(itemFormModel.name()).length + "/" + itemNameInput.maxLength;
});

// 見出し（登録画面のみ）。選択欄で選んでいるカテゴリの名前を入れる。編集画面の見出しは編集前の名前で固定するので使わない
itemFormModel.title = ko.pureComputed(function () {
    const id = itemFormModel.categoryId();
    const category = itemFormModel.categories().find(function (c) {
        return c.id === id;
    });
    return "『" + (category ? category.name() : "") + "』に欲しいものを登録";
});

ko.applyBindings(itemFormModel, itemFormSection);
