<script setup>
import { ref } from 'vue'
import { usePage,useForm, router } from '@inertiajs/vue3';
import { createToaster } from "@meforma/vue-toaster";
const toaster = createToaster({ });
const page = usePage();

const headers = [
{text:'Requisition No',value:'requisition.id'},
{text:'Product Name',value:'product.name'},
{text:'Total Requistion',value:'total_requisition'},
{text:'Unit Type',value:'product.unit_type'},
{text:'Action',value:'action'},
]
const searchItem=ref();
const searchField = ref(["requisition.id","product.name"]);
const items=ref(page.props.requisitionProducts);


const modal = ref(false);

const form = useForm({
    received_qty: '',
    requisition_product_id: '',
})
const showModalDetails = ref({
    productName: '',
    total_requisition: '',
    unit_type: '',

});

function showModal(id, product, total_requisition) {
    showModalDetails.value.unit_type = product.unit_type;
    showModalDetails.value.productName = product.name;
    showModalDetails.value.total_requisition = total_requisition;
    form.requisition_product_id = id;
    modal.value = true
}


function confirmAction() {
    if(form.received_qty==0 || form.received_qty==''){
        toaster.error('Received quantity must be greater than 0');
        return;
    }
  if(confirm('Are you sure you want to received this product?')) {
    form.post('/requisition-received-request',{
        preserveScroll: true,
        onSuccess:()=>{
            if(page.props.flash.status==false){
                toaster.error(page.props.flash.message);
            }else if(page.props.flash.status==true){
                toaster.success(page.props.flash.message);
                 showModal.value = false
                 router.visit('/requisition-product-list');
            }
        }
    });
  }

}


</script>

<template>

  <div class="p-4 bg-[#ffffff]">
    <h1 class="d-flex text-2xl font-bold mb-4">Requisition Product List</h1>
    <input type="text" class="border border-gray-300 rounded-md px-4 py-2 w-[300px] mb-4" v-model="searchItem" placeholder="Search here">

    <EasyDataTable :headers="headers" :items="items" alternating :rows-per-page="50" :search-field="searchField" :search-value="searchItem">
        <template #item-action="{ id, product, total_requisition }">
            <button @click="showModal(id, product, total_requisition)" class="bg-green-500 text-white font-bold py-2 px-4 rounded hover:bg-green-600 transition duration-300">Received</button>
        </template>
    </EasyDataTable>

     <div>

        <!-- Modal Overlay -->
        <div v-if="modal" class="fixed inset-0 bg-black/15 bg-opacity-50 flex items-center justify-center z-50">
          <!-- Modal Box -->
          <div class="bg-white w-full max-w-md rounded-lg shadow-lg p-6 relative">
            <h2 class="text-xl text-center font-semibold mb-4">Received Qty</h2>

            <div class="mb-4 text-center">
              <p><span style="color: blueviolet;">Product Name:</span> {{ showModalDetails.productName }}</p>
              <p><span style="color: blueviolet;">Total Requisition:</span> {{ showModalDetails.total_requisition }} {{ showModalDetails.unit_type }}</p>
            </div>

            <input v-model="form.received_qty" class="border border-gray-300 rounded-md px-4 py-2 w-full" type="text">

            <!-- Action buttons -->
            <div class="flex justify-end mt-6 space-x-2">
              <button
                @click="modal = false"
                class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 transition duration-300"
              >
                Cancel
              </button>
              <button
                @click="confirmAction"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition duration-300"
              >
                Confirm
              </button>
            </div>

            <!-- Close icon -->
            <button
              @click="modal = false"
              class="absolute top-2 right-2 text-red-500 hover:text-red-600 text-2xl transition duration-300"
            >
              &times;
            </button>
          </div>
        </div>
     </div>
  </div>

</template>

<style scoped>

</style>
