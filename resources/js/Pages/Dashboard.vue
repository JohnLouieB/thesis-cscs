<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref } from "vue";


const showModal = ref(false);

function openModal() {
    if (showModal.value) {
        showModal.value = false;
    } else {
        showModal.value = true;
    }
    console.log(showModal.value);
}
const handleOk = (e) => {
    console.log("working");
    showModal.value = false;
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
       
        <div id="home">

<!-- breadcrumb -->
    <nav class="text-sm font-semibold mb-6" aria-label="Breadcrumb">
      <ol class="list-none p-0 inline-flex">
        <li class="flex items-center">
          <a href="#" class="text-gray-600">Dashboard</a>
        </li>
      </ol>
    </nav>
    <!-- breadcrumb end -->

    <div class="lg:flex justify-between items-center mb-6">
      <p class="text-2xl font-semibold mb-2 lg:mb-0">Good evening, James!</p>
      <!-- <button class="bg-blue-500 hover:bg-blue-600 focus:outline-none rounded-lg px-6 py-2 text-white font-semibold shadow">View Logs</button> -->
    </div>

    <div class="flex flex-wrap -mx-3 mb-20">

      <div class="w-1/2 xl:w-1/4 px-3">
        <div class="w-full bg-white border text-blue-400 rounded-lg flex items-center p-6 mb-6 xl:mb-0">
            <svg viewBox="0 0 24 24">
  <path d="M2,12 L4,12 L4,16 L6,16 L6,8 L4,8 L4,12 L2,12 Z M8,6 L8,18 L10,18 L10,12 L14,12 L14,18 L16,18 L16,6 L14,6 L14,12 L10,12 L10,6 L8,6 Z M18,10 L20,10 L20,6 L22,6 L22,14 L20,14 L20,10 L18,10 Z" fill="currentColor" />
</svg>

          <div class="text-gray-700">
            <p class="font-semibold text-3xl">750.00</p>
            <p>Today Sales</p>
          </div>

        </div>
      </div>

      <div class="w-1/2 xl:w-1/4 px-3">
        <div class="w-full bg-white border text-blue-400 rounded-lg flex items-center p-6 mb-6 xl:mb-0">
            <svg viewBox="0 0 24 24">
  <path d="M10,2 L6,6 L6,18 L18,18 L18,6 L14,2 L10,2 Z M8,8 L16,8 L16,16 L8,16 L8,8 Z M12,18 L12,22 L14,22 L14,18 L12,18 Z M10,10 L14,10 L14,14 L10,14 L10,10 Z" fill="currentColor" />
</svg>

          <div class="text-gray-700">
            <p class="font-semibold text-3xl">85</p>
            <p>Product List</p>
          </div>
        </div>
      </div>

      <div class="w-1/2 xl:w-1/4 px-3">
        <div class="w-full bg-white border text-blue-400 rounded-lg flex items-center p-6">
            <svg viewBox="0 0 24 24">
  <path d="M4,2 L20,2 L20,22 L4,22 L4,2 Z M6,4 L6,20 L18,20 L18,4 L6,4 Z M8,6 L16,6 L16,8 L8,8 L8,6 Z M8,10 L16,10 L16,12 L8,12 L8,10 Z M8,14 L16,14 L16,16 L8,16 L8,14 Z" fill="currentColor" />
</svg>

          <div class="text-gray-700">
            <p class="font-semibold text-3xl">3</p>
            <p>Category List</p>
          </div>
        </div>
      </div>

      <div class="w-1/2 xl:w-1/4 px-3">
        <div class="w-full bg-white border text-blue-400 rounded-lg flex items-center p-6">
            <svg viewBox="0 0 24 24">
  <path d="M8,14 C9.657,14 11,12.657 11,11 C11,9.343 9.657,8 8,8 C6.343,8 5,9.343 5,11 C5,12.657 6.343,14 8,14 Z M16,14 C17.657,14 19,12.657 19,11 C19,9.343 17.657,8 16,8 C14.343,8 13,9.343 13,11 C13,12.657 14.343,14 16,14 Z M12,2 C8.134,2 5,5.134 5,9 C5,13 12,22 12,22 C12,22 19,13 19,9 C19,5.134 15.866,2 12,2 Z" fill="currentColor" />
</svg>

          <div class="text-gray-700">
            <p class="font-semibold text-3xl">175</p>
            <p>Customers</p>
          </div>

        </div>
      </div>

    </div>

    <div class="flex flex-wrap -mx-3">
      <div class="w-full xl:w-1/3 px-3">
        <p class="text-xl font-semibold mb-4">Recent Sales</p>
        <div class="w-full bg-white border rounded-lg p-4 mb-8 xl:mb-0">

            <div class="overflow-hidden rounded-lg shadow-lg">
            <div class="bg-neutral-50 py-3 px-5 dark:bg-neutral-700 dark:text-neutral-200">
                Doughnut chart
            </div>
            <canvas class="p-10" id="chartDoughnut"></canvas>
            </div>

         
  


          <canvas id="buyers-chart" width="600" height="400"></canvas>
        </div>
      </div>

      <div class="w-full xl:w-1/3 px-3">
        <p class="text-xl font-semibold mb-4">Recent Customers</p>
        <div class="w-full bg-white border rounded-lg p-4 mb-8 xl:mb-0">

            <div class="overflow-hidden rounded-lg shadow-lg">
            <div class="bg-neutral-50 py-3 px-5 dark:bg-neutral-700 dark:text-neutral-200">
                Doughnut chart
            </div>
            <canvas class="p-10" id="chartDoughnut"></canvas>
            </div>

          <canvas id="reviews-chart" width="600" height="400"></canvas>      
        </div>
      </div>

      <div class="w-full xl:w-1/3 px-3">
        <p class="text-xl font-semibold mb-4">Recent Transactions</p>
        <div class="w-full bg-white border rounded-lg p-4">
          <div class="w-full bg-gray-100 border rounded-lg flex justify-between items-center px-4 py-2 mb-4">
              <div>
                  <p class="font-semibold text-xl">Angelo</p>
                  <p>Chocolate drift</p>
              </div>
              <span class="text-pink-500 font-semibold text-lg">350.00</span>
          </div>

          <div class="w-full bg-gray-100 border rounded-lg flex justify-between items-center px-4 py-2 mb-4">
              <div>
                  <p class="font-semibold text-xl">Mark Lyster</p>
                  <p>Yema Cake</p>
              </div>
              <span class="text-pink-500 font-semibold text-lg">550.00</span>
          </div>

          <div class="w-full bg-gray-100 border rounded-lg flex justify-between items-center px-4 py-2 mb-4">
              <div>
                  <p class="font-semibold text-xl">Jeandy</p>
                  <p>Shabuuu</p>
              </div>
              <span class="text-pink-500 font-semibold text-lg">150.00</span>
          </div>

          <div class="w-full bg-gray-100 border rounded-lg flex justify-between items-center px-4 py-2">
              <div>
                  <p class="font-semibold text-xl">Reymark</p>
                  <p>Vanilla Cake</p>
              </div>
              <span class="text-pink-500 font-semibold text-lg">450.99</span>
          </div>
        </div>
      </div>

    </div>

</div>
    </AuthenticatedLayout>

</template>

        

<style>
body {
    background-image: url('bglogin.jpg');
    background-repeat: no-repeat;
    background-size: cover;
}
</style>
