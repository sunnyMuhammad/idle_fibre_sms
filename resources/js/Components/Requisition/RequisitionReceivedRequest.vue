<script setup>
import { usePage, Link, router } from "@inertiajs/vue3";
import { ref } from "vue";
import { createToaster } from "@meforma/vue-toaster";
const toaster = createToaster({});
const page = usePage();
const headers = [
    { text: "Requisition No", value: "requisition_product.requisition.id" },
    { text: "Product Name", value: "product.name" },
    {
        text: "Total Requistion",
        value: "requisition_product.total_requisition",
    },
    { text: "Received Qty", value: "received_qty" },
    { text: "Unit Type", value: "product.unit_type" },
    { text: "Status", value: "status" },
    { text: "Action", value: "action" },
];
const items = ref(page.props.recievedRequests);

function approvedRequest(received_id) {
    if (confirm("Are you sure you want to approved this request?")) {
        router.visit(
            `/requisition-approve-request?received_id=${received_id} & status=approved`
        );
    }
}
function cancelRequest(received_id) {
    if (confirm("Are you sure you want to Cancel this request?")) {
        router.visit(
            `/requisition-approve-request?received_id=${received_id} & status=cancel`
        );
    }
}

if (page.props.flash.status == true) {
    toaster.success(page.props.flash.message);
} else if (page.props.flash.status == false) {
    toaster.error(page.props.flash.message);
}
</script>

<template>
    <div class="p-4 bg-white max-w-full">
        <h1 class="text-2xl font-bold mb-4">Approve Requisition</h1>

        <EasyDataTable
            :headers="headers"
            :items="items"
            alternating
            :rows-per-page="50"
        >
            <template #item-action="{ id }">
                <div class="flex flex-wrap gap-1 pt-1 pb-1">
                    <Link
                        :href="`/edit-requisition-request-page?id=${id}`"
                        class="bg-blue-500 hover:bg-blue-700 text-white py-1 px-2 rounded text-xs flex items-center justify-center transition duration-300"
                    >
                        <span class="material-icons text-sm">edit</span>

                    </Link>
                    <button
                        @click="approvedRequest(id)"
                        class="bg-green-500 hover:bg-green-700 text-white py-1 px-2 rounded text-xs transition duration-300"
                    >
                        Approve
                    </button>
                    <button
                        @click="cancelRequest(id)"
                        class="bg-red-500 hover:bg-red-700 text-white py-1 px-2 rounded text-xs flex items-center justify-center transition duration-300"
                    >
                        <span class="material-icons text-sm">close</span>

                    </button>
                </div>
            </template>
        </EasyDataTable>
    </div>
</template>
