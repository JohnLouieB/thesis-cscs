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
const formErrors = ref([]);
const isEditing = ref(false);

const form = useForm({
    category: null,
    name: null,
    description: null,
    price: null,
    stock: 0,
    id: null,
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
        title: "Stock",
        key: "stock",
        class: "text-center",
        dataIndex: "stock",
    },
    {
        title: "Status",
        key: "status",
        class: "text-center",
        dataIndex: "status",
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
    loading.value = true;
    axios
        .get("/current-user")
        .then((res) => {
            currentUser.value = res.data;
            if (res.data.role == "cashier") {
                columns.value = columns.value.filter(
                    (column) => column.title !== "Action"
                );
            }
        })
        .finally(() => {
            loading.value = false;
        });
};

const handleAddProduct = () => {
    form.post("/products", {
        preserveScroll: true,
        onSuccess: () => {
            showAddProductModal.value = false;
            form.reset();
            showAddProductModal.value = false;
            notification.success({
                message: "Product Added Successfully",
            });
        },
        onError: (errors) => {
            formErrors.value = errors;
        },
    });
};

const handleCancel = () => {
    form.reset();
    formErrors.value = [];
    isEditing.value = false;
    showAddProductModal.value = false;
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
    showAddProductModal.value = true;
    isEditing.value = true;
    productId.value = product.id;
    form.id = product.id;
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
            formErrors.value = [];
            notification.success({
                message: "Product Updated Successfully",
            });
        },
        onError: (errors) => {
            formErrors.value = errors;
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
                <div class="mb-5 text-center page-title">Product list</div>
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
                        class="overflow-y-auto"
                        :loading="loading"
                        :columns="columns"
                        :data-source="props.products"
                        :pagination="false"
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
                                <span>
                                    {{ record.stock }}
                                </span>
                            </template>
                            <template v-else-if="column.key === 'status'">
                                <span
                                    class="text-green-400"
                                    v-if="record.stock !== 0"
                                >
                                    <img
                                        src="/active.png"
                                        class="w-[15px] h-[18px] ml-5"
                                    />
                                </span>
                                <span class="text-red-400" v-else>
                                    <img
                                        src="/inactive.png"
                                        class="w-[15px] h-[18px] ml-5"
                                    />
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
            :title="isEditing ? 'Edit Product' : 'Add Product'"
            :maskClosable="false"
            :afterClose="handleCancel"
            @ok="isEditing ? updateProduct() : handleAddProduct()"
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
                    <div>
                        <a-select ref="select" v-model:value="form.category">
                            <a-select-option
                                v-for="(item, index) in props.categories"
                                :key="index"
                                :value="item.name"
                            >
                                {{ item.name }}
                            </a-select-option>
                        </a-select>
                        <div>
                            <span
                                class="text-red-400 italic"
                                v-if="formErrors"
                                >{{ formErrors.category }}</span
                            >
                        </div>
                    </div>
                </a-form-item>
                <a-form-item label="Name" name="name">
                    <a-input v-model:value="form.name" />
                    <div>
                        <span class="text-red-400 italic" v-if="formErrors">{{
                            formErrors.name
                        }}</span>
                    </div>
                </a-form-item>
                <a-form-item label="Description" name="description">
                    <a-input v-model:value="form.description" />
                    <div>
                        <span class="text-red-400 italic" v-if="formErrors">{{
                            formErrors.description
                        }}</span>
                    </div>
                </a-form-item>
                <a-form-item label="Price" name="price">
                    <a-input v-model:value="form.price" />
                    <div>
                        <span class="text-red-400 italic" v-if="formErrors">{{
                            formErrors.price
                        }}</span>
                    </div>
                </a-form-item>
                <a-form-item label="Stock" name="stock">
                    <a-input type="number" v-model:value="form.stock" />
                    <div>
                        <span class="text-red-400 italic" v-if="formErrors">{{
                            formErrors.stock
                        }}</span>
                    </div>
                </a-form-item>
            </a-form>
        </a-modal>
    </AuthenticatedLayout>
</template>

<style scoped>
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
</style>
