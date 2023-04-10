<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm, Head } from "@inertiajs/vue3";
import { ref, watchEffect, onMounted } from "vue";
import { message, notification } from "ant-design-vue";
import { formatStrategyValues } from "ant-design-vue/lib/vc-tree-select/utils/strategyUtil";
import moment from "moment";

//Variables
const props = defineProps({
    categories: Array,
});

const showAddCategoryModal = ref(false);
const isEditing = ref(false);

const form = useForm({
    name: null,
    id: null,
});

const columns = [
    {
        title: "Name",
        dataIndex: "name",
        key: "name",
    },
    {
        title: "Action",
        class: "w-1 text-center",
        key: "action",
    },
];

const handleCancel = () => {
    showAddCategoryModal.value = false;
    form.name = null;
};

const handleAddCategory = () => {
    form.post("/categories", {
        preserveScroll: true,
        onSuccess: () => {
            showAddCategoryModal.value = false;
            form.reset();
            notification.success({
                message: "Category Added Successfully",
            });
        },
    });
};

const editCategoryModal = (e) => {
    form.name = e.name;
    form.id = e.id;
    showAddCategoryModal.value = true;
};

const handleEditCategory = () => {
    form.put(`/categories/${form.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            showAddCategoryModal.value = false;
            form.reset();
            notification.success({
                message: "Category Updated Successfully",
            });
        },
    });
};
const handleDelete = (id) => {
    form.delete(`/categories/${id}`, {
        onSuccess: () => {
            showAddCategoryModal.value = false;
            form.reset();
            notification.success({
                message: "Category Deleted Successfully",
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
                Category
            </h2>
        </template>
        <div class="py-12 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-5 text-center">Category</div>
                <div class="mb-5">
                    <a-button
                        type="primary"
                        @click="showAddCategoryModal = true"
                    >
                        Add Category
                    </a-button>
                </div>
                <a-table
                    :loading="loading"
                    :columns="columns"
                    :data-source="props.categories"
                >
                    <template #bodyCell="{ column, record }">
                        <template v-if="column.key === 'action'">
                            <a-popover title="Actions" trigger="click">
                                <template #content>
                                    <div class="flex space-x-5">
                                        <a @click="editCategoryModal(record)"
                                            >Update</a
                                        >
                                        <a-popconfirm
                                            title="Are you sure delete this product?"
                                            ok-text="Yes"
                                            cancel-text="No"
                                            @confirm="handleDelete(record.id)"
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
            <a-modal
                v-model:visible="showAddCategoryModal"
                :title="isEditing ? 'Update Category' : 'Add Category'"
                :maskClosable="false"
                :afterClose="handleCancel"
                @ok="isEditing ? handleEditCategory() : handleAddCategory()"
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
                    <a-form-item label="Name" name="name">
                        <a-input v-model:value="form.name" />
                    </a-form-item>
                </a-form>
            </a-modal>
        </div>
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
