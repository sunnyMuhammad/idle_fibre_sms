<script setup>
import IssueDetails from './IssueDetails.vue';
import { usePage, useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
const page = usePage();

const headers = [
    { text: 'Id', value: 'id' },
    { text: 'Product Name', value: 'product.name' },
    { text: 'Issue Qty', value: 'unit' },
    { text: 'Unit Type', value: 'product.unit_type' },
    { text: 'Machine Name', value: 'machine_name' },
    { text: 'Issue Date', value: 'issue_date' },
];
const items = ref(page.props.issueProducts.data);

const searchField = ref(["id", "product.name"]);
const searchItem = ref();
const formatDate = (date) => {
    const d = new Date(date).toLocaleString();
    return d;
};

const modal = ref(false);
const fromDate = new URLSearchParams(window.location.search).get('fromDate');
const toDate = new URLSearchParams(window.location.search).get('toDate');
const form = useForm({
    fromDate: fromDate,
    toDate: toDate,
});
function submitForm() {
    form.get('/issue-product-list');
}
function showModal() {
    modal.value = true
}
</script>

<template>
    <IssueDetails v-model:modal="modal" :issueProduct="items" :fromDate="form.fromDate" :toDate="form.toDate"/>
    <div class="p-4 bg-white">
        <!-- Header -->
        <div>
            <h1 class="text-2xl font-bold mb-4 flex items-center">Product Issue List</h1>
        </div>

        <!-- Filters -->
        <div class="flex flex-wrap gap-2 mb-2">
            <input
                type="text"
                v-model="searchItem"
                placeholder="Search here"
                class="mb-2 px-3 py-2 border border-gray-300 rounded text-sm w-48"
            />

            <input
                type="date"
                v-model="form.fromDate"
                class="mb-2 px-3 py-2 border border-gray-300 rounded text-sm w-40"
            />

            <input
                type="date"
                v-model="form.toDate"
                class="mb-2 px-3 py-2 border border-gray-300 rounded text-sm w-40"
            />

            <div class="flex gap-2 items-center">
                <button
                    @click="submitForm()"
                    class="bg-green-600 hover:bg-green-700 text-white text-xs px-3 py-1 rounded"
                >
                    Search Filter
                </button>
                <Link
                    class="bg-gray-500 hover:bg-gray-600 text-white text-xs px-3 py-1 rounded"
                    :href="`/issue-product-list`"
                >
                    Clear Search
                </Link>
                <button
                    @click="showModal()"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1 rounded"
                >
                    View All
                </button>
            </div>
        </div>

        <!-- Data Table -->
        <EasyDataTable
            :headers="headers"
            :items="items"
            alternating
            :rows-per-page="50"
            :search-field="searchField"
            :search-value="searchItem"
        >
            <template #item-issue_date="{ created_at }">
                {{ formatDate(created_at) }}
            </template>
        </EasyDataTable>

        <!-- Pagination Links -->
        <div class="flex gap-2 justify-end mt-4">
            <Link
                v-if="page.props.issueProducts.prev_page_url"
                :href="page.props.issueProducts.prev_page_url"
                class="text-blue-600 hover:underline text-sm"
            >
                Prev
            </Link>
            <Link
                v-if="page.props.issueProducts.next_page_url"
                :href="page.props.issueProducts.next_page_url"
                class="text-blue-600 hover:underline text-sm"
            >
                Next
            </Link>
        </div>
    </div>
</template>
