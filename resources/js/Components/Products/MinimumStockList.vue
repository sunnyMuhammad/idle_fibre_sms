<script setup>
import { usePage, Link, router } from "@inertiajs/vue3";
import { createToaster } from "@meforma/vue-toaster";
import { ref } from "vue";

const toaster = createToaster({});
const page = usePage();

const headers = [
    { text: "ID", value: "id" },
    { text: "Product Name", value: "name" },
    { text: "Category", value: "category.name" },
    { text: "Quantity", value: "unit" },
    { text: "Unit Type", value: "unit_type" },
    { text: "Parts no", value: "parts_no" },
    { text: "Rack", value: "rack_no" },
    { text: "Column", value: "column_no" },
    { text: "Row", value: "row_no" },
];

const items = ref(page.props.products.data);

const searchField = ref(["id", "name"]);
const searchItem = ref("");

if (page.props.flash.status === true) {
    toaster.success(page.props.flash.message);
} else if (page.props.flash.status === false) {
    toaster.error(page.props.flash.message);
}
</script>

<template>
    <div class="p-4 bg-[#f8f8f8] max-w-full mx-auto">
        <!-- Header -->
        <div class="mb-4">
            <h1 class="text-2xl font-bold mb-4 flex items-center gap-2">
                🚨 Minimum Stock List
            </h1>

            <div class="flex justify-between items-center mb-4">
                <input
                    type="text"
                    class="border border-gray-300 rounded-md px-4 py-2 w-[300px]"
                    v-model="searchItem"
                    placeholder="Search by name"
                />
            </div>
        </div>

        <!-- Table -->
        <div>
            <EasyDataTable
                :headers="headers"
                :items="items"
                :search-field="searchField"
                :search-value="searchItem"
                :rows-per-page="50"
                alternating
            />
        </div>
        <!-- Pagination Buttons -->
        <div class="flex justify-center gap-4 mt-6">
            <Link
                preserve-scroll
                v-if="page.props.products.prev_page_url"
                :href="page.props.products.prev_page_url"
                class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300 transition duration-300"
            >
                ⬅️ Previous
            </Link>
            <Link
                preserve-scroll
                v-if="page.props.products.next_page_url"
                :href="page.props.products.next_page_url"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition duration-300"
            >
                Next ➡️
            </Link>
        </div>
    </div>
</template>
