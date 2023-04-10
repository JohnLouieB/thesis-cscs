<script setup>
import { Link } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";
import axios from "axios";

import {
    DownOutlined,
    CrownOutlined,
    AreaChartOutlined,
    DashboardOutlined,
} from "@ant-design/icons-vue";

const selectedKeys = ref([window.location.href]);
const currentUser = ref([]);

let adminLinks = [
    {
        label: "Dashboard",
        path: "/dashboard",
        icon: DashboardOutlined,
    },
    {
        label: "Product List",
        path: "/products",
        icon: CrownOutlined,
    },
    {
        label: "POS",
        path: "/sales",
        icon: AreaChartOutlined,
    },
    {
        label: "Daily Sales Report",
        path: "/reports",
        icon: DashboardOutlined,
    },
];

let cashierLinks = [
    {
        label: "Dashboard",
        path: "/dashboard",
        icon: DashboardOutlined,
    },
    {
        label: "Product List",
        path: "/products",
        icon: CrownOutlined,
    },
    {
        label: "POS",
        path: "/sales",
        icon: AreaChartOutlined,
    },
];

let subMenuLinks = [
    {
        label: "Category List",
        path: "/categories",
        icon: DashboardOutlined,
    },
    {
        label: "User List",
        path: "/users",
        icon: DashboardOutlined,
    },
];

onMounted(() => {
    getUser();
    console.log(currentUser.value);
});

const getUser = () => {
    axios.get("/current-user").then((res) => {
        currentUser.value = res.data;
    });
};

const showingNavigationDropdown = ref(false);
</script>

<template>
    <a-layout class="min-h-screen font-sans font-normal">
        <a-layout-sider style="background-color: #ef559e" width="190">
            <div class="px-10 py-5">
                <a>
                    <img src="/STICKY2.png" class="max-w-[100%]" />
                </a>
            </div>
            <!-- {{ $page.props.auth.user.role }} -->
            <a-menu
                style="background-color: #ef559e"
                class="hover:bg-gray-200"
                v-model:selectedKeys="selectedKeys"
                theme="dark"
                mode="inline"
            >
                <a-menu-item
                    v-if="$page.props.auth.user.role == 'admin'"
                    style="background-color: #ef559e"
                    v-for="link in adminLinks"
                    :key="link.path"
                >
                    <Link :href="link.path">
                        <template v-if="link.icon">
                            <component :is="link.icon"></component>
                        </template>
                        <span class="font-medium">{{ link.label }}</span>
                    </Link>
                </a-menu-item>
                <a-menu-item
                    v-else
                    style="background-color: #ef559e"
                    v-for="x in cashierLinks"
                    :key="x.path"
                >
                    <Link :href="x.path">
                        <template v-if="x.icon">
                            <component :is="x.icon"></component>
                        </template>
                        <span class="font-medium">{{ x.label }}</span>
                    </Link>
                </a-menu-item>
                <a-sub-menu
                    v-if="$page.props.auth.user.role == 'admin'"
                    style="background-color: #ef559e"
                    title="Maintenance"
                >
                    <a-menu-item
                        style="background-color: #ef559e"
                        v-for="subLink in subMenuLinks"
                        :key="subLink.path"
                        ><Link :href="subLink.path">
                            <template v-if="subLink.icon">
                                <component :is="subLink.icon"></component>
                            </template>
                            <span class="font-medium">{{ subLink.label }}</span>
                        </Link></a-menu-item
                    >
                </a-sub-menu>
            </a-menu>
        </a-layout-sider>
        <a-layout>
            <a-layout-header
                class="bg-white flex justify-end"
                style="background-color: #ef559e"
            >
                <a-dropdown :trigger="['click']">
                    <a
                        class="ant-dropdown-link text-white font-semibold"
                        @click.prevent
                    >
                        {{ $page.props.auth.user.name }}
                        <DownOutlined />
                    </a>
                    <template #overlay>
                        <a-menu>
                            <!-- <a-menu-item key="0">
                                <Link href="/account-settings"
                                    >Account Settings</Link
                                >
                            </a-menu-item> -->
                            <a-menu-item key="1">
                                <Link
                                    :href="route('logout')"
                                    method="post"
                                    as="button"
                                    class="block"
                                    >Logout</Link
                                >
                            </a-menu-item>
                        </a-menu>
                    </template>
                </a-dropdown>
            </a-layout-header>
            <a-layout-content class="bg-white-500 p-5">
                <slot />
            </a-layout-content>
            <a-layout-footer class="text-center"
                >Sticky Buns all rights reserved @2023</a-layout-footer
            >
        </a-layout>
    </a-layout>
</template>

<style scoped>
>>> .ant-menu-dark .ant-menu-inline.ant-menu-sub {
    background: #ef559e;
}
>>> .ant-menu-inline.ant-menu-root
    .ant-menu-submenu-title
    > .ant-menu-title-content {
    color: white;
}
>>> .ant-menu-item .anticon + span {
    color: white;
}
>>> .ant-menu-item:hover {
    background-color: white;
    color: red;
}
</style>
