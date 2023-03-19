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
const receipt = ref([]);

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
        title: "Action",
        key: "action",
    },
];

const form = useForm({
    items: [],
    total: 0,
    tendered_amount: 0,
    change: 0,
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
    console.log(e);
    // form.items.name = e.name;
    // form.items.quantity = form.items.quantity + 1
    // form.items.price = Number(e.price)
    // form.total = form.total + Number(e.price);
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
    console.log(e);
    console.log(form.items);
    props.products.filter((val) => {
        if (val.name === e.name) {
            if (val.stock <= quantity.value) {
                alert("out of stock");
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
        }
    });
    const uniqueItems = Array.from(new Set(form.items.map((a) => a.name))).map(
        (name) => {
            return form.items.find((a) => a.name === name);
        }
    );
    form.items = uniqueItems;
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

const handleReceiptModal = (record) => {
    console.log(record);
    showReceiptModal.value = true;
    receipt.value = record;
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
                            <a-button
                                @click="handleReceiptModal(record)"
                                type="primary"
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
                                @click="addItem(product)"
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
                        <a-form-item label="Tendered" name="tendered_amount">
                            <a-input v-model:value="form.tendered_amount" />
                        </a-form-item>
                        <span> change: {{ form.change }}</span>
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
                <div>
                    <!-- <div v-for="(item, index) in receipt" :key="index" class="mx-auto items-center"> -->
                    <table class="body-wrap">
                        <tbody>
                            <tr>
                                <td></td>
                                <td class="container" width="600">
                                    <div class="content">
                                        <table
                                            class="main"
                                            width="100%"
                                            cellpadding="0"
                                            cellspacing="0"
                                        >
                                            <tbody>
                                                <tr>
                                                    <td
                                                        class="content-wrap aligncenter"
                                                    >
                                                        <table
                                                            width="100%"
                                                            cellpadding="0"
                                                            cellspacing="0"
                                                        >
                                                            <tbody>
                                                                <tr>
                                                                    <td
                                                                        class="content-block"
                                                                    >
                                                                        <h2>
                                                                            Sticky
                                                                            Buns
                                                                            -
                                                                            Main
                                                                            Branch
                                                                        </h2>
                                                                        <h4>
                                                                            Canhaway
                                                                            ,
                                                                            Guindulman,
                                                                            Bohol
                                                                        </h4>
                                                                        <h4>
                                                                            Contact
                                                                            No.
                                                                            09989864912
                                                                            /
                                                                            09176236594
                                                                        </h4>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td
                                                                        class="content-block"
                                                                    >
                                                                        <table
                                                                            class="invoice"
                                                                        >
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td>
                                                                                        Cient
                                                                                        name:
                                                                                        {{
                                                                                            receipt.client_name
                                                                                        }}<br />{{
                                                                                            receipt.created_at
                                                                                        }}
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>
                                                                                        <table
                                                                                            class="invoice-items"
                                                                                            cellpadding="0"
                                                                                            cellspacing="0"
                                                                                        >
                                                                                            <tbody>
                                                                                                <tr>
                                                                                                    <td>
                                                                                                        Service
                                                                                                        1
                                                                                                    </td>
                                                                                                    <td
                                                                                                        class="alignright"
                                                                                                    >
                                                                                                        {{
                                                                                                            receipt.data
                                                                                                        }}
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td>
                                                                                                        Service
                                                                                                        2
                                                                                                    </td>
                                                                                                    <td
                                                                                                        class="alignright"
                                                                                                    >
                                                                                                        $
                                                                                                        10.00
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td>
                                                                                                        Service
                                                                                                        3
                                                                                                    </td>
                                                                                                    <td
                                                                                                        class="alignright"
                                                                                                    >
                                                                                                        $
                                                                                                        6.00
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr
                                                                                                    class="total"
                                                                                                >
                                                                                                    <td
                                                                                                        class="alignright"
                                                                                                        width="80%"
                                                                                                    >
                                                                                                        Total
                                                                                                    </td>
                                                                                                    <td
                                                                                                        class="alignright"
                                                                                                    >
                                                                                                        $
                                                                                                        36.00
                                                                                                    </td>
                                                                                                </tr>
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td
                                                                        class="content-block"
                                                                    >
                                                                        Thank
                                                                        you for
                                                                        purchase.
                                                                        Keep
                                                                        Safe and
                                                                        God
                                                                        Bless !
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div class="footer">
                                            <table width="100%">
                                                <tbody>
                                                    <tr>
                                                        <td
                                                            class="aligncenter content-block"
                                                        >
                                                            THIS IS NOT AN
                                                            OFFICIAL RECEIPT
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </a-form>
        </a-modal>
    </AuthenticatedLayout>
</template>
<style scoped>
table td {
    vertical-align: top;
}

/* -------------------------------------
    BODY & CONTAINER
------------------------------------- */
body {
    background-color: #f6f6f6;
}

.body-wrap {
    background-color: #f6f6f6;
    width: 100%;
}

.container {
    display: block !important;
    max-width: 600px !important;
    margin: 0 auto !important;
    /* makes it centered */
    clear: both !important;
}

.content {
    max-width: 600px;
    margin: 0 auto;
    display: block;
    padding: 20px;
}

/* -------------------------------------
    HEADER, FOOTER, MAIN
------------------------------------- */
.main {
    background: #fff;
    border: 1px solid #e9e9e9;
    border-radius: 3px;
}

.content-wrap {
    padding: 20px;
}

.content-block {
    padding: 0 0 20px;
}

.header {
    width: 100%;
    margin-bottom: 20px;
}

.footer {
    width: 100%;
    clear: both;
    color: #999;
    padding: 20px;
}
.footer a {
    color: #999;
}
.footer p,
.footer a,
.footer unsubscribe,
.footer td {
    font-size: 12px;
}

/* -------------------------------------
    TYPOGRAPHY
------------------------------------- */
h1,
h2,
h3 {
    font-family: "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif;
    color: #000;
    margin: 40px 0 0;
    line-height: 1.2;
    font-weight: 400;
}

h1 {
    font-size: 32px;
    font-weight: 500;
}

h2 {
    font-size: 24px;
}

h3 {
    font-size: 18px;
}

h4 {
    font-size: 14px;
    font-weight: 600;
}

p,
ul,
ol {
    margin-bottom: 10px;
    font-weight: normal;
}
p li,
ul li,
ol li {
    margin-left: 5px;
    list-style-position: inside;
}

/* -------------------------------------
    LINKS & BUTTONS
------------------------------------- */
a {
    color: #1ab394;
    text-decoration: underline;
}

.btn-primary {
    text-decoration: none;
    color: #fff;
    background-color: #1ab394;
    border: solid #1ab394;
    border-width: 5px 10px;
    line-height: 2;
    font-weight: bold;
    text-align: center;
    cursor: pointer;
    display: inline-block;
    border-radius: 5px;
    text-transform: capitalize;
}

/* -------------------------------------
    OTHER STYLES THAT MIGHT BE USEFUL
------------------------------------- */
.last {
    margin-bottom: 0;
}

.first {
    margin-top: 0;
}

.aligncenter {
    text-align: center;
}

.alignright {
    text-align: right;
}

.alignleft {
    text-align: left;
}

.clear {
    clear: both;
}

/* -------------------------------------
    ALERTS
    Change the class depending on warning email, good email or bad email
------------------------------------- */
.alert {
    font-size: 16px;
    color: #fff;
    font-weight: 500;
    padding: 20px;
    text-align: center;
    border-radius: 3px 3px 0 0;
}
.alert a {
    color: #fff;
    text-decoration: none;
    font-weight: 500;
    font-size: 16px;
}
.alert.alert-warning {
    background: #f8ac59;
}
.alert.alert-bad {
    background: #ed5565;
}
.alert.alert-good {
    background: #1ab394;
}

/* -------------------------------------
    INVOICE
    Styles for the billing table
------------------------------------- */
.invoice {
    margin: 40px auto;
    text-align: left;
    width: 80%;
}
.invoice td {
    padding: 5px 0;
}
.invoice .invoice-items {
    width: 100%;
}
.invoice .invoice-items td {
    border-top: #eee 1px solid;
}
.invoice .invoice-items .total td {
    border-top: 2px solid #333;
    border-bottom: 2px solid #333;
    font-weight: 700;
}

/* -------------------------------------
    RESPONSIVE AND MOBILE FRIENDLY STYLES
------------------------------------- */
@media only screen and (max-width: 640px) {
    h1,
    h2,
    h3,
    h4 {
        font-weight: 600 !important;
        margin: 20px 0 5px !important;
    }

    h1 {
        font-size: 22px !important;
    }

    h2 {
        font-size: 18px !important;
    }

    h3 {
        font-size: 16px !important;
    }

    .container {
        width: 100% !important;
    }

    .content,
    .content-wrap {
        padding: 10px !important;
    }

    .invoice {
        width: 100% !important;
    }
}
</style>
