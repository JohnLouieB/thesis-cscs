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
import axios from "axios";

const topProducts = ref([]);
const topCustomers = ref([]);
const recentCustomers = ref([]);
const todaySales = ref(0);
const productsCount = ref(0);
const categoriesCount = ref(0);
const customersCount = ref(0);
const currentUser = ref([]);

onMounted(() => {
    getCurrentUser();
    getTopSales();
    getTopCustomers();
    getRecentCustomers();
    getTodaySales();
    getTotalProductsCount();
    getTotalCategoriesCount();
});

const getTopSales = () => {
    axios.get("/api/get-top-products").then((res) => {
        topProducts.value = res.data;
    });
};

const getTopCustomers = () => {
    axios.get("/api/get-top-customers").then((res) => {
        let temp = res.data.sort((a, b) => {
            return b.count - a.count;
        });
        topCustomers.value = temp.slice(0, 5);
    });
};

const getRecentCustomers = () => {
    axios.get("/api/get-recent-customers").then((res) => {
        let temp = res.data.filter((obj, index) => {
            return (
                index ===
                res.data.findIndex((o) => obj.client_name === o.client_name)
            );
        });
        customersCount.value = temp.length;
        let sorted = temp.sort(function (a, b) {
            return b.id - a.id;
        });
        recentCustomers.value = sorted.slice(0, 5);
    });
};

const getTodaySales = () => {
    let temp = [];
    axios.get("/api/get-today-sales").then((res) => {
        res.data.filter((e) => {
            temp.push(Number(e.total));
        });
        todaySales.value = temp.reduce(function (acc, val) {
            return acc + val;
        }, 0);
        todaySales.value = new Intl.NumberFormat("PHP", {
            style: "currency",
            currency: "PHP",
        }).format(todaySales.value);
    });
};

const getTotalProductsCount = () => {
    axios.get("/api/get-products-count").then((res) => {
        productsCount.value = res.data.length;
    });
};

const getTotalCategoriesCount = () => {
    axios.get("/api/get-categories-count").then((res) => {
        categoriesCount.value = res.data.length;
    });
};

const getCurrentUser = () => {
    axios.get("/current-user").then((res) => {
        currentUser.value = res.data;
    });
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div id="home" class="py-12 min-h-screen">
            <h4 class="page-title font-bold mb-8 text-center">
                Welcome to The Sticky Buns Dashboard
            </h4>
            <div
                :class="
                    currentUser.role == 'admin'
                        ? ''
                        : 'flex justify-center space-x-10'
                "
                class="flex flex-wrap mb-20"
            >
                <div
                    v-if="currentUser.role == 'admin'"
                    class="w-1/2 xl:w-1/4 px-3"
                >
                    <div
                        class="w-full bg-white border text-gray-700 rounded-lg flex items-center p-6 mb-6 xl:mb-0 hover:bg-violet-400 hover:text-white"
                    >
                        <img src="/sales.png" class="w-[80px] h-[84px]" />
                        <div class="ml-5 pt-5">
                            <p class="font-semibold text-2xl">
                                {{ todaySales }}
                            </p>
                            <p>Today Sales</p>
                        </div>
                    </div>
                </div>

                <div class="w-1/2 xl:w-1/4 px-3">
                    <div
                        class="w-full bg-white border text-gray-700 rounded-lg flex items-center p-6 mb-6 xl:mb-0 hover:bg-violet-400 hover:text-white"
                    >
                        <img src="/product.png" class="w-[80px] h-[84px]" />
                        <div class="ml-5 pt-5">
                            <p class="font-semibold text-2xl">
                                {{ productsCount }}
                            </p>
                            <p>Product List</p>
                        </div>
                    </div>
                </div>

                <div class="w-1/2 xl:w-1/4 px-3">
                    <div
                        class="w-full bg-white border text-gray-700 rounded-lg flex items-center p-6 hover:bg-violet-400 hover:text-white"
                    >
                        <img src="/category.png" class="w-[60px] h-[64px]" />
                        <div class="ml-5 pt-5">
                            <p class="font-semibold text-2xl">
                                {{ categoriesCount }}
                            </p>
                            <p>Category List</p>
                        </div>
                    </div>
                </div>

                <div class="w-1/2 xl:w-1/4 px-3">
                    <div
                        class="w-full bg-white border text-gray-700 rounded-lg flex items-center p-6 hover:bg-violet-400 hover:text-white"
                    >
                        <img src="/customer.png" class="w-[80px] h-[84px]" />
                        <div class="ml-5 pt-5">
                            <p class="font-semibold text-2xl">
                                {{ customersCount }}
                            </p>
                            <p>Customers</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap">
                <div
                    v-if="currentUser.role == 'admin'"
                    class="w-full xl:w-1/3 px-3"
                >
                    <div class="flex space-x-2">
                        <p class="text-xl font-semibold mb-4">Top Customers</p>
                        <img src="/crown.png" class="w-[20px] h-[26px]" />
                    </div>
                    <div
                        v-for="(customer, index) in topCustomers"
                        :key="index"
                        class="px-5 mt-4 py-5 bg-white rounded-lg shadow-lg hover:bg-violet-400 hover:text-white"
                    >
                        <div class="flex space-x-8">
                            <img
                                src="/customers.gif"
                                class="w-[50px] h-[54px]"
                            />
                            <span class="mt-4 text-lg">
                                {{ customer.client_name }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="w-full xl:w-1/3 px-3">
                    <div class="flex space-x-2">
                        <p class="text-xl font-semibold mb-4">Top Products</p>
                        <img src="/trophy.png" class="w-[24px] h-[23px]" />
                    </div>
                    <div
                        v-for="(product, index) in topProducts"
                        :key="index"
                        class="px-5 mt-4 py-5 bg-white rounded-lg shadow-lg hover:bg-violet-400 hover:text-white"
                    >
                        <div>
                            <span>{{ product.name }}</span>
                            <a-progress
                                strokeLinecap="square"
                                :percent="100"
                                :show-info="false"
                                :stroke-color="{
                                    '0%': '#108ee9',
                                    '100%': '#87d068',
                                }"
                            />
                        </div>
                    </div>
                </div>

                <div
                    v-if="currentUser.role == 'admin'"
                    class="w-full xl:w-1/3 px-3 "
                >
                    <div class="flex space-x-2 mt-3.5">
                        <p class="text-xl font-semibold mb-4">
                            Recent Transactions
                        </p>
                        <img src="/transaction.png" class="w-[28px] h-[26px]" />
                    </div>
                    <div class="w-full bg-white border rounded-lg p-4">
                        <div
                            v-for="(customer, index) in recentCustomers"
                            :key="index"
                            class="w-full bg-gray-100 border rounded-lg flex justify-between items-center px-4 py-2 mb-4 hover:bg-violet-400 hover:text-white"
                        >
                            <div>
                                <p class="font-semibold text-xl">
                                    {{ customer.client_name }}
                                </p>
                                <span class="underline"
                                    >processed by:
                                    {{ customer.processed_by }}</span
                                >
                            </div>
                            <span class="font-semibold text-lg">{{
                                new Intl.NumberFormat("PHP", {
                                    style: "currency",
                                    currency: "PHP",
                                }).format(customer.total)
                            }}</span>
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
.page-title {
    font-weight: 400 !important;
    font-size: 20px !important;
    line-height: 23px !important;
    color: #000000 !important;
}
</style>
