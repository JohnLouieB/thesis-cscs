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
const formErrors = ref([]);
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
    role: "admin",
    password: null,
    password_confirmation: null,
    id: null,
});

const submit = () => {
    formErrors.value = [];
    form.post("/users", {
        preserveScroll: true,
        onSuccess: () => {
            showAddModal.value = false;
            form.reset();
            notification.success({
                message: "Account Added Successfully",
            });
            formErrors.value = [];
        },
        onError: (errors) => {
            formErrors.value = errors;
        },
    });
};

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
    form.id = e.id;
    showAddModal.value = true;
    createUser.value = false;
    formErrors.value = [];
};

const update = () => {
    formErrors.value = [];
    form.put(`/users/${userId.value}`, {
        preserveScroll: true,
        onSuccess: () => {
            showAddModal.value = false;
            form.reset();
            notification.success({
                message: "Account Updated Successfully",
            });
        },
        onError: (errors) => {
            formErrors.value = errors;
        },
    });
};

const deleteUser = (id) => {
    form.delete(`/users/${id}`, {
        onSuccess: () => {
            notification.success({
                message: "Account Deleted Successfully",
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
                    :footer="null"
                    :closable="true"
                    :maskClosable="false"
                >
                    <div class="form-control">
                        <div class="flex justify-center">
                            <div>
                                <div class>
                                    <div>
                                        <label class="label">
                                            <span class="label-text"
                                                >Name:</span
                                            >
                                        </label>
                                        <a-input
                                            type="text"
                                            v-model:value="form.name"
                                            placeholder="username"
                                            class="input"
                                        />
                                        <!-- <input
                                                type="text"
                                                v-model="form.name"
                                                placeholder="username"
                                                class="input input-bordered w-full max-w-xs"
                                            /> -->
                                        <div>
                                            <span
                                                class="text-red-400 italic"
                                                v-if="formErrors"
                                                >{{ formErrors.name }}</span
                                            >
                                        </div>
                                    </div>
                                    <div class="mt-5">
                                        <label class="label">
                                            <span class="label-text"
                                                >Email:</span
                                            >
                                        </label>
                                        <a-input
                                            type="text"
                                            v-model:value="form.email"
                                            placeholder="email@gmail.com"
                                            class="input"
                                        />
                                        <!-- <input
                                                type="text"
                                                v-model="form.email"
                                                placeholder="UserEmail@gmail.com"
                                                class="input input-bordered w-full max-w-xs"
                                            /> -->
                                        <div>
                                            <span
                                                class="text-red-400 italic"
                                                v-if="formErrors"
                                                >{{ formErrors.email }}</span
                                            >
                                        </div>
                                    </div>
                                </div>
                                <div class="w-full mt-5">
                                    <div class="w-full">
                                        <label class="label">
                                            <span class="label-text"
                                                >Role:</span
                                            >
                                        </label>
                                        <a-select
                                            v-model:value="form.role"
                                            style="width: 120px"
                                        >
                                            <a-select-option value="admin"
                                                >admin</a-select-option
                                            >
                                            <a-select-option value="cashier">
                                                cashier
                                            </a-select-option>
                                        </a-select>
                                    </div>
                                </div>
                                <div class="mt-5">
                                    <div>
                                        <label class="label">
                                            <span class="label-text"
                                                >Password:</span
                                            >
                                        </label>
                                        <a-input
                                            type="text"
                                            v-model:value="form.password"
                                            placeholder="password"
                                            class="input"
                                        />
                                        <div>
                                            <span
                                                class="text-red-400 italic"
                                                v-if="formErrors"
                                                >{{ formErrors.password }}</span
                                            >
                                        </div>
                                    </div>
                                    <div v-if="createUser == true" class="mt-5">
                                        <label class="label">
                                            <span class="label-text"
                                                >Password Confirmation:</span
                                            >
                                        </label>
                                        <a-input
                                            type="text"
                                            v-model:value="
                                                form.password_confirmation
                                            "
                                            placeholder="confirm password"
                                            class="input"
                                        />
                                        <div>
                                            <span
                                                class="text-red-400 italic"
                                                v-if="formErrors"
                                                >{{ formErrors.password }}</span
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
