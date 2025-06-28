<template>
    <div>
        <!-- Modal Overlay start -->
        <div
            v-if="modal"
            class="fixed inset-0 bg-black/15 bg-opacity-50 flex items-center justify-center z-50"
        >
            <!-- Modal Box -->
            <div
                class="bg-white w-full max-w-md rounded-lg shadow-lg p-6 relative"
            >
                <label for="availabe_qty">Available Qty</label>
                <input
                    v-model="product.unit"
                    class="border border-gray-300 rounded-md px-4 py-2 w-full"
                    type="text"
                    readonly
                />
                <label for="product_name">Product Name</label>
                <input
                    v-model="product.name"
                    class="border border-gray-300 rounded-md px-4 py-2 w-full"
                    type="text"
                    readonly
                />
                <label for="requistion_qty">Requisition Qty</label>
                <input
                    v-model="product.requisition_qty"
                    class="border border-gray-300 rounded-md px-4 py-2 w-full"
                    type="text"
                />
                <label for="qty_type">Qty Type</label>
                <input
                    readonly
                    v-model="product.unit_type"
                    class="border border-gray-300 rounded-md px-4 py-2 w-full"
                    type="text"
                />

                <label for="remarks">Remarks</label>
                <input
                    v-model="product.remarks"
                    class="border border-gray-300 rounded-md px-4 py-2 w-full"
                    type="text"
                />
                <label for="remarks">Size</label>
                <input
                    v-model="product.size"
                    class="border border-gray-300 rounded-md px-4 py-2 w-full"
                    type="text"
                />

                <label for="store_code">Store Code</label>
                <input
                    v-model="product.store_code"
                    class="border border-gray-300 rounded-md px-4 py-2 w-full"
                    type="text"
                />

                <label for="where_to_use">Where To Use</label>
                <input
                    v-model="product.where_to_use"
                    class="border border-gray-300 rounded-md px-4 py-2 w-full"
                    type="text"
                />

                <!-- Action buttons -->
                <div class="flex justify-end mt-6 space-x-2">
                    <button
                        @click="modal = false"
                        class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400"
                    >
                        Cancel
                    </button>
                    <button
                        @click="addProduct"
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition duration-300"
                    >
                        Confirm
                    </button>
                </div>

                <!-- Close icon -->
                <button
                    @click="modal = false"
                    class="absolute top-2 right-2 text-gray-500 hover:text-black text-xl"
                >
                    &times;
                </button>
            </div>
        </div>
        <!-- Modal Overlay end -->
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Requisition Table start -->
        <div>
            <div
                class="border border-gray-300 p-6 rounded-lg bg-white shadow-md"
            >
                <div class="text-right">
                    <h2 class="text-xl font-semibold text-gray-800">Invoice</h2>
                    <p class="text-sm text-gray-600">
                        {{ new Date().toISOString().slice(0, 10) }}
                    </p>
                </div>

                <div class="mt-6">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Requisition Order For:
                    </h3>
                </div>

                <div class="overflow-x-auto mt-6">
                    <table
                        class="min-w-full border border-gray-300 text-sm text-left text-gray-700"
                    >
                        <thead class="bg-gray-100 text-gray-800 font-semibold">
                            <tr>
                                <th class="px-3 py-2 border">No</th>
                                <th class="px-3 py-2 border">Name</th>
                                <th class="px-3 py-2 border">Qty</th>
                                <th class="px-3 py-2 border">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(product, index) in productList"
                                :key="index"
                                class="hover:bg-gray-50 transition-colors"
                            >
                                <td class="px-3 py-2 border">
                                    {{ index + 1 }}
                                </td>
                                <td class="px-3 py-2 border">
                                    {{ product.name }}
                                </td>
                                <td class="px-3 py-2 border">
                                    {{ product.requisition_qty }}
                                </td>
                                <td class="px-3 py-2 border text-center">
                                    <button
                                        @click="removeProduct(index)"
                                        class="text-white bg-red-500 hover:bg-red-600 px-3 py-1 rounded"
                                    >
                                        Remove
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <button
                    @click="createRequisiton()"
                    class="bg-indigo-500 text-white py-2 px-4 rounded mt-10 hover:bg-indigo-600 cursor-pointer"
                >
                    Confirm
                </button>
            </div>
        </div>
        <!-- Requisition Table end -->

        <!-- Product Table start -->
        <div>
            <span>{{ status }}</span>
            <div class="flex items-center gap-2 mb-4">
                <input
                    type="text"
                    class="border border-gray-300 rounded-md px-4 py-2 w-[300px]"
                    v-model="searchItem"
                    placeholder="Search here"
                />
                <button
                    @click="allProduct"
                    class="bg-indigo-500 text-white py-2 px-4 rounded hover:bg-indigo-600 cursor-pointer"
                >
                    All Product
                </button>
                <button
                    @click="lowStock"
                    class="bg-indigo-500 text-white py-2 px-4 rounded hover:bg-indigo-600 cursor-pointer"
                >
                    Low Stock
                </button>
            </div>

            <div class="relative">
                <!-- Loading Overlay start -->
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
                <!-- Loading Overlay end -->
                <EasyDataTable
                    :headers="headers"
                    :items="items"
                    alternating
                    :rows-per-page="10"
                    :search-field="searchField"
                    :search-value="searchItem"
                >
                    <template #item-action="{ id, unit, name, unit_type }">
                        <button
                            @click="showModal(id, unit, name, unit_type)"
                            class="bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-600 transition duration-300"
                        >
                            Add
                        </button>
                    </template>
                </EasyDataTable>
            </div>
            <!-- Product Table end -->
        </div>
    </div>
