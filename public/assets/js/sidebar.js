const sidebarEl = document.querySelector("aside.sidebar");

const initialCategories = JSON.parse(sidebarEl.dataset.categories);
const baseUrl = sidebarEl.dataset.baseUrl;
const createUrl = sidebarEl.dataset.createUrl;
const updateUrl = sidebarEl.dataset.updateUrl;
const deleteUrl = sidebarEl.dataset.deleteUrl;
const deleteDialog = sidebarEl.querySelector(".category-delete-dialog");

// CSRFトークン。サーバーは送信のたびに新しいトークンを返すので、ここで受け取って次の送信に使う
let csrfToken = sidebarEl.querySelector('input[name="fuel_csrf_token"]').value;

function updateCsrfToken(token) {
    csrfToken = token;
    // ログアウトなど、ページ内の通常のフォームのトークンも新しい値にそろえる
    document.querySelectorAll('input[name="fuel_csrf_token"]').forEach(function (input) {
        input.value = token;
    });
}

// サーバーから受け取ったカテゴリを画面用の形にする。名前と件数は変わるので observable にする
function toCategory(data) {
    return {
        id: String(data.id),
        name: ko.observable(data.name),
        is_default: String(data.is_default),
        item_count: ko.observable(Number(data.item_count)),
    };
}

const viewModel = {
    categories: ko.observableArray(initialCategories.map(toCategory)),
    baseUrl: baseUrl,
    newCategoryName: ko.observable(""),
    categoryError: ko.observable(""),
    isSubmitting: ko.observable(false),

    // 「⋯」メニューを開いているカテゴリのID（null なら閉じている）
    openMenuId: ko.observable(null),
    // 名前を編集中のカテゴリのID（null なら編集していない）
    editingId: ko.observable(null),
    editName: ko.observable(""),
    editError: ko.observable(""),
    isUpdating: ko.observable(false),

    // 削除ポップアップに出すカテゴリの情報
    deleteName: ko.observable(""),
    deleteCount: ko.observable(0),
    deleteAction: ko.observable(""),
    isDeleting: ko.observable(false),

    addCategory: async function (form) {
        if (viewModel.isSubmitting()) {
            return;
        }
        viewModel.isSubmitting(true);
        viewModel.categoryError("");

        try {
            const response = await fetch(createUrl, {
                method: "POST",
                body: new FormData(form),
            });
            const data = await response.json();

            if (data.token) {
                updateCsrfToken(data.token);
            }

            if (!response.ok) {
                viewModel.categoryError(data.error);
                return;
            }

            viewModel.categories.push(toCategory(data));
            viewModel.newCategoryName("");
        } catch (e) {
            viewModel.categoryError("通信に失敗しました。もう一度お試しください");
        } finally {
            viewModel.isSubmitting(false);
        }
    },

    toggleMenu: function (category) {
        if (viewModel.openMenuId() === category.id) {
            viewModel.openMenuId(null);
        } else {
            viewModel.openMenuId(category.id);
        }
    },

    startEdit: function (category) {
        viewModel.openMenuId(null);
        viewModel.editName(category.name());
        viewModel.editError("");
        viewModel.editingId(category.id);
    },

    cancelEdit: function () {
        viewModel.editingId(null);
    },

    startDelete: function (category) {
        viewModel.openMenuId(null);
        viewModel.deleteName(category.name());
        viewModel.deleteCount(category.item_count());
        viewModel.deleteAction(deleteUrl + category.id);
        viewModel.isDeleting(false);
        deleteDialog.showModal();
    },

    closeDelete: function () {
        deleteDialog.close();
    },

    // 削除フォームの送信（ボタンの連打で二重に送らないようにする）
    submitDelete: function () {
        if (viewModel.isDeleting()) {
            return false;
        }
        viewModel.isDeleting(true);
        // true を返すと、knockout が止めずに通常のフォーム送信を行う
        return true;
    },

    saveEdit: async function (form) {
        if (viewModel.isUpdating()) {
            return;
        }
        // フォームがどのカテゴリの行にあるかを knockout から取り出す
        const category = ko.dataFor(form);
        viewModel.isUpdating(true);
        viewModel.editError("");

        const body = new FormData(form);
        body.append("fuel_csrf_token", csrfToken);

        try {
            const response = await fetch(updateUrl + category.id, {
                method: "POST",
                body: body,
            });
            const data = await response.json();

            if (data.token) {
                updateCsrfToken(data.token);
            }

            if (!response.ok) {
                viewModel.editError(data.error);
                return;
            }

            category.name(data.name);
            viewModel.editingId(null);

            // 今開いているカテゴリなら、メイン画面の見出しも新しい名前にする
            const title = document.querySelector(".category-title");
            if (title && title.dataset.categoryId === category.id) {
                title.textContent = data.name;
            }
        } catch (e) {
            viewModel.editError("通信に失敗しました。もう一度お試しください");
        } finally {
            viewModel.isUpdating(false);
        }
    },
};
ko.applyBindings(viewModel, sidebarEl);

// メニューの外をクリックしたらメニューを閉じる
document.addEventListener("click", function (event) {
    if (!event.target.closest(".category-menu-button, .category-menu")) {
        viewModel.openMenuId(null);
    }
});
