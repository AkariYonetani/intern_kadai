const sidebarEl = document.querySelector("aside.sidebar");

const initialCategories = JSON.parse(sidebarEl.dataset.categories);
const baseUrl = sidebarEl.dataset.baseUrl;

const viewModel = {
    categories: ko.observableArray(initialCategories),
    baseUrl: baseUrl,
    newCategoryName: ko.observable(""),

    addCategory: function () {
        const name = viewModel.newCategoryName().trim();
        if (name === "") {
            return;
        }
        viewModel.categories.push({
            id: "tmp",
            name: name,
            item_count: "0",
            is_default: "0",
        });
        viewModel.newCategoryName("");
    },
};
ko.applyBindings(viewModel, sidebarEl);