</template>

<script setup>
import { usePage, useForm, router } from "@inertiajs/vue3";
import { ref, reactive, onMounted, watch } from "vue";
import { createToaster } from "@meforma/vue-toaster";
import axios from "axios";


const toaster = createToaster({});
const page = usePage();


const modal = ref(false);
const isLoading = ref(false);
const status = ref("All Products");
const searchItem = ref();
const searchField = ref(["id", "name", "category.name"]);


const product = reactive({
    id: "",
    unit: "",
    unit_type: "",
    name: "",
    requisition_qty: 0,
    where_to_use: "",
    remarks: "",
    store_code: "",
    size: "",
});


const headers = [
    { text: "ID", value: "id" },
    { text: "Product Name", value: "name" },
    { text: "Category", value: "category.name" },
    { text: "Brand", value: "brand_name" },
    { text: "Quantity", value: "unit" },
    { text: "Action", value: "action" },
];


const productList = ref(JSON.parse(localStorage.getItem("productList")) || []);


watch(
    productList,
    (newList) => {
        localStorage.setItem("productList", JSON.stringify(newList));
    },
 { deep: true }
);

const items = ref([]);

onMounted(() => {
    allProduct();
});

async function allProduct() {
    try {
        isLoading.value = true;
        const res = await axios.get("/product-list");
        items.value = res.data.products;
        status.value = "All Products";
    } catch (error) {
        console.error("Failed to load product list", error);
    } finally {
        isLoading.value = false;
    }
}

async function lowStock() {
    try {
        isLoading.value = true;
        const res = await axios.get("/low-stock");
        items.value = res.data.minimumSotck;
        status.value = "Low Stock";
    } catch (error) {
        console.error("Failed to load product list", error);
    } finally {
        isLoading.value = false;
    }
}


function showModal(id, unit, name, unit_type) {
    Object.assign(product, {
        id,
        unit,
        name,
        unit_type,
        requisition_qty: 0,
        where_to_use: "",
        remarks: "",
        store_code: "",
        size: "",
    });
    modal.value = true;
}


function addProduct() {
    if (product.requisition_qty <= 0) {
        toaster.error("Minimum Quantity is 1");
        return;
    }

    const exists = productList.value.find((p) => p.id === product.id);
    if (exists) {
        toaster.error("Product Already Added");
        modal.value = false;
        return;
    }

    const newProduct = { ...product };
    productList.value.push(newProduct);
    modal.value = false;
}


function removeProduct(index) {
    productList.value.splice(index, 1);
}


const form = useForm({ products: "" });

function createRequisiton() {
    if (productList.value.length === 0) {
        toaster.error("Please Add Product");
        return;
    }

    form.products = productList.value;

    form.post("/create-requisition", {
        preserveScroll: true,
        onSuccess: () => {
            if (page.props.flash.status == true) {
                toaster.success(page.props.flash.message);
                localStorage.removeItem("productList");
                productList.value = [];
                router.visit("/list-requisition");
            } else {
                toaster.error(page.props.flash.message);
            }
        },
    });
}
</script>

