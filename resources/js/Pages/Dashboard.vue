<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref } from "vue";
import { Carousel } from "ant-design-vue";

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
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Dashboard
            </h2>
        </template>

        <div class="py-12 h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-5 text-center">Welcome to CSCS dashboard!!!</div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
