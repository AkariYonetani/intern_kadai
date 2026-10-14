// 欲しいもの登録・編集フォーム（knockout と sidebar.js のあとに読み込む）
const itemFormSection = document.querySelector(".item-form-section");
const itemForm = itemFormSection.querySelector("form");
const itemNameInput = itemForm.querySelector('input[name="name"]');

const itemFormModel = {
    // サイドバーと同じ配列を使う。サイドバーで追加・名前変更したカテゴリが、選択欄にもすぐ反映される
    categories: viewModel.categories,
    categoryId: ko.observable(itemForm.dataset.initialCategoryId),
    // 入力エラーで戻ってきたときは、サーバーが入れた値から始める
    name: ko.observable(itemNameInput.value),
    desireLevel: ko.observable(itemForm.dataset.initialDesireLevel),
    isSubmitting: false,

    categoryLabel: function (category) {
        return category.name();
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
