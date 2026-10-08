const sidebarEl = document.querySelector("aside.sidebar");

const initialCategories = JSON.parse(sidebarEl.dataset.categories);
const baseUrl = sidebarEl.dataset.baseUrl;
const createUrl = sidebarEl.dataset.createUrl;

const viewModel = {
    categories: ko.observableArray(initialCategories),
    baseUrl: baseUrl,
    newCategoryName: ko.observable(""),
    categoryError: ko.observable(""),
    isSubmitting: ko.observable(false),

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
                form.querySelector('input[name="fuel_csrf_token"]').value = data.token;
            }

            if (!response.ok) {
                viewModel.categoryError(data.error);
                return;
            }

            viewModel.categories.push({
                id: data.id,
                name: data.name,
                is_default: data.is_default,
                item_count: data.item_count,
            });
            viewModel.newCategoryName("");
        } catch (e) {
            viewModel.categoryError("通信に失敗しました。もう一度お試しください");
        } finally {
            viewModel.isSubmitting(false);
        }
    },
};
ko.applyBindings(viewModel, sidebarEl);
