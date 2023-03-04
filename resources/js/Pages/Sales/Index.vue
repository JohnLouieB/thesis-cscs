<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm, Head } from "@inertiajs/vue3";
import { ref, watchEffect, onMounted } from "vue";
import { message, notification } from "ant-design-vue";
import { formatStrategyValues } from "ant-design-vue/lib/vc-tree-select/utils/strategyUtil";
import moment from "moment";

//Variables
const props = defineProps({
    products: Array,
    sales: Array,
    date: String,
});

const showCreateSaleModal = ref(false);
const disable = ref(false);
const productId = ref("");
const price = ref(0);

const columns = [
    {
        title: "Product",
        dataIndex: "data",
        key: "data",
    },
    {
        title: "Total",
        dataIndex: "total",
        key: "total",
    },
    {
        title: "Tendered Amount",
        dataIndex: "tendered_amount",
        key: "tendered_amount",
    },
    {
        title: "Change",
        dataIndex: "change",
        key: "change",
    },
    {
        title: "Date created",
        dataIndex: "created_at",
        key: "created_at",
    },
];

const form = useForm({
    items: [],
    total: 0,
    tendered_amount: 1000,
    change: null,
});

// console.log(props.products);
console.log(props.sales);

const handleOk = (e) => {
    showModal.value = false;
};

const handleAddProduct = (e) => {
    if (e.stock < 1) {
        notification["error"]({
            description: `The item ${e.name} is currently out of stock.`,
        });
    } else {
        notification["success"]({
            message: `${e.name} ₱${e.price}.00`,
            description: `You added ${e.name} in the list.`,
        });
        form.total = form.total + Number(e.price);
        if (form.items.length == 0) {
            form.items.push({
                name: e.name,
                quantity: 1,
                price: Number(e.price),
            });
        } else if (form.items.length > 0) {
            form.items.forEach((el) => {
                if (e.name === el.name) {
                    el.quantity += 1;
                }
                if (e.name !== el.name) {
                    form.items.push({
                        name: e.name,
                        quantity: 1,
                        price: Number(e.price),
                    });
                }
            });
        }
    }
    const uniqueItems = Array.from(new Set(form.items.map((a) => a.name))).map(
        (name) => {
            return form.items.find((a) => a.name === name);
        }
    );
    form.items = uniqueItems;
};

const addItem = (e) => {
    form.items.filter((el) => {
        if (el.name == e.name) {
            el.quantity = e.quantity + 1;
            form.total = form.total + Number(e.price);
        }
    });
};

const removeItem = (e) => {
    form.items.filter((el) => {
        if (el.name == e.name) {
            el.quantity = e.quantity - 1;
            form.total =
                form.total - Number(e.price) < 0
                    ? 0
                    : form.total - Number(e.price);
        }
    });
    if (e.quantity < 1) {
        disable.value = false;
        const filteredItem = form.items.filter((item) => item.name !== e.name);
        form.items = filteredItem;
    }
};

const submit = () => {
    form.change = form.tendered_amount - form.total;
    form.post("/sales", {
        preserveScroll: true,
        onSuccess: () => {
            showCreateSaleModal.value = false;
            form.reset();
            notification.success({
                message: "Product ordered Successfully",
            });
        },
    });
};

const handleCancel = () => {
    form.value = {};
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
                <div class="mb-5 text-center">Sales</div>
                <div class="mb-5">
                    <a-button
                        type="primary"
                        @click="showCreateSaleModal = true"
                    >
                        Create
                    </a-button>
                </div>
                <a-table :columns="columns" :data-source="props.sales">
                    <template #bodyCell="{ column, text, record }">
                        <template v-if="column.dataIndex === 'data'">
                            <div v-for="item in record">
                                <a>{{ record.name }}</a>
                            </div>
                        </template>
                        <template v-if="column.dataIndex === 'created_at'">
                            <span>{{ props.date }}</span>
                        </template>
                    </template>
                </a-table>
            </div>
        </div>
        <a-modal
            v-model:visible="showCreateSaleModal"
            title="Create Sale"
            @ok="handleAddSale"
            :afterClose="handleCancel"
            width="50%"
            :footer="null"
            :closable="true"
            :maskClosable="false"
        >
            <div class="flex justify-between">
                <a-card style="width: 400px">
                    <div class="grid grid-cols-3 gap-2">
                        <div
                            v-for="(product, index) in props.products"
                            :key="index"
                        >
                            <a-button
                                v-if="product.stock > 0"
                                @click="handleAddProduct(product, index)"
                                type="primary"
                                shape="round"
                                :disabled="
                                    disable && productId === index
                                        ? true
                                        : false
                                "
                                class="cursor-pointer"
                            >
                                {{ product.name }}
                            </a-button>
                            <a-button
                                v-else
                                @click="handleAddProduct(product)"
                                type="primary"
                                danger
                                shape="round"
                                class="cursor-pointer"
                            >
                                {{ product.name }}
                            </a-button>
                        </div>
                    </div>
                </a-card>
                <a-card class="overflow-y-scroll" style="width: 350px">
                    <div v-for="item in form.items">
                        <div
                            v-if="item.quantity > 0"
                            class="mb-2 overflow-y-auto"
                        >
                            <div class="flex justify-between">
                                <span class="w-1/4">{{ item.name }}</span>
                                <div class="flex space-x-1">
                                    <a-button
                                        @click="addItem(item)"
                                        class="font-bold cursor-pointer"
                                        >+</a-button
                                    >
                                    <a-button
                                        @click="removeItem(item)"
                                        class="font-bold cursor-pointer"
                                        >-</a-button
                                    >
                                </div>
                                <div class="flex justify-end">
                                    <span>quantity: {{ item.quantity }} </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </a-card>
                <div class="w-auto flex justify-between">
                    <div class="w-full my-auto">
                        Total Amount: {{ form.total }}
                    </div>
                </div>
            </div>
            <div class="flex justify-end mt-4">
                <a-button type="primary" @click="submit">submit</a-button>
            </div>
        </a-modal>
    </AuthenticatedLayout>
</template>
