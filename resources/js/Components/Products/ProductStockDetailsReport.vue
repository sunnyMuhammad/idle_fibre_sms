<script setup>
import {useForm,router} from "@inertiajs/vue3"
import {createToaster} from "@meforma/vue-toaster";
const toaster = createToaster({});
const props=defineProps({
    categories: Array,
    reportModal: Boolean,

})

const form=useForm({
    fromDate: '',
    toDate: '',
    category_id: '',
});

const emit = defineEmits(["update:reportModal"]);

function submitForm(){
    if(form.fromDate=='' || form.toDate==''){
        toaster.error("Please select date range");
        return;
    }else{
        window.location.href=(`/product-stock-report?fromDate=${form.fromDate}&toDate=${form.toDate}&category_id=${form.category_id}`);
        emit("update:reportModal", false);
    }

}
</script>

<template>
   <div>
        <!-- Modal Overlay -->
        <div
            v-if="reportModal"
            class="fixed inset-0 bg-black/15 bg-opacity-50 flex items-center justify-center z-50"
        >
            <!-- Modal Box -->
            <div
                class="bg-white w-full max-w-md rounded-lg shadow-lg p-6 relative"
            >
               <label for="categories">Select Category</label>
                <select v-model="form.category_id" id="categories" class="border border-gray-300 rounded-md px-4 py-2 w-full bg-white">
                    <option selected value="">Select Category</option>
                    <option v-for="category in categories" :value="category.id">
                        {{ category.name }}
                    </option>
                </select>

                <label for="fromDate">From</label>
                <input
                    v-model="form.fromDate"
                    class="border border-gray-300 rounded-md px-4 py-2 w-full"
                    type="date"

                />
                <label for="toDate">To</label>
                <input
                    v-model="form.toDate"
                    class="border border-gray-300 rounded-md px-4 py-2 w-full"
                    type="date"

                />

                <!-- Action buttons -->
                <div class="flex justify-end mt-6 space-x-2">
                    <button
                        @click="$emit('update:reportModal', false)"
                        class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400"
                    >
                        Cancel
                    </button>
                    <button
                        @click="submitForm"
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
                    >
                        Download
                    </button>
                </div>

                <!-- Close icon -->
                <button
                    @click="$emit('update:reportModal', false)"
                    class="absolute top-2 right-2 text-gray-500 hover:text-black text-xl"
                >
                    &times;
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
