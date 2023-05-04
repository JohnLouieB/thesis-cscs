<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm, Head } from "@inertiajs/vue3";
import { ref, watch, onMounted } from "vue";
import { message, notification } from "ant-design-vue";
import moment from "moment";
import axios from "axios";

//Variables
const props = defineProps({
    reports: Array,
    date: String,
});

const showReceiptModal = ref(false);
const showReportModal = ref(false);
const receiptData = ref([]);
const sourceData = ref([]);
const searchUser = ref("All");
const users = ref([]);
const total = ref(0);
const generateReport = ref("");
const generate = ref("");
const loading = ref(false);
const canPrint = ref(false);
const dateReport = ref("");

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
        class: "w-1 text-center",
        key: "action",
    },
];

onMounted(() => {
    sourceData.value = props.reports;
    getData();
    getUsers();
});

const handleCancel = () => {
    showReceiptModal.value = false;
    showReportModal.value = false;
};

const handleViewReceipt = (val) => {
    receiptData.value = val;
    showReceiptModal.value = true;
};

const getData = () => {
    loading.value = true;
    axios
        .get("/api/reports/sales", {
            params: {
                searchByClient:
                    searchUser.value == "All" ? "" : searchUser.value,
                generate: generate.value,
                generateReport: generateReport.value,
            },
        })
        .then((res) => {
            sourceData.value = res.data.report;
            dateReport.value = res.data.date;
            console.log(res);
            if (generate.value) {
                canPrint.value = true;
            } else {
                canPrint.value = false;
            }
            sourceData.value.filter((e) => {
                total.value = total.value + Number(e.total);
            });
        })
        .finally(() => {
            loading.value = false;
        });
};

const formatDate = (date) => {
    const newDate = new Date(date);
    const options = { year: "numeric", month: "long", day: "numeric" };
    return new Intl.DateTimeFormat("en-US", options).format(newDate);
};

const onSearch = () => {
    total.value = 0;
    getData();
};

const viewReport = () => {
    console.log(generate.value);
    showReportModal.value = true;
};

const getUsers = () => {
    axios.get("/api/get-users").then((res) => {
        users.value = res.data;
    });
};
const printReceipt = () => {
    let printableContent = document.getElementById("printable-content");
    let printWindow = window.open("", "", "height=1000,width=1000");
    printWindow.document.write(printableContent.innerHTML);
    printWindow.print();
};
</script>

