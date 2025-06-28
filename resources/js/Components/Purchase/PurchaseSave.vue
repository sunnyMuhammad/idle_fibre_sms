<script setup>
import { usePage, useForm, router } from "@inertiajs/vue3";
import { ref, computed, onBeforeMount } from "vue";
import axios from "axios";
import { createToaster } from "@meforma/vue-toaster";
const toaster = createToaster({});
const page = usePage();
const isLoading = ref(false);
const errors = computed(() => page.props.flash.errors || {});
const purchase_id = new URLSearchParams(window.location.search).get(
    "purchase_id"
);
const purchaseProduct = page.props.purchaseProduct;
let URL = "/create-purchase";

const form = useForm({
    product_id: "",
    product_name: "",
    unit: 0,
    vendor_id: "",
    brand_name: "",
    reqisition_no: "",
    price: 0,
    purchase_id: purchase_id,
    remarks: "",
});

if (purchase_id != 0 && purchaseProduct != null) {
    form.product_id = purchaseProduct.product_id;
    form.product_name = purchaseProduct.product.name;
    form.unit = purchaseProduct.unit;
    form.vendor_id = purchaseProduct.vendor_id;
    form.reqisition_no = purchaseProduct.reqisition_no;
    form.price = purchaseProduct.price;
    form.brand_name = purchaseProduct.brand_name;
    form.remarks = purchaseProduct.remarks;

    URL = "/update-purchase";
}

function submitForm() {
    if (form.product_name == "") {
        toaster.error("Please select a product");
        return;
    }

    form.post(URL, {
        preserveScroll: true,
        onSuccess: () => {
            if (page.props.flash.status == false) {
                toaster.error(page.props.flash.message);
            } else if (page.props.flash.status == true) {
                toaster.success(page.props.flash.message);
                router.get("/list-purchase");
            }
        },
    });
}
function selectProduct(name, id) {
    form.product_id = id;
    form.product_name = name;
}

const headers = [
    { text: "ID", value: "id" },
    { text: "Product Name", value: "name" },
    { text: "Parts no", value: "parts_no" },
    { text: "Brand Name", value: "brand_name" },
    { text: "Category", value: "category.name" },
    { text: "Quantity", value: "unit" },
    { text: "Action", value: "action" },
];

const items = ref([]);

async function getAllProduct() {
    try {
        isLoading.value = true;
        const res = await axios.get(`/product-list`);
        items.value = res.data.products;
    } catch (error) {
        console.error("Failed to load product list", error);
    } finally {
        isLoading.value = false;
    }
}

onBeforeMount(() => {
    getAllProduct();
});

const searchField = ref([
    "id",
    "name",
    "category.name",
    "brand_name",
    "parts_no",
]);
const searchItem = ref();
</script>
<template>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="p-6 max-w-2xl">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">
                Add Purchase Entry
            </h2>

            <form class="space-y-5">
                <!-- Product Name -->
                <div>
                    <label
                        for="product_name"
                        class="block text-sm font-medium text-gray-700 mb-1"
                        >Product Name</label
                    >
                    <input
                        v-model="form.product_name"
                        type="text"
                        class="w-full px-4 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400"
                        readonly
                    />
                </div>

                <!-- Vendor name -->
                <div>
                    <label
                        for="vendor"
                        class="block text-sm font-medium text-gray-700 mb-1"
                        >Vendor Name</label
                    >
                    <select
                        v-model="form.vendor_id"
                        class="w-full px-4 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400 bg-white"
                    >
                        <option>Select Vendor</option>
                        <option
                            v-for="vendor in page.props.vendors"
                            :key="vendor.id"
                            :value="vendor.id"
                        >
                            {{ vendor.name }}
                        </option>
                    </select>
                    <p v-if="errors.vendor_id" class="text-red-500">
                        {{ errors.vendor_id[0] }}
                    </p>
                </div>

                <!-- Brand Name -->
                <div>
                    <label
                        for="brand_name"
                        class="block text-sm font-medium text-gray-700 mb-1"
                        >Brand Name</label
                    >
                    <input
                        v-model="form.brand_name"
                        type="text"
                        class="w-full px-4 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400"
                    />
                </div>

                <!-- Reqisition No -->
                <div>
                    <label
                        for="reqisition_no"
                        class="block text-sm font-medium text-gray-700 mb-1"
                        >Reqisition No</label
                    >
                    <input
                        v-model="form.reqisition_no"
                        type="text"
                        class="w-full px-4 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400"
                    />
                </div>

                <!-- Price -->
                <div>
                    <label
                        for="price"
                        class="block text-sm font-medium text-gray-700 mb-1"
                        >Price</label
                    >
                    <input
                        v-model="form.price"
                        type="text"
                        class="w-full px-4 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400"
                    />
                    <p v-if="errors.price" class="text-red-500">
                        {{ errors.price[0] }}
                    </p>
                </div>

                <!-- Quantity -->
                <div>
                    <label
                        for="quantity"
                        class="block text-sm font-medium text-gray-700 mb-1"
                        >Quantity</label
                    >
                    <input
                        v-model="form.unit"
                        type="text"
                        class="w-full px-4 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400"
                    />
                    <p v-if="errors.unit" class="text-red-500">
                        {{ errors.unit[0] }}
                    </p>
                </div>

                <!-- Remarks -->
                <div>
                    <label
                        for="remark"
                        class="block text-sm font-medium text-gray-700 mb-1"
                        >Remarks</label
                    >
                    <input
                        v-model="form.remarks"
                        type="text"
                        class="w-full px-4 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400"
                    />
                </div>

                <!-- Submit Button -->
                <div class="pt-3">
                    <button
                        @click="submitForm"
                        type="button"
                        class="w-full bg-green-600 text-white py-2 rounded-xl hover:bg-green-700 transition duration-300"
                    >
                        {{
                            purchase_id == 0
                                ? "Confirm Purchase"
                                : "Update Purchase"
                        }}
                    </button>
                </div>
            </form>
        </div>
        <div class="mt-10">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">
                Select Product From Here
            </h2>
            <input
                type="text"
                class="border border-gray-300 rounded-md px-4 py-2 w-[300px] mb-2"
                v-model="searchItem"
                placeholder="Search here"
            />
            <div class="relative">
                <!-- Loadin Overlay start -->

                <div
                    v-if="isLoading"
                    class="absolute inset-0 bg-white/60 flex items-center justify-center z-10 rounded"
                >
                    <svg
                        class="animate-spin h-6 w-6 text-indigo-600"
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
                    <span class="ml-2 text-gray-700 font-medium"
                        >Loading...</span
                    >
                </div>
                <!-- Loadin Overlay end -->

                <EasyDataTable
                    :headers="headers"
                    :items="items"
                    alternating
                    :rows-per-page="10"
                    :search-field="searchField"
                    :search-value="searchItem"
                >
                    <template #item-action="{ name, id }">
                        <button
                            @click="selectProduct(name, id)"
                            class="bg-green-500 text-white font-bold py-2 px-4 rounded ml-1 hover:bg-green-600 transition duration-300"
                        >
                            Select
                        </button>
                    </template>
                </EasyDataTable>
            </div>
        </div>
    </div>
</template>
