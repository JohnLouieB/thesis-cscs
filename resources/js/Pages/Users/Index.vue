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
const createAccount = ref("");
const createUser = ref(false);
const options = ref([
    {
        value: "cashier",
        label: "cashier",
    },
    {
        value: "admin",
        label: "admin",
    },
]);
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
        title: "Actions",
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

const showRegister = () => {
    showAddModal.value = true;
    createUser.value = true;
};

const handleCancel = () => {
    form.value = {};
};

const editUser = (e) => {
    console.log(e.name);
    form.value = Object.assign({}, form.value, JSON.parse(JSON.stringify(e)));
    console.log(form.value, "form");
    showAddModal.value = true;
    createUser.value = false;
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
        <div class="py-12 h-screen" v-if="props.user.role !== 'admin'">
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
            <div class="mb-5 text-center text-[20px] font-[elephant]">
                Users
            </div>
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
                    <a-button type="primary" @click="submit()">Submit</a-button>
                </div>
            </a-modal>
            <a-table class="mt-5" :columns="columns" :data-source="props.users">
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
