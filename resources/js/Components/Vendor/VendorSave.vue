<script setup>
import { usePage, useForm } from "@inertiajs/vue3";
import { createToaster } from "@meforma/vue-toaster";
import { router, Link } from "@inertiajs/vue3";
import { computed } from "vue";

const page = usePage();
const errors = computed(() => page.props.flash.errors || {});
const toaster = createToaster({});

const vendorId = new URLSearchParams(window.location.search).get("vendor_id");
const vendor = page.props.vendor;

const form = useForm({
  vendor_id: vendorId,
  name: "",
  phone: "",
  address: "",
});

let URL = "/create-vendor";
if (vendorId != 0 && vendor != null) {
  form.name = vendor.name;
  form.phone = vendor.phone;
  form.address = vendor.address;
  URL = "/update-vendor";
}

function submitForm() {
  form.post(URL, {
    preserveScroll: true,
    onSuccess: () => {
      if (page.props.flash.status == false) {
        toaster.error(page.props.flash.message);
      } else if (page.props.flash.status == true) {
        toaster.success(page.props.flash.message);
        router.visit("/list-vendor");
      }
    },
  });
}
</script>

<template>
  <div class="container mx-auto py-8 px-4">
    <form
      @submit.prevent="submitForm"
      class="max-w-lg mx-auto bg-white p-8 rounded-md shadow-md"
    >
      <div class="flex justify-end mb-4">
        <Link
          href="/list-vendor"
          class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-md transition duration-300"
          >Back</Link
        >
      </div>

      <h2 class="text-2xl font-semibold text-gray-800 mb-6">
        {{ vendorId == 0 ? "Add Vendor" : "Update Vendor" }}
      </h2>

      <div class="mb-4">
        <label
          for="name"
          class="block text-sm font-medium text-gray-700 mb-1"
          >Name</label
        >
        <input
          v-model="form.name"
          type="text"
          id="name"
          class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400"
        />
        <p v-if="errors.name" class="text-red-500 mt-1">{{ errors.name[0] }}</p>
      </div>

      <div class="mb-4">
        <label
          for="phone"
          class="block text-sm font-medium text-gray-700 mb-1"
          >Phone</label
        >
        <input
          v-model="form.phone"
          type="text"
          id="phone"
          class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400"
        />
        <p v-if="errors.phone" class="text-red-500 mt-1">{{ errors.phone[0] }}</p>
      </div>

      <div class="mb-6">
        <label
          for="address"
          class="block text-sm font-medium text-gray-700 mb-1"
          >Address</label
        >
        <input
          v-model="form.address"
          type="text"
          id="address"
          class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400"
        />
        <p
          v-if="errors.address"
          class="text-red-500 mt-1"
        >
          {{ errors.address[0] }}
        </p>
      </div>

      <button
        type="submit"
        class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-xl transition duration-300"
      >
        {{ vendorId == 0 ? "Add Vendor" : "Update Vendor" }}
      </button>
    </form>
  </div>
</template>

<style scoped></style>
