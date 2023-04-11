<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Register from "../../Pages/Auth/Register.vue";
import { useForm } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";
import { message, notification } from "ant-design-vue";

const props = defineProps({
    users: Array,
    user: String,
});

const showAddModal = ref(false);
const createUser = ref(false);
const userId = ref(null);
const columns = [
    {
        title: "Name",
        dataIndex: "name",
        key: "name",
    },
    {
        title: "Email",
        dataIndex: "email",
        key: "email",
    },
    {
        title: "Role",
        dataIndex: "role",
        key: "role",
    },
    {
        title: "Action",
        class: "w-1 text-center",
        dataIndex: "actions",
        key: "actions",
    },
];

const form = useForm({
    name: null,
    email: null,
    role: null,
    password: null,
    password_confirmation: null,
});

const submit = () => {
    form.post("/users", {
        preserveScroll: true,
        onSuccess: () => {
            showAddModal.value = false;
            form.reset();
            notification.success({
                message: "Cashier Added Successfully",
            });
        },
    });
};

onMounted(() => {
    console.log("res");
});

const showRegister = () => {
    showAddModal.value = true;
    createUser.value = true;
};

const handleCancel = () => {
    form.value = {};
    form.name = null;
    form.email = null;
};

const editUser = (e) => {
    userId.value = e.id;
    form.name = e.name;
    form.email = e.email;
    form.role = e.role;
    showAddModal.value = true;
    createUser.value = false;
};

const update = () => {
    form.put(`/users/${userId.value}`, {
        preserveScroll: true,
        onSuccess: () => {
            showAddModal.value = false;
            form.reset();
            notification.success({
                message: "User Updated Successfully",
            });
        },
    });
};

const deleteUser = (id) => {
    form.delete(`/users/${id}`, {
        onSuccess: () => {
            notification.success({
                message: "User Deleted Successfully",
            });
        },
    });
};
</script>
<template>
    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Users
            </h2>
        </template>
        <div class="py-12 min-h-screen" v-if="props.user.role !== 'admin'">
            <a-result
                status="403"
                title="403"
                sub-title="Sorry, you are not authorized to access this page."
            >
                <template #extra>
                    <!-- <a-button type="primary">Back Home</a-button> -->
                </template>
            </a-result>
        </div>
        <div v-else class="py-12 h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-5 text-center page-title">Users</div>
                <a-button type="primary" @click="showRegister()">
                    Add New Account
                </a-button>
                <a-modal
                    v-model:visible="showAddModal"
                    :title="createUser ? 'Add Account' : 'Edit Account'"
                    :afterClose="handleCancel"
                    width="50%"
                    height="50%"
                    :footer="null"
                    :closable="true"
                    :maskClosable="false"
                >
                    <a-form :model="form">
                        <a-form-item label="Name" name="name">
                            <a-input v-model:value="form.name" />
                        </a-form-item>
                        <a-form-item label="Email" name="email">
                            <a-input type="email" v-model:value="form.email" />
                        </a-form-item>
                        <div v-if="createUser == true">
                            <a-form-item label="Role" name="role">
                                <a-select v-model:value="form.role">
                                    <a-select-option value="cashier"
                                        >cashier</a-select-option
                                    >
                                    <a-select-option value="admin"
                                        >admin</a-select-option
                                    >
                                </a-select>
                            </a-form-item>
                            <a-form-item label="Password" name="password">
                                <a-input v-model:value="form.password" />
                            </a-form-item>
                            <a-form-item
                                label="Password Confirmation"
                                name="password_confirmation"
                            >
                                <a-input
                                    v-model:value="form.password_confirmation"
                                />
                            </a-form-item>
                        </div>
                    </a-form>
                    <div class="flex justify-end">
                        <a-button
                            type="primary"
                            @click="createUser ? submit() : update()"
                            >Submit</a-button
                        >
                    </div>
                </a-modal>
                <a-table
                    class="mt-5"
                    :columns="columns"
                    :data-source="props.users"
                >
                    <template #bodyCell="{ column, text, record }">
                        <template v-if="column.key === 'actions'">
                            <a-popover title="Actions" trigger="click">
                                <template #content>
                                    <div class="flex space-x-5">
                                        <a @click="editUser(record)">Update</a>
                                        <a-popconfirm
                                            title="Are you sure to delete this user?"
                                            ok-text="Yes"
                                            cancel-text="No"
                                            @confirm="deleteUser(record.id)"
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
