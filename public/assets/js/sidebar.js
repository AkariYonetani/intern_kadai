const sidebarEl = document.querySelector("aside.sidebar");
const initialCategories = JSON.parse(sidebarEl.dataset.categories);
const viewModel = {
    categories: ko.observableArray(initialCategories),
};
ko.applyBindings(viewModel, sidebarEl);
