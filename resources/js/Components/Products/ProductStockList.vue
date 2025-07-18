<script setup>
import { usePage, useForm, Link } from "@inertiajs/vue3";
import ProductStockDetails from "./ProductStockDetails.vue";
import ProductStockDetailsReport from "./ProductStockDetailsReport.vue";
import { ref, onMounted } from "vue";
import axios from "axios";

const page = usePage();
const isLoading = ref(false);
const items = ref([]);
const modal = ref(false);
const reportModal = ref(false);

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

const searchField = ref([
    "product_name",
    "parts_no",
    "rack_no",
    "column_no",
    "row_no",
]);
const searchItem = ref("");
const category_name = ref("");
const categories = ref([]);

const form = useForm({
    fromDate: "",
    toDate: "",
    category_id: "",
});

const getAllProducts = async () => {
    isLoading.value = true;
    try {
        const res = await axios.get(`/product-stock-list`);
        items.value = res.data.productStockList.productList;
        category_name.value = res.data.productStockList.category_name;
        categories.value = res.data.productStockList.categories;
    } catch (error) {
        console.error("Failed to load product stock list", error);
    } finally {
        isLoading.value = false;
    }
};

onMounted(() => {
    getAllProducts();
});

const formatDate = (dateStr) => {
    if (!dateStr) return "-";
    const date = new Date(dateStr);
    return date.toLocaleDateString("en-GB", {
        day: "2-digit",
        month: "short",
        year: "numeric",
    });
};

const submitForm = async () => {
    isLoading.value = true;
    try {
        const res = await axios.get(
            `/product-stock-list?fromDate=${form.fromDate}&toDate=${form.toDate}&category_id=${form.category_id}`
        );
        items.value = res.data.productStockList.productList;
        category_name.value = res.data.productStockList.category_name;
        categories.value = res.data.productStockList.categories;
    } catch (error) {
        console.error("Failed to load product stock list", error);
    } finally {
        isLoading.value = false;
    }
};

const showModal = () => (modal.value = true);
const showReportModal = () => (reportModal.value = true);
</script>

<template>
    <ProductStockDetails
        v-model:modal="modal"
        :items="items"
        :fromDate="form.fromDate"
        :toDate="form.toDate"
    />
    <ProductStockDetailsReport
        v-model:reportModal="reportModal"
        :categories="categories"
    />

    <!-- Loading Overlay -->
    <div
        class="fixed inset-0 bg-gray-500 bg-opacity-50 flex items-center justify-center z-50"
        v-if="isLoading"
    >
        <div class="flex flex-col items-center space-y-2">
            <svg
                class="animate-spin h-10 w-10 text-white"
                fill="none"
                viewBox="0 0 24 24"
            >
                <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                />
                <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                />
            </svg>
            <p class="text-white text-lg font-semibold animate-pulse">
                Loading...
            </p>
        </div>
    </div>

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
                    v-for="category in categories"
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
                    :href="`/product-stock-list-page`"
                    class="bg-gray-500 hover:bg-gray-600 text-white text-xs px-3 py-1 rounded flex items-center transition duration-300"
                    >Clear Search</Link
                >
                <button
                    @click="showModal()"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1 rounded transition duration-300"
                >
                    View All
                </button>

                <button
                    v-if="page.props.user.can['product-stock-report']"
                    @click="showReportModal()"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1 rounded transition duration-300"
                >
                    Report
                </button>
            </div>
        </div>

        <div class="mb-6 space-y-1 text-sm">
            <p>
                <span class="text-violet-700 font-semibold"
                    >Searching Period:</span
                >
                {{ formatDate(form.fromDate) }} - {{ formatDate(form.toDate) }}
            </p>
            <p>
                <span class="text-violet-700 font-semibold"
                    >Selected Category:</span
                >
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
