<script setup>
import DamageDetails from './DamageDetails.vue';
import { ref } from 'vue'
import { usePage,useForm,Link } from '@inertiajs/vue3';
const page = usePage();

const headers = [
    {text:'ID',value:'id'},
    {text:'Product Name',value:'product.name'},
    {text:'Damage Qty',value:'unit'},
    {text:'Unit Type',value:'product.unit_type'},
    {text:'Receive Date',value:'damage_date'},
]
const formatDate = (date) => {
  const d = new Date(date).toLocaleString();
  return d;
};
const items=ref(page.props.damageProducts.data);
const searchField = ref(["id","product.name"]);
const searchItem=ref();

const modal = ref(false);
const fromDate=new URLSearchParams(window.location.search).get('fromDate');
const toDate=new URLSearchParams(window.location.search).get('toDate');
const form=useForm({
    fromDate: fromDate,
    toDate: toDate,
});
function submitForm() {
    form.get('/damage-product-list');
}
function showModal() {
    modal.value = true
}
</script>

<template>
  <DamageDetails
    v-model:modal="modal"
    :damageItem="items"
    :fromDate="form.fromDate"
    :toDate="form.toDate"
  />

  <div class="p-4 bg-[#f8f8f8]">
    <div class="mb-4">
      <h1 class="text-2xl font-bold mb-4">Damage Product List</h1>
    </div>

    <div class="flex flex-wrap gap-2 mb-4">
      <input
        type="text"
        class="px-2 py-1 border border-gray-300 text-sm rounded w-[150px]"
        v-model="searchItem"
        placeholder="Search here"
      />
      <input
        v-model="form.fromDate"
        type="date"
        class="px-2 py-1 border border-gray-300 text-sm rounded w-[140px]"
      />
      <input
        v-model="form.toDate"
        type="date"
        class="px-2 py-1 border border-gray-300 text-sm rounded w-[140px]"
      />

      <div class="flex flex-wrap gap-2">
        <button
          @click="submitForm()"
          class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 text-xs rounded transition duration-300"
        >
          Search Filter
        </button>
        <Link
          class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 text-xs rounded flex items-center transition duration-300"
          :href="`/damage-product-list`"
        >
          Clear Search
        </Link>
        <button
          @click="showModal()"
          class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 text-xs rounded transition duration-300"
        >
          View All
        </button>
      </div>
    </div>

    <div>
      <EasyDataTable
        :headers="headers"
        :items="items"
        alternating
        :rows-per-page="5"
        :search-field="searchField"
        :search-value="searchItem"
      >
        <template #item-damage_date="{ created_at }">
          {{ formatDate(created_at) }}
        </template>
      </EasyDataTable>
    </div>

    <div class="flex gap-2 justify-end mt-4 text-sm">
      <Link
        v-if="page.props.damageProducts.prev_page_url"
        :href="page.props.damageProducts.prev_page_url"
        class="text-blue-500 hover:underline"
      >
        Prev
      </Link>
      <Link
        v-if="page.props.damageProducts.next_page_url"
        :href="page.props.damageProducts.next_page_url"
        class="text-blue-500 hover:underline"
      >
        Next
      </Link>
    </div>
  </div>
</template>