<template>
    <Head title="Daily Sales Report" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Daily Sales Report
            </h2>
        </template>

        <div class="py-12 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-5 text-center page-title">
                    Daily Sales Report
                </div>
                <div class="flex space-x-2 my-2">
                    <div class="w-1/4">
                        <a-select
                            v-model:value="searchUser"
                            class="w-full"
                            @change="onSearch"
                        >
                            <a-select-option value="All"> All </a-select-option>
                            <a-select-option
                                v-for="(user, index) in users"
                                :key="index"
                                :value="user.name"
                                >{{ user.name }}</a-select-option
                            >
                        </a-select>
                    </div>
                    <div class="w-1/2">
                        <a-button :disabled="!canPrint" @click="viewReport()"
                            >View Report</a-button
                        >
                    </div>
                    <div class="flex justify-end w-full">
                        <div>
                            <div class="w-auto">
                                <label class="text-[15px] mr-2"
                                    >Generate Report:</label
                                >
                                <a-select
                                    v-model:value="generateReport"
                                    style="width: 200px"
                                    allow-clear
                                >
                                    <a-select-option value="daily">
                                        Generate Daily Report
                                    </a-select-option>
                                    <a-select-option value="weekly"
                                        >Generate Weekly Report</a-select-option
                                    >
                                    <a-select-option value="monthly"
                                        >Generate Monthly
                                        Report</a-select-option
                                    >
                                </a-select>
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    v-if="
                        generateReport == 'weekly' ||
                        generateReport == 'monthly' ||
                        generateReport == 'daily'
                    "
                    class="float-right w-auto py-2"
                >
                    <div v-if="generateReport == 'daily'">
                        <label class="text-[15px] mr-2">Select Date:</label>
                        <a-date-picker
                            @change="onSearch"
                            v-model:value="generate"
                            :bordered="true"
                        />
                    </div>

                    <div v-if="generateReport == 'weekly'">
                        <label class="text-[15px] mr-2"
                            >Select Week Range:</label
                        >
                        <a-range-picker
                            v-model:value="generate"
                            picker="week"
                            @change="onSearch"
                        />
                    </div>
                    <div v-if="generateReport == 'monthly'">
                        <label class="text-[15px] mr-2">Select Month:</label>
                        <a-date-picker
                            v-model:value="generate"
                            picker="month"
                            @change="onSearch"
                        />
                    </div>
                </div>
                <a-table
                    :loading="loading"
                    :columns="columns"
                    :data-source="sourceData"
                >
                    <template #bodyCell="{ column, text, record }">
                        <template v-if="column.key === 'total'">
                            <div>
                                {{
                                    new Intl.NumberFormat("PHP", {
                                        style: "currency",
                                        currency: "PHP",
                                    }).format(record.total)
                                }}
                            </div>
                        </template>
                        <template v-if="column.dataIndex === 'created_at'">
                            <span>{{ formatDate(record.created_at) }}</span>
                        </template>
                        <template v-if="column.key === 'action'">
                            <a-button
                                type="primary"
                                @click="handleViewReceipt(record)"
                                >View</a-button
                            >
                        </template>
                    </template>
                    <template #summary>
                        <a-table-summary-row>
                            <a-table-summary-cell>Total</a-table-summary-cell>
                            <a-table-summary-cell>
                                <a-typography-text>
                                    <span class="text-blue-600 font-bold"></span
                                ></a-typography-text>
                            </a-table-summary-cell>
                            <a-table-summary-cell>
                                <a-typography-text>
                                    <span class="text-blue-600 font-bold"></span
                                ></a-typography-text>
                            </a-table-summary-cell>
                            <a-table-summary-cell>
                                <a-typography-text>
                                    <span class="text-blue-600 font-bold"></span
                                ></a-typography-text>
                            </a-table-summary-cell>
                            <a-table-summary-cell>
                                <a-typography-text>
                                    <span class="text-blue-600 font-bold">
                                        {{
                                            new Intl.NumberFormat("PHP", {
                                                style: "currency",
                                                currency: "PHP",
                                            }).format(total)
                                        }}</span
                                    ></a-typography-text
                                >
                            </a-table-summary-cell>
                        </a-table-summary-row>
                    </template>
                </a-table>
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
                name="basic"
                :label-col="{ span: 5 }"
                :wrapper-col="{ span: 16 }"
                autocomplete="off"
            >
                <div>
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
                                                                                            receiptData.processed_by
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
                                                                                                        }}.00
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
                                                                                                        }}.00
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
                                                                                                        {{
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
                                                                                                        {{
                                                                                                            receiptData.change
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
        </a-modal>
        <a-modal
            v-model:visible="showReportModal"
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
                                                                        <!-- <h4>
                                                                            Contact
                                                                            No.
                                                                            09989864912
                                                                            /
                                                                            09176236594
                                                                        </h4> -->
                                                                        <h4>
                                                                            Date:
                                                                            {{
                                                                                new Date().getFullYear()
                                                                            }}/{{
                                                                                new Date().getMonth() +
                                                                                1
                                                                            }}/{{
                                                                                new Date().getDate()
                                                                            }}
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
                                                                                <tr
                                                                                    class="text-center"
                                                                                >
                                                                                    <td>
                                                                                        Sales
                                                                                        Report
                                                                                        for
                                                                                        the
                                                                                        {{
                                                                                            generateReport ==
                                                                                            "daily"
                                                                                                ? "Date of"
                                                                                                : generateReport ==
                                                                                                  "weekly"
                                                                                                ? "Week of"
                                                                                                : "Month of"
                                                                                        }}
                                                                                        {{
                                                                                            dateReport
                                                                                        }}
                                                                                        <br />
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td
                                                                                        class="text-center text-lg font-bold"
                                                                                    >
                                                                                        {{
                                                                                            new Intl.NumberFormat(
                                                                                                "PHP",
                                                                                                {
                                                                                                    style: "currency",
                                                                                                    currency:
                                                                                                        "PHP",
                                                                                                }
                                                                                            ).format(
                                                                                                total
                                                                                            )
                                                                                        }}
                                                                                    </td>
                                                                                </tr>
                                                                                <!-- <tr>
                                                                                    <td>
                                                                                        <table
                                                                                            class="invoice-items"
                                                                                            cellpadding="0"
                                                                                            cellspacing="0"
                                                                                        >
                                                                                            <tbody>
                                                                                                <tr>
                                                                                                    <td>
                                                                                                        test
                                                                                                    </td>
                                                                                                    <td
                                                                                                        class="alignright"
                                                                                                    >
                                                                                                        test
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
                                                                                                        test
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
                                                                                                        test
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
                                                                                                        test
                                                                                                    </td>
                                                                                                </tr>
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </td>
                                                                                </tr> -->
                                                                            </tbody>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                                <!-- <tr>
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
                                                                </tr> -->
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div class="footer flex justify-center">
                                            <div>
                                                <table width="100%">
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                @{{
                                                                    new Date().getFullYear()
                                                                }}
                                                                All Rights
                                                                Reserved™
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
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
.page-title {
    font-weight: 400 !important;
    font-size: 20px !important;
    line-height: 23px !important;
    color: #000000 !important;
}

>>> .ant-table-summary {
    position: relative;
    z-index: 2;
    background: #ddd6fe;
}
</style>
