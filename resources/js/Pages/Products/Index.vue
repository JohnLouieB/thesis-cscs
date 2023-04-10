<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm, Head } from "@inertiajs/vue3";
import { ref, watchEffect, onMounted } from "vue";
import { message, notification } from "ant-design-vue";
import axios from "axios";

//Variables
const props = defineProps({
    products: Array,
    categories: Array,
});

const showAddProductModal = ref(false);
const showEditProductModal = ref(false);
const loading = ref(false);
const currentUser = ref([]);
const productId = ref(null);

const form = useForm({
    category: null,
    name: null,
    description: null,
    price: null,
    stock: 0,
});

const columns = ref([
    {
        title: "Category",
        dataIndex: "category",
        key: "category",
    },
    {
        title: "Name",
        dataIndex: "name",
        key: "name",
    },
    {
        title: "Description",
        dataIndex: "description",
        key: "description",
    },
    {
        title: "Price",
        key: "price",
        dataIndex: "price",
    },
    {
        title: "Status",
        key: "stock",
        dataIndex: "stock",
    },
    {
        title: "Action",
        key: "action",
    },
]);

onMounted(() => {
    getUser();
});

const getUser = () => {
    axios.get("/current-user").then((res) => {
        currentUser.value = res.data;
        if (res.data.role == "cashier") {
            columns.value = columns.value.filter(
                (column) => column.title !== "Action"
            );
        }
    });
};

const handleAddProduct = () => {
    form.post("/products", {
        preserveScroll: true,
        onSuccess: () => {
            showAddProductModal.value = false;
            form.reset();
            notification.success({
                message: "Product Added Successfully",
            });
        },
    });
};

const handleCancel = () => {
    form.value = {};
};

const handleDelete = (id) => {
    form.delete(`/products/${id}`, {
        onSuccess: () => {
            notification.success({
                message: "Product Deleted Successfully",
            });
        },
    });
};

const editProductModal = (product) => {
    showEditProductModal.value = true;
    productId.value = product.id;
    form.category = product.category;
    form.name = product.name;
    form.description = product.description;
    form.price = product.price;
    form.stock = product.stock;
};

const updateProduct = () => {
    form.put(`/products/${productId.value}`, {
        preserveScroll: true,
        onSuccess: () => {
            showEditProductModal.value = false;
            form.reset();
            notification.success({
                message: "Product Updated Successfully",
            });
        },
    });
};
</script>

<template>
    <Head title="Product List" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Product List
            </h2>
        </template>

        <div class="py-12 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-5 text-center">Product list</div>
                <div v-if="currentUser.role == 'admin'" class="mb-5">
                    <a-button
                        type="primary"
                        @click="showAddProductModal = true"
                    >
                        Add product
                    </a-button>
                </div>
                <div>
                    <a-table
                        :loading="loading"
                        :columns="columns"
                        :data-source="props.products"
                    >
                        <template #headerCell="{ column }"> </template>

                        <template #bodyCell="{ column, record }">
                            <template v-if="column.key === 'category'">
                                <span>
                                    {{ record.category }}
                                </span>
                            </template>
                            <template v-if="column.key === 'name'">
                                <span>
                                    {{ record.name }}
                                </span>
                            </template>
                            <template v-else-if="column.key === 'description'">
                                <span>
                                    {{ record.description }}
                                </span>
                            </template>
                            <template v-else-if="column.key === 'price'">
                                <span>
                                    {{ record.price }}
                                </span>
                            </template>
                            <template v-else-if="column.key === 'stock'">
                                <span
                                    class="text-green-400"
                                    v-if="record.stock !== 0"
                                >
                                    Active
                                </span>
                                <span class="text-red-400" v-else>
                                    InActive
                                </span>
                            </template>
                            <template
                                v-else-if="
                                    column.key === 'action' &&
                                    currentUser.role == 'admin'
                                "
                            >
                                <a-popover title="Actions" trigger="click">
                                    <template #content>
                                        <div class="flex space-x-5">
                                            <a @click="editProductModal(record)"
                                                >Update</a
                                            >
                                            <a-popconfirm
                                                title="Are you sure delete this product?"
                                                ok-text="Yes"
                                                cancel-text="No"
                                                @confirm="
                                                    handleDelete(record.id)
                                                "
                                                @cancel="cancel"
                                            >
                                                <a>Delete</a>
                                            </a-popconfirm>
                                        </div>
                                    </template>
                                    <a-button type="primary">view</a-button>
                                </a-popover>
                            </template>
                        </template>
                    </a-table>
                </div>
            </div>
        </div>
        <a-modal
            v-model:visible="showAddProductModal"
            title="Add Product"
            :maskClosable="false"
            :afterClose="handleCancel"
            @ok="handleAddProduct"
            @cancel="handleCancel"
        >
            <a-form
                :model="form"
                name="basic"
                :label-col="{ span: 5 }"
                :wrapper-col="{ span: 16 }"
                autocomplete="off"
                @finish="onFinish"
                @finishFailed="onFinishFailed"
            >
                <a-form-item label="Category" name="category">
                    <a-select ref="select" v-model:value="form.category">
                        <a-select-option
                            v-for="(item, index) in props.categories"
                            :key="index"
                            :value="item.name"
                        >
                            {{ item.name }}
                        </a-select-option>
                    </a-select>
                </a-form-item>
                <a-form-item label="Name" name="name">
                    <a-input v-model:value="form.name" />
                </a-form-item>
                <a-form-item label="Description" name="description">
                    <a-input v-model:value="form.description" />
                </a-form-item>
                <a-form-item label="Price" name="price">
                    <a-input v-model:value="form.price" />
                </a-form-item>
                <a-form-item label="Stock" name="stock">
                    <a-input type="number" v-model:value="form.stock" />
                </a-form-item>
            </a-form>
        </a-modal>
        <a-modal
            v-model:visible="showEditProductModal"
            title="Edit Product"
            :maskClosable="false"
            :afterClose="handleCancel"
            @ok="updateProduct"
        >
            <a-form
                :model="formState"
                name="basic"
                :label-col="{ span: 5 }"
                :wrapper-col="{ span: 16 }"
                autocomplete="off"
                @finish="onFinish"
                @finishFailed="onFinishFailed"
            >
                <a-form-item label="Category" name="category">
                    <a-input v-model:value="form.category" />
                </a-form-item>
                <a-form-item label="Name" name="name">
                    <a-input v-model:value="form.name" />
                </a-form-item>
                <a-form-item label="Description" name="description">
                    <a-input v-model:value="form.description" />
                </a-form-item>
                <a-form-item label="Price" name="price">
                    <a-input v-model:value="form.price" />
                </a-form-item>
                <a-form-item label="Stock" name="stock">
                    <a-input type="number" v-model:value="form.stock" />
                </a-form-item>
            </a-form>
        </a-modal>
    </AuthenticatedLayout>
</template>

<style scoped>
>>> .ant-btn-primary {
    color: #fff;
    border-color: #ef559e;
    background: #ef559e;
    text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.12);
    box-shadow: 0 2px 0 rgba(0, 0, 0, 0.045);
}
</style>
