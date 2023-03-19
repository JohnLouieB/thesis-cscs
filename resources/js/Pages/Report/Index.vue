<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm, Head } from "@inertiajs/vue3";
import { ref, watchEffect, onMounted } from "vue";
import { message, notification } from "ant-design-vue";
import { formatStrategyValues } from "ant-design-vue/lib/vc-tree-select/utils/strategyUtil";
import moment from "moment";

//Variables
const props = defineProps({
    reports: Array,
    date: String,
});

const columns = [
    {
        title: "Date created",
        dataIndex: "created_at",
        key: "created_at",
    },
    {
        title: "Client Name",
        dataIndex: "client_name",
        key: "client_name",
    },
    {
        title: "Amount",
        dataIndex: "total",
        key: "total",
    },
    {
        title: "Processed By",
        dataIndex: "processed_by",
        key: "processed_by",
    },
    {
        title: "Action",
        key: "action",
    },
    // {
    //     title: "Product",
    //     dataIndex: "data",
    //     key: "data",
    // },
    // {
    //     title: "Total",
    //     dataIndex: "total",
    //     key: "total",
    // },
    // {
    //     title: "Change",
    //     dataIndex: "change",
    //     key: "change",
    // },
];
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
                <div class="mb-5 text-center">Daily Sales Report</div>
                <!-- <div class="mb-5">
                    <a-button
                        type="primary"
                        @click="showCreateSaleModal = true"
                    >
                        Create
                    </a-button>
                </div> -->
                <a-table :columns="columns" :data-source="props.reports">
                    <template #bodyCell="{ column, text, record }">
                        <template v-if="column.dataIndex === 'data'">
                            <div>
                                <a>{{ record.data }}</a>
                            </div>
                        </template>
                        <template v-if="column.dataIndex === 'created_at'">
                            <span>{{ props.date }}</span>
                        </template>
                    </template>
                </a-table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
