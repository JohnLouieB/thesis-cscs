<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref } from "vue";
import {
    UserOutlined,
    ProjectTwoTone,
    ProfileTwoTone,
    ContainerTwoTone,
    SmileTwoTone,
} from "@ant-design/icons-vue";

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
                <p class="text-2xl font-semibold mb-2 lg:mb-0">
                    Good evening, James!
                </p>
                <!-- <button class="bg-blue-500 hover:bg-blue-600 focus:outline-none rounded-lg px-6 py-2 text-white font-semibold shadow">View Logs</button> -->
            </div>

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
                    <p class="text-xl font-semibold mb-4">Top Product</p>
                    <div class="px-5 py-5 bg-white rounded-lg shadow-lg">
                        <a>Hotdog</a>
                        <a-progress
                            :percent="90"
                            status="active"
                            strokeColor="red"
                        />
                        <a>Hotdog</a>
                        <a-progress
                            :percent="70"
                            status="active"
                            strokeColor="green"
                        />
                        <a>Hotdog</a>
                        <a-progress
                            :percent="50"
                            status="active"
                            strokeColor="pink"
                        />
                        <a>Hotdog</a>
                        <a-progress
                            :percent="30"
                            status="active"
                            strokeColor="violet"
                        />
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
