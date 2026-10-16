// メイン画面の欲しいもの一覧（knockout と sidebar.js のあとに読み込む）
const itemSection = document.querySelector(".item-section");

const itemListModel = {
    items: ko.observableArray(JSON.parse(itemSection.dataset.items)),
    detailUrl: itemSection.dataset.detailUrl,
    // 画像は api/item/image/{ID} から本人確認を通して受け取る
    // 一覧では ?v=ファイル名 を付ける（差し替えるとURLが変わり、ブラウザのキャッシュの古い画像が出ないようにする）
    imageUrl: itemSection.dataset.imageUrl,
    editUrl: itemSection.dataset.editUrl,
    listError: ko.observable(""),
    // 削除の通信中は、すべての「削除」ボタンを押せなくする（連打対策）
    isDeleting: ko.observable(false),

    deleteItem: async function (item) {
        if (itemListModel.isDeleting()) {
            return;
        }
        if (!confirm("『" + item.name + "』を削除します。この操作は元に戻せません。")) {
            return;
        }
        itemListModel.isDeleting(true);
        itemListModel.listError("");

        // トークンは sidebar.js で一元管理しているものを使う
        const body = new FormData();
        body.append("fuel_csrf_token", csrfToken);

        try {
            const response = await fetch(itemSection.dataset.deleteUrl + item.id, {
                method: "POST",
                body: body,
            });
            const data = await response.json();

            if (data.token) {
                updateCsrfToken(data.token);
            }

            if (!response.ok) {
                itemListModel.listError(data.error);
                return;
            }

            // 一覧から消す。最後の1件なら「まだ欲しいものがありません」が表示される
            itemListModel.items.remove(item);

            // サイドバーの件数を1減らす
            const category = viewModel.categories().find(function (c) {
                return c.id === String(data.category_id);
            });
            if (category) {
                category.item_count(category.item_count() - 1);
            }
        } catch (e) {
            itemListModel.listError("通信に失敗しました。もう一度お試しください");
        } finally {
            itemListModel.isDeleting(false);
        }
    },
};

// 選んでいる並び順（リロードするとデフォルトに戻る）
itemListModel.sortKey = ko.observable("default");

// 並び替えた一覧。items（サーバーから来た、登録が新しい順）はそのまま残し、並び替えた写しを表示する
itemListModel.sortedItems = ko.pureComputed(function () {
    const items = itemListModel.items();
    const key = itemListModel.sortKey();
    if (key === "default") {
        return items;
    }

    // 同じ値のときは元の順番（登録が新しい順）を保つため、元の位置を覚えておく
    const position = new Map();
    items.forEach(function (item, index) {
        position.set(item, index);
    });

    return items.slice().sort(function (a, b) {
        let diff = 0;
        if (key === "desire_desc") {
            diff = Number(b.desire_level) - Number(a.desire_level);
        } else {
            // 金額が未入力のものは、安い順でも高い順でも末尾にする
            if (a.price === null && b.price !== null) {
                return 1;
            }
            if (a.price !== null && b.price === null) {
                return -1;
            }
            if (a.price !== null && b.price !== null) {
                // JSONの数値は文字列で届くので、数にしてから比べる
                diff = key === "price_asc"
                    ? Number(a.price) - Number(b.price)
                    : Number(b.price) - Number(a.price);
            }
        }
        return diff !== 0 ? diff : position.get(a) - position.get(b);
    });
});

ko.applyBindings(itemListModel, itemSection);
