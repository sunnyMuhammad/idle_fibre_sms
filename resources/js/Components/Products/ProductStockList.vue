<script setup>
import { usePage, useForm, Link } from "@inertiajs/vue3";
import ProductStockDetails from "./ProductStockDetails.vue";
import { ref } from "vue";
const page = usePage();

const headers = [
    { text: "Name", value: "product_name" },
    { text: "Category", value: "category_name" },
    { text: "Parts No", value: "parts_no" },
    { text: "Rack", value: "rack_no" },
    { text: "Column", value: "column_no" },
    { text: "Row", value: "row_no" },
    { text: "Floor Received", value: "floor_recieve" },
    { text: "Total Received", value: "total_received" },
    { text: "Total Issue", value: "total_issue" },
    { text: "Available stock", value: "available_unit" },
    { text: "Unit Type", value: "unit_type" },
];

const items = ref(page.props.productList);
const modal = ref(false);
const searchField = ref([
    "product_name",
    "parts_no",
    "rack_no",
    "column_no",
    "row_no",
]);
const searchItem = ref();

const fromDate = new URLSearchParams(window.location.search).get("fromDate");
const toDate = new URLSearchParams(window.location.search).get("toDate");
const category_id = new URLSearchParams(window.location.search).get(
    "category_id"
);
const category_name = page.props.category_name;

const form = useForm({
    fromDate: fromDate,
    toDate: toDate,
    category_id: category_id,
});

function formatDate(dateStr) {
    if (!dateStr) return "-";
    const date = new Date(dateStr);
    return date.toLocaleDateString("en-GB", {
        day: "2-digit",
        month: "short",
        year: "numeric",
    });
}

function submitForm() {
    form.get("/product-stock-list");
}

function showModal() {
    modal.value = true;
}
</script>

<template>
    <ProductStockDetails
        v-model:modal="modal"
        :items="items"
        :fromDate="form.fromDate"
        :toDate="form.toDate"
    />
    <div class="container mx-auto p-4 bg-white">
        <div class="mb-4">
            <h1 class="text-2xl font-bold">All Product Stock List</h1>
        </div>

        <div class="flex flex-wrap gap-2 mb-4">
            <input
                type="text"
                v-model="searchItem"
                placeholder="Search here"
                class="form-input text-sm px-2 py-1 border rounded w-40"
            />

            <select
                v-model="form.category_id"
                class="form-select text-sm px-2 py-1 border rounded w-40"
            >
                <option value="">Select Category</option>
                <option
                    v-for="category in page.props.categories"
                    :key="category.id"
                    :value="category.id"
                >
                    {{ category.name }}
                </option>
            </select>

            <input
                v-model="form.fromDate"
                type="date"
                class="form-input text-sm px-2 py-1 border rounded w-36"
            />

            <input
                v-model="form.toDate"
                type="date"
                class="form-input text-sm px-2 py-1 border rounded w-36"
            />

            <div class="flex space-x-2">
                <button
                    @click="submitForm()"
                    class="bg-green-600 hover:bg-green-700 text-white text-xs px-3 py-1 rounded transition duration-300"
                >
                    Search Filter
                </button>
                <Link
                    :href="`/product-stock-list`"
                    class="bg-gray-500 hover:bg-gray-600 text-white text-xs px-3 py-1 rounded flex items-center transition duration-300"
                    >Clear Search</Link
                >
                <button
                    @click="showModal()"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1 rounded transition duration-300"
                >
                    View All
                </button>
            </div>
        </div>

        <div class="mb-6 space-y-1 text-sm">
            <p>
                <span class="text-violet-700 font-semibold">Searching Period:</span>
                {{ formatDate(fromDate) }} - {{ formatDate(toDate) }}
            </p>
            <p>
                <span class="text-violet-700 font-semibold">Selected Category:</span>
                {{ category_name ? category_name : "-" }}
            </p>
        </div>

        <div>
            <EasyDataTable
                :headers="headers"
                :items="items"
                alternating
                :rows-per-page="50"
                :search-field="searchField"
                :search-value="searchItem"
            />
        </div>
    </div>
</template>
