<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm, Head } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";
import { message, notification } from "ant-design-vue";
import {
    SettingOutlined,
    EditOutlined,
    EllipsisOutlined,
    PlusCircleOutlined,
} from "@ant-design/icons-vue";
import axios from "axios";

//Variables
const props = defineProps({
    products: Array,
    sales: Array,
    date: String,
    items: Array,
    user: String,
    categories: Array,
    errors: Array,
});

const showReceiptModal = ref(false);
const quantity = ref(0);
const isOutOfStock = ref(false);
const filterByCategory = ref("all");
const tempArray = ref([]);
const search = ref("");
const receiptData = ref({
    items: [],
    total: 0,
    tendered_amount: 0,
    change: 0,
    client_name: null,
    processed_by: props.user.name,
});

const columns = [
    {
        title: "Qty",
        dataIndex: "qty",
        key: "qty",
    },
    {
        title: "Product",
        dataIndex: "product",
        key: "product",
    },
    {
        title: "Total",
        dataIndex: "total",
        key: "total",
    },
    {
        title: "",
        class: "w-1 text-center",
        dataIndex: "actions",
        key: "actions",
    },
];

const form = useForm({
    items: [],
    total: 0,
    tendered_amount: 0,
    change: 0,
    client_name: null,
    processed_by: props.user.name,
});

onMounted(() => {
    tempArray.value = props.products;
    handleChange();
});

const handleAddProduct = (e) => {
    if (e.stock < 1) {
        notification["error"]({
            description: `The item ${e.name} is currently out of stock.`,
        });
    }
};

