<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm, Head } from "@inertiajs/vue3";
import { ref, watchEffect, onMounted } from "vue";
import { message, notification } from "ant-design-vue";

//Variables
const props = defineProps({
    products: Array,
});

const showAddProductModal = ref(false);
const showEditProductModal = ref(false);
const loading = ref(false);

const productId = ref(null);

const form = useForm({
    category: null,
    name: null,
    description: null,
    price: null,
    stock: 0,
});

const columns = [
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
];
//Methods
onMounted(() => {
    console.log(props.products);
});

const handleAddProduct = () => {
    console.log(form.value);
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
    form.delete(`/products/${id}`);
};

const editProductModal = (product) => {
    showEditProductModal.value = true;
    // console.log(form.category);
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
                <div class="mb-5 text-center">Product list</div>
                <div class="mb-5">
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
                        <template #headerCell="{ column }">
                            <!-- <template v-if="column.key === 'category'">
                                <span>
                                    <smile-outlined />
                                    {{ record.category }}
                                </span>
                            </template> -->
                        </template>

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
                            <template v-else-if="column.key === 'action'">
                                <a-popover title="Actions" trigger="click">
                                    <template #content>
                                        <div class="flex space-x-5">
                                            <a @click="editProductModal(record)"
                                                >Update</a
                                            >
                                            <a @click="handleDelete(record.id)"
                                                >Delete</a
                                            >
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
            @ok="handleAddProduct"
            @cancel="handleCancel"
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
        <a-modal
            v-model:visible="showEditProductModal"
            title="Edit Product"
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
