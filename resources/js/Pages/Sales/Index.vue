<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm, Head } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";
import { message, notification } from "ant-design-vue";
import { formatStrategyValues } from "ant-design-vue/lib/vc-tree-select/utils/strategyUtil";
import moment from "moment";

//Variables
const props = defineProps({
    products: Array,
    sales: Array,
    date: String,
    items: Array,
    user: String,
    categories: Array,
});

const showCreateSaleModal = ref(false);
const showContinueModal = ref(false);
const showReceiptModal = ref(false);
const quantity = ref(0);
const isOutOfStock = ref(false);
const filterByCategory = ref("");
const tempArray = ref([]);

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
        title: "Tendered Amount",
        dataIndex: "tendered_amount",
        key: "tendered_amount",
    },
    {
        title: "Action",
        key: "action",
    },
];

const form = useForm({
    items: [],
    total: 0,
    tendered_amount: 1000,
    change: null,
    client_name: null,
    processed_by: props.user,
});

onMounted(() => {
    tempArray.value = props.products;
});

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
            quantity.value = el.quantity;
        }
    });
    props.products.filter((el) => {
        if (el.name === e.name) {
            if (el.stock < quantity.value) {
                isOutOfStock.value = true;
            } else {
                isOutOfStock.value = false;
            }
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
            props.products.filter((i) => {
                if (i.name === el.name) {
                    if (i.stock >= el.quantity) {
                        console.log("test");
                        isOutOfStock.value = false;
                    }
                }
            });
        }
    });
};

const submit = () => {
    form.change = form.tendered_amount - form.total;
    form.post("/sales", {
        preserveScroll: true,
        onSuccess: () => {
            showContinueModal.value = false;
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

const handleContinue = () => {
    showCreateSaleModal.value = false;
    showContinueModal.value = true;
};

const handleReceiptModal = () => {
    showReceiptModal.value = true;
};

const handleChange = () => {
    if (filterByCategory.value) {
        tempArray.value = props.products.filter(
            (el) => el.category === filterByCategory.value
        );
    } else {
        tempArray.value = props.products;
    }
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
                            <div>
                                <a>{{ record.data }}</a>
                            </div>
                        </template>
                        <template v-if="column.dataIndex === 'created_at'">
                            <span>{{ props.date }}</span>
                        </template>
                        <template v-if="column.key === 'action'">
                            <a-button @click="handleReceiptModal" type="primary"
                                >view</a-button
                            >
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
            <div class="flex justify-start w-1/2">
                <div class="w-full">
                    <a-form
                        :model="form"
                        name="basic"
                        :label-col="{ span: 5 }"
                        :wrapper-col="{ span: 16 }"
                        autocomplete="off"
                        @finish="onFinish"
                        @finishFailed="onFinishFailed"
                    >
                        <a-form-item label="Filter by" name="categories">
                            <a-select
                                ref="select"
                                @change="handleChange()"
                                v-model:value="filterByCategory"
                                :allowClear="true"
                            >
                                <a-select-option
                                    v-for="(item, index) in props.categories"
                                    :key="index"
                                    :value="item.name"
                                >
                                    {{ item.name }}
                                </a-select-option>
                            </a-select>
                        </a-form-item>
                    </a-form>
                </div>
            </div>
            <div class="flex justify-between">
                <a-card style="width: 400px">
                    <div class="grid grid-cols-3 gap-2">
                        <div v-for="(product, index) in tempArray" :key="index">
                            <a-button
                                v-if="product.stock > 0"
                                @click="handleAddProduct(product, index)"
                                type="primary"
                                shape="round"
                                :disabled="isOutOfStock ? true : false"
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
                <a-button
                    :disabled="form.items.length === 0 ? true : false"
                    type="primary"
                    @click="handleContinue"
                    >Continue</a-button
                >
            </div>
        </a-modal>
        <a-modal
            v-model:visible="showContinueModal"
            title="Order"
            @ok="handleAddSale"
            :afterClose="handleCancel"
            width="50%"
            height="50%"
            :footer="null"
            :closable="true"
            :maskClosable="false"
        >
            <a-form
                :model="formState"
                name="basic"
                :label-col="{ span: 5 }"
                :wrapper-col="{ span: 16 }"
                autocomplete="off"
            >
                <div class="mx-auto items-center">
                    <div class="w-1/2">
                        <a-form-item label="Client Name" name="client_name">
                            <a-input v-model:value="form.client_name" />
                        </a-form-item>
                    </div>
                    <div class="mt-5">
                        List of Orders:
                        <div
                            class="ml-5"
                            v-for="(item, index) in form.items"
                            :key="index"
                        >
                            <ul>
                                <li>{{ item.name }}</li>
                            </ul>
                        </div>
                        Total Amount: {{ form.total }}
                    </div>
                    <div class="mt-5">processed by: {{ props.user }}</div>
                </div>
                <div class="flex justify-end mt-4">
                    <a-button type="primary" @click="submit">Submit</a-button>
                </div>
            </a-form>
        </a-modal>
        <a-modal
            v-model:visible="showReceiptModal"
            title="Order"
            :afterClose="handleCancel"
            width="50%"
            height="50%"
            :footer="null"
            :closable="true"
            :maskClosable="false"
        >
            <a-form
                :model="formState"
                name="basic"
                :label-col="{ span: 5 }"
                :wrapper-col="{ span: 16 }"
                autocomplete="off"
            >
                <div class="mx-auto items-center">
                    {{ props.sales }}
                </div>
            </a-form>
        </a-modal>
    </AuthenticatedLayout>
</template>
