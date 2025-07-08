<script setup>
import { usePage, Link, router } from "@inertiajs/vue3";
import { createToaster } from "@meforma/vue-toaster";
import { ref,computed,watch } from "vue";
const toaster = createToaster({});
const page = usePage();

const headers = [
    { text: "ID", value: "id" },
    { text: "Image", value: "image" },
    { text: "Product Name", value: "name" },
    { text: "Category", value: "category.name" },
    { text: "Brand", value: "brand_name" },
    { text: "Quantity", value: "unit" },
    { text: "Unit Type", value: "unit_type" },
    { text: "Rack", value: "rack_no" },
    { text: "Column", value: "column_no" },
    { text: "Row", value: "row_no" },
    { text: "Action", value: "action" },
];
const items = computed(() => page.props.products.data);

const searchItem = ref('');

watch(searchItem,(value)=>{
   setTimeout(() => {
        router.visit(`/list-product-page?search=${value}`,{
        preserveState:true,
        preserveScroll:true,
        onSuccess:()=>{
            items.value = page.props.products.data
        }
    },500);
   })
});

function deleteProduct(porduct_id) {
    if (confirm("Are you sure you want to delete this product?")) {
        router.visit(`/delete-product?product_id=${porduct_id}`);
    }
}

if (page.props.flash.status == true) {
    toaster.success(page.props.flash.message);
} else if (page.props.flash.status == false) {
    toaster.error(page.props.flash.message);
}
</script>

<template>
    <div class="container mx-auto p-4 bg-white">
        <h1 class="flex text-2xl font-bold mb-4">Product List</h1>

        <input
            type="text"
            class="border border-gray-300 rounded-md px-4 py-2 w-[300px]"
            v-model="searchItem"
            placeholder="Search here"
        />
        <a v-if = "page.props.user.can['product-report']" class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1 rounded transition duration-300 m-1" href="/product-list-report">Download Report</a>
        <Link  class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1 rounded transition duration-300 m-1" href="/list-product-page">Refresh</Link>

        <div v-if="page.props.user.can['create-product']" class="my-4">
            <Link
                :href="`/product-save-page?product_id=${0}`"
                class="bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded transition duration-300"
            >
                Add New Product
            </Link>
        </div>

        <div>
            <EasyDataTable
                :headers="headers"
                :items="items"
                alternating
                :rows-per-page="50"
            >
                <template #item-image="{ image }">
                    <div class="py-2">
                        <img v-if="image"
                            :src="`/storage/uploads/${image}`"
                            class="object-cover h-[50px] w-[50px]"
                        />
                    </div>
                </template>

                <template #item-action="{ id }">
                    <div class="flex gap-1">
                        <Link
                            v-if="page.props.user.can['update-product']"
                            :href="`/product-save-page?product_id=${id}`"
                            class="bg-green-500 hover:bg-green-700 text-white py-1 px-2 rounded text-xs flex items-center justify-center transition duration-300"
                        >
                            <span class="material-icons text-sm">edit</span>
                        </Link>
                        <button
                            v-if="page.props.user.can['delete-product']"
                            @click="deleteProduct(id)"
                            class="bg-red-500 hover:bg-red-600 text-white py-1 px-2 rounded text-xs flex items-center justify-center transition duration-300"
                        >
                            <span class="material-icons text-sm">delete</span>
                        </button>
                        <Link
                            v-if="page.props.user.can['issue-product']"
                            :href="`/product-issue-page?product_id=${id}`"
                            class="bg-indigo-500 hover:bg-indigo-600 text-white py-1 px-2 rounded text-xs flex items-center justify-center transition duration-300"
                        >
                            Issue
                        </Link>
                        <Link
                            v-if="page.props.user.can['receive-floor-receive']"
                            :href="`/floor-recieve-page?product_id=${id}`"
                            class="bg-gray-500 hover:bg-gray-600 text-white py-1 px-2 rounded text-xs flex items-center justify-center transition duration-300"
                        >
                            Floor Receive
                        </Link>
                    </div>
                </template>
            </EasyDataTable>
        </div>

          <div class="flex justify-center gap-4 mt-6">
            <Link
                preserve-scroll preserve-state
                v-if="page.props.products.prev_page_url"
                :href="page.props.products.prev_page_url"
                class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300 transition duration-300"
            >
                ⬅️ Previous
            </Link>
            <Link
                preserve-scroll preserve-state
                v-if="page.props.products.next_page_url"
                :href="page.props.products.next_page_url"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition duration-300"
            >
                Next ➡️
            </Link>
        </div>
    </div>
</template>
