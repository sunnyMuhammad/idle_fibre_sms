<script setup>
import { useForm,usePage,router, Link } from '@inertiajs/vue3';
import { createToaster } from "@meforma/vue-toaster";
import { computed } from "vue";
const toaster = createToaster({ });
const page = usePage();

const errors=computed(() => page.props.flash.errors || {});

const form = useForm({
    floor_recieve: page.props.floorRecieve.unit,
    floor_recieve_id:page.props.floorRecieve.id,
})
const submitForm = () => {
    form.post("/update-floor-recieve", {
        preserveScroll: true,
        onSuccess: () => {
            if(page.props.flash.status==false){
                toaster.error(page.props.flash.message);
            }else if(page.props.flash.status==true){
                toaster.success(page.props.flash.message);
                router.visit('/floor-receive-list');
            }
        }
    })
};
</script>

<template>
  <!-- component -->
  <div class="flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-xl shadow-lg p-8">

      <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Floor Receive</h2>

      <div class="float-end">
        <Link
          href="/list-product"
          class="inline-block bg-green-600 hover:bg-green-700 text-white py-1 px-3 text-sm rounded mx-3"
        >
          Back
        </Link>
      </div>

      <div class="mb-4 text-center">
        <p><span class="text-violet-600 font-semibold">Product Name:</span> {{ page.props.floorRecieve.product.name }}</p>
        <p><span class="text-violet-600 font-semibold">Available Stock:</span> {{ page.props.floorRecieve.product.unit }}</p>
      </div>

      <form @submit.prevent="submitForm" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Recieve</label>
          <input
            v-model="form.floor_recieve"
            type="text"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all"
          />
          <p v-if="errors.floor_recieve" class="text-red-500">{{ errors.floor_recieve[0] }}</p>
        </div>
        <button
          type="submit"
          class="mt-4 w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 rounded-lg transition-colors"
        >
          Update
        </button>
      </form>
    </div>
  </div>
</template>


