const sidebarEl = document.querySelector("aside.sidebar");

const initialCategories = JSON.parse(sidebarEl.dataset.categories);
const baseUrl = sidebarEl.dataset.baseUrl;

const viewModel = {
    categories: ko.observableArray(initialCategories),
    baseUrl: baseUrl,
};
ko.applyBindings(viewModel, sidebarEl);
