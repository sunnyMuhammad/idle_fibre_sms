<script setup>
import PurchaseDetails from "./PurchaseDetails.vue";
import { ref } from "vue";
import { router, usePage, Link, useForm } from "@inertiajs/vue3";
import { createToaster } from "@meforma/vue-toaster";
const toaster = createToaster({});
const page = usePage();

const headers = [
    { text: "ID", value: "id" },
    { text: "Product Name", value: "product.name" },
    { text: "Reqisition no", value: "reqisition_no" },
    { text: "Vendor Name", value: "vendor.name" },
    { text: "Phone", value: "vendor.phone" },
    { text: "Address", value: "vendor.address" },
    { text: "Brand Name", value: "brand_name" },
    { text: "Price", value: "price" },
    { text: "Unit", value: "unit" },
    { text: "Unit Type", value: "product.unit_type" },
    { text: "Created Date", value: "created_at" },
    { text: "Action", value: "action" },
];

const items = ref(page.props.purchases.data);
const searchField = ref([
    "id",
    "product_name",
    "reqisition_no",
    "vendor_name",
    "brand_name",
]);
const searchItem = ref();
const modal = ref(false);

const formatDate = (date) => {
    const d = new Date(date).toLocaleString();
    return d;
};

function deletePurchase(purchase_id) {
    if (confirm("Are you sure you want to delete this purchase?")) {
        router.get(`/delete-purchase?purchase_id=${purchase_id}`);
    }
}

if(page.props.flash.status==true){
    toaster.success(page.props.flash.message);
}else if(page.props.flash.status==false){
    toaster.error(page.props.flash.message);

}

const fromDate = new URLSearchParams(window.location.search).get("fromDate");
const toDate = new URLSearchParams(window.location.search).get("toDate");
const form = useForm({
    fromDate: fromDate,
    toDate: toDate,
});

const showModal = () => {
    modal.value = true;
};
function submitForm() {
    form.get("/list-purchase");
}
</script>

<template>
    <PurchaseDetails
        :products="items"
        v-model:modal="modal"
        :fromDate="form.fromDate"
        :toDate="form.toDate"
    />
    <div class="p-4 bg-[#f8f8f8]">
        <div class="flex flex-wrap -mx-2">
            <div class="w-full md:w-1/2 px-2">
                <h1 class="text-2xl font-bold mb-4">Purchase List</h1>
            </div>
        </div>

        <div class="flex flex-wrap mb-2 -mx-2">
            <div class="w-full md:w-1/6 px-2 mb-1">
                <input
                    type="text"
                    v-model="searchItem"
                    placeholder="Search here"
                    class="block w-full text-sm py-1 px-2 border border-gray-300 rounded-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                />
            </div>

            <div class="w-full md:w-1/6 px-2 mb-1">
                <input
                    v-model="form.fromDate"
                    type="date"
                    class="block w-full text-sm py-1 px-2 border border-gray-300 rounded-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                />
            </div>

            <div class="w-full md:w-1/6 px-2 mb-1">
                <input
                    v-model="form.toDate"
                    type="date"
                    class="block w-full text-sm py-1 px-2 border border-gray-300 rounded-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                />
            </div>

            <div
                class="w-full md:w-1/3 px-2 mb-2 flex flex-wrap items-center gap-2"
            >
                <button
                    @click="submitForm()"
                    class="bg-green-600 hover:bg-green-700 text-white text-xs py-1 px-3 rounded transition duration-300"
                >
                    Search Filter
                </button>
                <Link
                    :href="`/list-purchase`"
                    class="bg-gray-500 hover:bg-gray-600 text-white text-xs py-1 px-3 rounded transition duration-300"
                >
                    Clear Search
                </Link>
                <button
                    @click="showModal()"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-xs py-1 px-3 rounded transition duration-300"
                >
                    View All
                </button>
            </div>
        </div>

        <div class="flex flex-wrap mb-4 -mx-2">
            <div v-if="page.props.user.can['create-purchase']" class="w-full px-2">
                <Link
                    :href="`/purchase-save-page?purchase_id=${0}`"
                    class="bg-green-500 text-white py-2 px-4 rounded inline-block hover:bg-green-600 transition duration-300"
                >
                    Add Purchase
                </Link>
            </div>
        </div>

        <div class="flex flex-wrap -mx-2">
            <div class="w-full px-2">
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
                        <div
                            class="flex flex-wrap gap-1 justify-center pt-1 pb-1"
                        >
                            <Link v-if="page.props.user.can['update-purchase']"
                                :href="`/purchase-save-page?purchase_id=${id}`"
                                class="bg-green-500 hover:bg-green-700 text-white py-1 px-2 rounded text-xs inline-block"
                            >
                                <span class="material-icons text-sm">edit</span>

                            </Link>
                            <button v-if="page.props.user.can['delete-purchase']"
                                @click="deletePurchase(id)"
                                class="bg-red-500 hover:bg-red-600 text-white py-1 px-2 rounded text-xs inline-block"
                            >
                                <span class="material-icons text-sm">delete</span>

                            </button>
                        </div>
                    </template>
                </EasyDataTable>
            </div>
        </div>

        <div class="flex gap-2 justify-end mt-4">
            <Link
                v-if="page.props.purchases.prev_page_url"
                :href="page.props.purchases.prev_page_url"
                class="text-blue-600 hover:underline"
            >
                Prev
            </Link>
            <Link
                v-if="page.props.purchases.next_page_url"
                :href="page.props.purchases.next_page_url"
                class="text-blue-600 hover:underline"
            >
                Next
            </Link>
        </div>
    </div>
</template>
