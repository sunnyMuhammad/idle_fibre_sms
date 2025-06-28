<script setup>
import RequisitionDetails from "./RequisitionDetails.vue";
import { usePage, Link, router, useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import { createToaster } from "@meforma/vue-toaster";
const toaster = createToaster({});
const page = usePage();

const headers = [
    { text: "Requisition No", value: "id" },
    { text: "Created By", value: "created_by" },
    { text: "Created Date", value: "created_at" },
    { text: "Action", value: "action" },
];
const items = ref(page.props.requisitions.data);
const searchField = ref("id");
const searchItem = ref();

const formatDate = (date) => {
    const d = new Date(date).toLocaleString();
    return d;
};

const fromDate = new URLSearchParams(window.location.search).get("fromDate");
const toDate = new URLSearchParams(window.location.search).get("toDate");
const form = useForm({
    fromDate: fromDate,
    toDate: toDate,
});

function submitForm() {
    form.get("/list-requisition");
}

const modal = ref(false);
const products = ref();

function showModal(id) {
    products.value = items.value.find((item) => item.id == id);
    modal.value = true;
}

function deleteRequisition(id) {
    if (confirm("Are you sure you want to delete this Requisition?")) {
        router.get(`/delete-requisition?requisition_id=${id}`);
    }
}

if (page.props.flash.status == true) {
    toaster.success(page.props.flash.message);
} else if (page.props.flash.status == false) {
    toaster.error(page.props.flash.message);
}
</script>

<template>
    <RequisitionDetails v-model:modal="modal" :products="products" />
    <div class="container mx-auto p-4 bg-white">
        <div class="flex">
            <div class="w-1/2">
                <h1 class="text-2xl font-bold mb-4">Requisition List</h1>
            </div>
        </div>

        <div class="flex flex-wrap mb-2 gap-2">
            <div class="w-1/6">
                <input
                    type="text"
                    v-model="searchItem"
                    placeholder="Search here"
                    class="block w-full px-2 py-1 text-sm border border-gray-300 rounded-sm focus:outline-none focus:ring-1 focus:ring-blue-600"
                />
            </div>

            <div class="w-1/6">
                <input
                    v-model="form.fromDate"
                    type="date"
                    class="block w-full px-2 py-1 text-sm border border-gray-300 rounded-sm focus:outline-none focus:ring-1 focus:ring-blue-600"
                />
            </div>

            <div class="w-1/6">
                <input
                    v-model="form.toDate"
                    type="date"
                    class="block w-full px-2 py-1 text-sm border border-gray-300 rounded-sm focus:outline-none focus:ring-1 focus:ring-blue-600"
                />
            </div>

            <div class="w-2/6 flex items-center space-x-2">
                <button
                    @click="submitForm()"
                    class="bg-green-600 hover:bg-green-700 text-white text-xs px-3 py-1 rounded-sm transition duration-300"
                >
                    Search Filter
                </button>
                <Link
                    :href="`/list-requisition`"
                    class="bg-gray-500 hover:bg-gray-600 text-white text-xs px-3 py-1 rounded-sm transition duration-300"
                >
                    Clear Search
                </Link>
            </div>
        </div>

        <div v-if="page.props.user.can['create-requisition']" class="mt-4 mb-4">
            <Link
                :href="`/requisition-save-page`"
                class="bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded transition duration-300"
            >
                Create new Requisition
            </Link>
        </div>

        <EasyDataTable
            :headers="headers"
            :items="items"
            alternating
            :rows-per-page="50"
            :search-field="searchField"
            :search-value="searchItem"
        >
            <template #item-created_at="{ created_at }">
                {{ formatDate(created_at) }}
            </template>

            <template #item-action="{ id }">
                <button
                    @click="showModal(id)"
                    class="border border-gray-700 text-gray-700 text-xs px-2 py-1 rounded hover:bg-gray-200 transition duration-300"
                    title="View"
                >
                    <span class="material-icons text-sm">visibility</span>
                </button>
                <button
                    v-if="page.props.user.can['delete-requisition']"
                    @click="deleteRequisition(id)"
                    class="bg-red-600 hover:bg-red-700 text-white text-xs px-2 py-1 rounded ml-2 transition duration-300"
                    title="Delete"
                >
                    <span class="material-icons text-sm">delete</span>
                </button>
            </template>
        </EasyDataTable>

        <!-- Pagination Buttons -->
        <div class="flex justify-center gap-4 mt-6">
            <Link
                preserve-scroll
                v-if="page.props.requisitions.prev_page_url"
                :href="page.props.requisitions.prev_page_url"
                class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300 transition duration-300"
            >
                ⬅️ Previous
            </Link>
            <Link
                preserve-scroll
                v-if="page.props.requisitions.next_page_url"
                :href="page.props.requisitions.next_page_url"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition duration-300"
            >
                Next ➡️
            </Link>
        </div>
    </div>
</template>