const addItem = (e) => {
    props.products.filter((val) => {
        if (val.name === e.name) {
            if (val.stock <= quantity.value) {
                return message.error("Out of Stock");
            } else {
                if (form.items.length == 0) {
                    form.total = form.total + Number(e.price);
                    form.items.push({
                        name: e.name,
                        quantity: 1,
                        price: Number(e.price),
                        id: e.id,
                        stock: val.stock,
                        total_sale: e.total_sale,
                    });
                    notification["success"]({
                        message: `${e.name} ₱${e.price}`,
                        description: `You added ${e.name} in the list.`,
                    });
                } else if (form.items.length > 0) {
                    form.items.forEach((el) => {
                        if (e.name === el.name && el.quantity < val.stock) {
                            form.total = form.total + Number(el.price);
                            el.quantity += 1;
                            notification["success"]({
                                message: `${e.name} ₱${e.price}`,
                                description: `You added ${e.name} in the list.`,
                            });
                        }
                        if (el.quantity + 1 > val.stock) {
                            return message.error("Out of Stock");
                        }
                    });
                    let temp = form.items.some((item) => e.name == item.name);
                    if (!temp) {
                        form.total = form.total + Number(e.price);
                        form.items.push({
                            name: e.name,
                            quantity: 1,
                            price: Number(e.price),
                            id: e.id,
                            stock: val.stock,
                            total_sale: e.total_sale,
                        });
                        notification["success"]({
                            message: `${e.name} ₱${e.price}`,
                            description: `You added ${e.name} in the list.`,
                        });
                    }
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
    if (e.quantity > 1) {
        let temp = form.items.findIndex((item) => {
            return item.name == e.name;
        });
        let items = {
            id: e.id,
            name: e.name,
            price: e.price,
            quantity: e.quantity - 1,
            stock: e.stock,
            total_sale: e.total_sale,
        };

        form.items[temp] = items;
    }
    if (e.quantity === 1) {
        form.items = form.items.filter((el) => el.name != e.name);
    }
    form.total = form.total - e.price;
};

const submit = () => {
    receiptData.value.client_name = form.client_name;
    receiptData.value.items = form.items;
    receiptData.value.total = form.total;
    receiptData.value.tendered_amount = form.tendered_amount;
    receiptData.value.change = form.change;
    receiptData.value.processed_by = form.processed_by;
    form.change = form.tendered_amount - form.total;
    form.post("/sales", {
        preserveScroll: true,
        onSuccess: () => {
            showReceiptModal.value = true;
            form.reset();
            axios.get("/products");
            notification.success({
                message: "Product ordered Successfully",
            });
        },
        onError: () => {
            notification.warning({
                message: "Some fields don't have data!",
            });
        },
    });
};

const handleCancel = () => {
    form.value = {};
};

const handleChange = () => {
    if (filterByCategory.value == "all") {
        tempArray.value = props.products;
    } else {
        if (filterByCategory.value) {
            tempArray.value = props.products.filter(
                (el) => el.category === filterByCategory.value
            );
        } else {
            tempArray.value = props.products;
        }
    }
};

const onSearch = () => {
    if (search.value) {
        tempArray.value = props.products.filter(
            (el) =>
                el.name.toLocaleLowerCase() == search.value.toLocaleLowerCase()
        );
        tempArray.value.filter(
            (val) => (filterByCategory.value = val.category)
        );
    }
    if (search.value == "") {
        filterByCategory.value = "all";
        handleChange();
    }
};
const printReceipt = () => {
    let printableContent = document.getElementById("printable-content");
    let printWindow = window.open("", "", "height=1000,width=1000");
    printWindow.document.write(printableContent.innerHTML);
    printWindow.print();
};
</script>

<template>
    <Head title="Sales" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Sales
            </h2>
        </template>
        <div class="py-12 min-h-screen">
            <div class="mb-5 text-center page-title">Sales</div>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="w-1/4 mx-4 my-2">
                    <a-input-search
                        v-model:value="search"
                        placeholder="Search . . ."
                        style="width: 200px"
                        @search="onSearch"
                        allow-clear
                    />
                </div>
                <div class="lg:flex space-x-4">
                    <div
                        class="mx-4 overflow-y-auto max-h-[45vh] bg-white w-full"
                    >
                        <a-tabs
                            @change="handleChange()"
                            class="mt-4"
                            type="card"
                            v-model:activeKey="filterByCategory"
                        >
                            <a-tab-pane key="all" tab="All">
                                <div
                                    class="overflow-y-auto grid grid-cols-3 gap-2"
                                >
                                    <div
                                        v-for="(x, index) in tempArray"
                                        :key="index"
                                        class="w-auto pb-4"
                                    >
                                        <a-card
                                            class="w-auto"
                                            :bodyStyle="{
                                                backgroundColor: '#f1f5f9',
                                            }"
                                        >
                                            <template #actions>
                                                <div v-if="x.stock > 0">
                                                    <plus-circle-outlined
                                                        key="setting"
                                                        @click="addItem(x)"
                                                    />
                                                </div>
                                                <div
                                                    v-else
                                                    class="text-red-400"
                                                    @click="addItem(x)"
                                                >
                                                    Out of Stock
                                                </div>
                                            </template>
                                            <a-card-meta
                                                :title="x.name"
                                                :description="`₱${x.price}`"
                                            >
                                                <template #avatar>
                                                    <a-avatar
                                                        src="STICKY2.png"
                                                    />
                                                </template>
                                            </a-card-meta>
                                        </a-card>
                                    </div>
                                </div>
                            </a-tab-pane>
                            <a-tab-pane
                                v-for="(item, index) in props.categories"
                                :key="item.name"
                                :tab="item.name"
                            >
                                <div class="grid grid-cols-3 gap-2">
                                    <div
                                        v-for="(product, index) in tempArray"
                                        :key="index"
                                        class="with-auto pb-4"

                                    >
                                        <a-card
                                            class="w-[190px]"
                                            :bodyStyle="{
                                                backgroundColor: '#f1f5f9',
                                            }"
                                        >
                                            <template #actions>
                                                <div v-if="product.stock > 0">
                                                    <plus-circle-outlined
                                                        key="setting"
                                                        @click="
                                                            addItem(product)
                                                        "
                                                    />
                                                </div>
                                                <div
                                                    v-else
                                                    class="text-red-400"
                                                    @click="addItem(product)"
                                                >
                                                    Out of Stock
                                                </div>
                                            </template>
                                            <a-card-meta
                                                :title="product.name"
                                                :description="`₱${product.price}`"
                                            >
                                                <template #avatar>
                                                    <a-avatar
                                                        src="STICKY2.png"
                                                    />
                                                </template>
                                            </a-card-meta>
                                        </a-card>
                                    </div>
                                </div>
                            </a-tab-pane>
                        </a-tabs>
                    </div>
                    <div class="mx-4 bg-white w-1/2 min-h-20vh">
                        <div class="my-2 text-center">
                            <h4>Orders</h4>
                        </div>
                        <a-table
                            bordered
                            :columns="columns"
                            :data-source="form.items"
                            :pagination="false"
                            class="overflow-y-auto"
                        >
                            <template #bodyCell="{ column, record }">
                                <template v-if="column.key === 'qty'">
                                    {{ record.quantity }}
                                </template>
                                <template v-if="column.key === 'product'">
                                    <div>
                                        <div class="w-full">
                                            {{ record.name }}
                                        </div>
                                        <div>₱{{ record.price }}</div>
                                    </div>
                                </template>
                                <template v-if="column.key === 'total'">
                                    {{
                                        new Intl.NumberFormat("PHP", {
                                            style: "currency",
                                            currency: "PHP",
                                        }).format(
                                            record.quantity * record.price
                                        )
                                    }}
                                </template>
                                <template v-if="column.key === 'actions'">
                                    <span
                                        @click="removeItem(record)"
                                        class="text-red-500 cursor-pointer"
                                        >x</span
                                    >
                                </template>
                            </template>
                        </a-table>
                    </div>
                </div>
                <div v-if="form.total > 0" class="my-4 ml-4">
                    <div class="px-4 bg-white">
                        <a-form :model="form" name="basic" autocomplete="off">
                            <div class="pt-4 items-center">
                                <a-form-item
                                    required
                                    label="Client Name"
                                    name="client_name"
                                >
                                    <div class="w-auto">
                                        <a-input
                                            v-model:value="form.client_name"
                                        />
                                        <span
                                            class="text-red-400 italic"
                                            v-if="errors"
                                            >{{ errors.client_name }}</span
                                        >
                                    </div>
                                </a-form-item>
                                <a-form-item
                                    required
                                    label="Tendered"
                                    name="tendered_amount"
                                >
                                    <div class="ml-5 w-auto">
                                        <a-input
                                            v-model:value="form.tendered_amount"
                                        />
                                        <span
                                            class="text-red-400 italic"
                                            v-if="errors"
                                            >{{ errors.tendered_amount }}</span
                                        >
                                    </div>
                                </a-form-item>
                                <div class="my-5">
                                    Total:
                                    {{
                                        new Intl.NumberFormat("PHP", {
                                            style: "currency",
                                            currency: "PHP",
                                        }).format(form.total)
                                    }}
                                </div>
                                <span>
                                    change:
                                    {{
                                        form.tendered_amount > 0
                                            ? new Intl.NumberFormat("PHP", {
                                                  style: "currency",
                                                  currency: "PHP",
                                              }).format(
                                                  form.tendered_amount -
                                                      form.total
                                              )
                                            : new Intl.NumberFormat("PHP", {
                                                  style: "currency",
                                                  currency: "PHP",
                                              }).format(0)
                                    }}</span
                                >
                                <div class="mt-5">
                                    processed by: {{ props.user.name }}
                                </div>
                            </div>
                            <div class="flex justify-end py-4">
                                <a-button type="primary" @click="submit()"
                                    >Continue</a-button
                                >
                            </div>
                        </a-form>
                    </div>
                </div>
            </div>
        </div>
        <a-modal
            v-model:visible="showReceiptModal"
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
                <div id="printable-content">
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
                                                                                        Client:
                                                                                        {{
                                                                                            receiptData.client_name
                                                                                        }}
                                                                                        <br />
                                                                                        Casher:
                                                                                        {{
                                                                                            props
                                                                                                .user
                                                                                                .name
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
                                                                                                <tr
                                                                                                    v-for="(
                                                                                                        item,
                                                                                                        index
                                                                                                    ) in receiptData.items"
                                                                                                    :key="
                                                                                                        index
                                                                                                    "
                                                                                                >
                                                                                                    <td>
                                                                                                        {{
                                                                                                            item.name
                                                                                                        }}
                                                                                                    </td>
                                                                                                    <td
                                                                                                        class="alignright"
                                                                                                    >
                                                                                                        x{{
                                                                                                            item.quantity
                                                                                                        }}
                                                                                                        ₱{{
                                                                                                            item.price
                                                                                                        }}
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
                                                                                                        ₱{{
                                                                                                            receiptData.total
                                                                                                        }}
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr
                                                                                                    class="total"
                                                                                                >
                                                                                                    <td
                                                                                                        class="alignright"
                                                                                                        width="80%"
                                                                                                    >
                                                                                                        Cash
                                                                                                    </td>
                                                                                                    <td
                                                                                                        class="alignright"
                                                                                                    >
                                                                                                        ₱{{
                                                                                                            receiptData.tendered_amount
                                                                                                        }}
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr
                                                                                                    class="total"
                                                                                                >
                                                                                                    <td
                                                                                                        class="alignright"
                                                                                                        width="80%"
                                                                                                    >
                                                                                                        Change
                                                                                                    </td>
                                                                                                    <td
                                                                                                        class="alignright"
                                                                                                    >
                                                                                                        ₱{{
                                                                                                            receiptData.tendered_amount -
                                                                                                            receiptData.total
                                                                                                        }}
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
            <div class="flex justify-center mt-5">
                <div
                    class="flex bg-blue-200 rounded-lg px-2 py-2 hover:cursor-pointer"
                    @click="printReceipt()"
                >
                    <img src="/printer.png" class="w-[30px] h-[34px]" />
                    <span class="pt-1.5 text-black">Print Receipt</span>
                </div>
            </div>
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

>>> .ant-btn-primary {
    color: #fff;
    border-color: #8b5cf6;
    background: #8b5cf6;
    text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.12);
    box-shadow: 0 2px 0 rgba(0, 0, 0, 0.045);
}

>>> .ant-btn-primary:hover {
    color: #fff;
    border-color: #6d28d9;
    background: #6d28d9;
    text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.12);
    box-shadow: 0 2px 0 rgba(0, 0, 0, 0.045);
}

>>> .ant-tabs .ant-tabs-nav .ant-tabs-nav-wrap {
    margin-top: 1rem;
    margin-left: 1rem;
}

>>> .ant-tabs-tabpane {
    flex: none;
    width: 100%;
    outline: none;
    margin-left: 1rem;
}
>>> .ant-tabs-tab.ant-tabs-tab-active .ant-tabs-tab-btn {
    color: #8b5cf6;
    text-shadow: 0 0 0.25px currentcolor;
}
.page-title {
    font-weight: 400 !important;
    font-size: 20px !important;
    line-height: 23px !important;
    color: #000000 !important;
}
</style>
