<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";
import {
    UserOutlined,
    ProjectTwoTone,
    ProfileTwoTone,
    ContainerTwoTone,
    SmileTwoTone,
} from "@ant-design/icons-vue";

const showModal = ref(false);
const topProducts = ref([]);
const topCustomers = ref([]);

onMounted(() => {
    getTopSales();
    getTopCustomers();
});

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

const getTopSales = () => {
    axios.get("/api/get-top-products").then((res) => {
        topProducts.value = res.data;
    });
};

const getTopCustomers = () => {
    axios.get("/api/get-top-customers").then((res) => {
        topCustomers.value = res.data;
        console.log(topCustomers.value);
    });
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div id="home">
            <div class="flex flex-wrap -mx-3 mb-20">
                <div class="w-1/2 xl:w-1/4 px-3">
                    <div
                        class="w-full bg-white border text-blue-400 rounded-lg flex items-center p-6 mb-6 xl:mb-0"
                    >
                        <project-two-tone
                            two-tone-color="#eb2f96"
                            style="font-size: 80px"
                        />
                        <div class="text-gray-700">
                            <p class="font-semibold text-2xl">750.00</p>
                            <p>Today Sales</p>
                        </div>
                    </div>
                </div>

                <div class="w-1/2 xl:w-1/4 px-3">
                    <div
                        class="w-full bg-white border text-blue-400 rounded-lg flex items-center p-6 mb-6 xl:mb-0"
                    >
                        <profile-two-tone
                            two-tone-color="#eb2f96"
                            style="font-size: 80px"
                        />

                        <div class="text-gray-700">
                            <p class="font-semibold text-2xl">85</p>
                            <p>Product List</p>
                        </div>
                    </div>
                </div>

                <div class="w-1/2 xl:w-1/4 px-3">
                    <div
                        class="w-full bg-white border text-blue-400 rounded-lg flex items-center p-6"
                    >
                        <container-two-tone
                            two-tone-color="#eb2f96"
                            style="font-size: 80px"
                        />

                        <div class="text-gray-700">
                            <p class="font-semibold text-2xl">3</p>
                            <p>Category List</p>
                        </div>
                    </div>
                </div>

                <div class="w-1/2 xl:w-1/4 px-3">
                    <div
                        class="w-full bg-white border text-blue-400 rounded-lg flex items-center p-6"
                    >
                        <smile-two-tone
                            two-tone-color="#eb2f96"
                            style="font-size: 80px"
                        />

                        <div class="text-gray-700">
                            <p class="font-semibold text-2xl">175</p>
                            <p>Customers</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap -mx-3">
                <div class="w-full xl:w-1/3 px-3">
                    <p class="text-xl font-semibold mb-4">Top Customers</p>
                    <div class="px-5 py-5 bg-white rounded-lg shadow-lg">
                        <div class="flex space-x-8">
                            <a-avatar :size="64">
                                <template #icon><UserOutlined /></template>
                            </a-avatar>
                            <h4 class="mt-5">Diano James</h4>
                            <a class="mt-5 w-1/2">
                                <span class="float-right">
                                    View Transactions
                                </span>
                            </a>
                        </div>
                        <div class="flex space-x-8 mt-2">
                            <a-avatar :size="64">
                                <template #icon><UserOutlined /></template>
                            </a-avatar>
                            <h4 class="mt-5">Diano James</h4>
                            <a class="mt-5 w-1/2">
                                <span class="float-right">
                                    View Transactions
                                </span>
                            </a>
                        </div>
                        <div class="flex space-x-8 mt-2">
                            <a-avatar :size="64">
                                <template #icon><UserOutlined /></template>
                            </a-avatar>
                            <h4 class="mt-5">Diano James</h4>
                            <a class="mt-5 w-1/2">
                                <span class="float-right">
                                    View Transactions
                                </span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="w-full xl:w-1/3 px-3">
                    <p class="text-xl font-semibold mb-4">Top Products</p>
                    <div
                        v-for="(product, index) in topProducts"
                        :key="index"
                        class="px-5 mt-4 py-5 bg-white rounded-lg shadow-lg"
                    >
                        <div>
                            <a>{{ product.name }}</a>
                            <a-progress
                                :percent="90"
                                status="active"
                                strokeColor="red"
                            />
                        </div>
                    </div>
                </div>

                <div class="w-full xl:w-1/3 px-3">
                    <p class="text-xl font-semibold mb-4">
                        Recent Transactions
                    </p>
                    <div class="w-full bg-white border rounded-lg p-4">
                        <div
                            class="w-full bg-gray-100 border rounded-lg flex justify-between items-center px-4 py-2 mb-4"
                        >
                            <div>
                                <p class="font-semibold text-xl">Angelo</p>
                                <p>Chocolate drift</p>
                            </div>
                            <span class="text-pink-500 font-semibold text-lg"
                                >350.00</span
                            >
                        </div>

                        <div
                            class="w-full bg-gray-100 border rounded-lg flex justify-between items-center px-4 py-2 mb-4"
                        >
                            <div>
                                <p class="font-semibold text-xl">Mark Lyster</p>
                                <p>Yema Cake</p>
                            </div>
                            <span class="text-pink-500 font-semibold text-lg"
                                >550.00</span
                            >
                        </div>

                        <div
                            class="w-full bg-gray-100 border rounded-lg flex justify-between items-center px-4 py-2 mb-4"
                        >
                            <div>
                                <p class="font-semibold text-xl">Jeandy</p>
                                <p>Shabuuu</p>
                            </div>
                            <span class="text-pink-500 font-semibold text-lg"
                                >150.00</span
                            >
                        </div>

                        <div
                            class="w-full bg-gray-100 border rounded-lg flex justify-between items-center px-4 py-2"
                        >
                            <div>
                                <p class="font-semibold text-xl">Reymark</p>
                                <p>Vanilla Cake</p>
                            </div>
                            <span class="text-pink-500 font-semibold text-lg"
                                >450.99</span
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
body {
    background-image: url("bglogin.jpg");
    background-repeat: no-repeat;
    background-size: cover;
}
</style>
