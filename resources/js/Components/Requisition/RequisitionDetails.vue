<script setup>
import { ref } from "vue";
const props = defineProps({
    products: Object,
    modal: Boolean,
});

const emit = defineEmits(["update:modal"]);
const printModal = () => {
    const printContents = document.getElementById("modal-content").innerHTML;
    const originalContents = document.body.innerHTML;

    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
    location.reload();
};
</script>

<template>
    <div
        v-if="modal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
    >
        <div id="modal-content"
            class="bg-white w-[1000px] max-w-full rounded-xl shadow-2xl p-6 relative print:w-full print:shadow-none print:p-0 h-[500px] overflow-auto"
        >
            <div class="flex justify-between mb-4">
                <div class="mt-4">
                    <h1 class="text-xl font-bold">Store Purchase Requisition</h1>
                    <p>Date: {{ new Date(props.products.created_at).toLocaleString().split(",")[0] }}</p>
                </div>
                <div class="font-bold">
                    <img class="h-[90px]" src="../../Assets/img/logo.jpg" alt="Logo" />
                </div>
            </div>

            <div class="mb-4 text-xl">
              <p><strong>Requisition No:</strong> {{ props.products.id }}</p>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto overflow-y-auto">
                <table class="w-full mb-4 text-sm border border-black border-collapse">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="text-center border border-black px-2 py-1">#</th>
                            <th class="text-center border border-black px-2 py-1">Name of Item</th>
                            <th class="text-center border border-black px-2 py-1">Size</th>
                            <th class="text-center border border-black px-2 py-1">Unit</th>
                            <th class="text-center border border-black px-2 py-1">Required Quantity</th>
                            <th class="text-center border border-black px-2 py-1">Present Stock</th>
                            <th class="text-center border border-black px-2 py-1">Store Code</th>
                            <th class="text-center border border-black px-2 py-1">Where to be use</th>
                            <th class="text-center border border-black px-2 py-1">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(product, index) in props.products.requisition_products"
                            :key="product.id"
                            class="hover:bg-gray-50 print:border-2 border border-black"
                        >
                            <td class="text-center border border-black px-2 py-1">{{ index + 1 }}</td>
                            <td class="text-center border border-black px-2 py-1">{{ product.product.name }}</td>
                            <td class="text-center border border-black px-2 py-1">{{ product.size }}</td>
                            <td class="text-center border border-black px-2 py-1">{{ product.product.unit_type }}</td>
                            <td class="text-center border border-black px-2 py-1">{{ product.total_requisition }}</td>
                            <td class="text-center border border-black px-2 py-1">{{ product.product.unit }}</td>
                            <td class="text-center border border-black px-2 py-1">{{ product.store_code }}</td>
                            <td class="text-center border border-black px-2 py-1">{{ product.where_to_use }}</td>
                            <td class="text-center border border-black px-2 py-1">{{ product.remarks }}</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Action Buttons -->
                <div class="flex justify-between mt-6">
                    <button
                        @click="$emit('update:modal', false)"
                        class="bg-red-500 text-white py-2 px-4 rounded-md hover:bg-red-600 print:hidden"
                    >
                        Close
                    </button>
                    <button
                       @click="printModal"
                        class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 print:hidden"
                    >
                        Print
                    </button>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-6 text-center text-xs text-gray-500 print:hidden">
                Press the Print button or Ctrl+P to print this summary.
            </div>
            <div class="absolute bottom-2 text-sm font-bold w-full hidden print:flex justify-between">
                <p class="border-t border-black px-2">Prepared by</p>
                <p class="border-t border-black px-2">Manager(store)</p>
                <p class="border-t border-black px-2">Manager(Maintenence)</p>
                <p class="border-t border-black px-2">Manager(Prodt.)</p>
                <p class="border-t border-black px-2">G M(F & A)</p>
            </div>
        </div>
    </div>
</template>
