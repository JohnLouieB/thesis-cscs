<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Register from "../../Pages/Auth/Register.vue";
import { useForm } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";

const props = defineProps({
    users: Array,
    user: String,
});

const showAddModal = ref(false);
const showRegisterAdmin = ref(false);
const createAccount = ref("");
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
            showRegister.value = false;
            form.reset();
            notification.success({
                message: "Cashier Added Successfully",
            });
        },
    });
};

const showRegister = (e) => {
    showAddModal.value = true;
    createAccount.value = e;
};

const handleCancel = () => {
    form.value = {};
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
            <a-button type="primary" @click="showRegister('Cashier')">
                Add New Cashier
            </a-button>
            <a-button
                class="ml-5"
                type="primary"
                @click="showRegister('Admin')"
            >
                Add New Admin
            </a-button>
            <a-modal
                v-model:visible="showAddModal"
                :title="createAccount"
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
                    <a-form-item label="Role" name="role">
                        <a-input
                            readonly
                            :value="createAccount"
                            v-model:value="form.role"
                        />
                    </a-form-item>
                    <a-form-item label="Password" name="password">
                        <a-input v-model:value="form.password" />
                    </a-form-item>
                    <a-form-item
                        label="Password Confirmation"
                        name="password_confirmation"
                    >
                        <a-input v-model:value="form.password_confirmation" />
                    </a-form-item>
                </a-form>
                <div class="flex justify-end">
                    <a-button type="primary" @click="submit()">Submit</a-button>
                </div>
            </a-modal>
            <a-table class="mt-5" :columns="columns" :data-source="props.users">
                <template #bodyCell="{ column, text, record }">
                    <template v-if="column.key === 'actions'">
                        <div class="flex space-x-5">
                            <a-button @click="handleViewReceipt(record)"
                                >Update</a-button
                            >
                            <a-button @click="handleViewReceipt(record)"
                                >Delete</a-button
                            >
                        </div>
                    </template>
                </template>
            </a-table>
        </div>
    </AuthenticatedLayout>
</template>
