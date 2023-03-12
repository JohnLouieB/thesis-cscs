<script setup>
import { ref, reactive } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { Link } from "@inertiajs/vue3";

import {
    DownOutlined,
    ArrowLeftOutlined,
    CompassOutlined,
    CrownOutlined,
    AreaChartOutlined,
    UserOutlined,
    DashboardOutlined,
} from "@ant-design/icons-vue";

const selectedKeys = ref([window.location.href]);

let links = [
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
        label: "Sales",
        path: "/sales",
        icon: AreaChartOutlined,
    },
    {
        label: "Daily Sales Report",
        path: "/reports",
        icon: DashboardOutlined,
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
        path: "/user-list",
        icon: DashboardOutlined,
    },
];

const showingNavigationDropdown = ref(false);
</script>

<template>
    <a-layout class="min-h-screen font-sans font-normal">
        <a-layout-sider style="background-color: #263e8e" width="230">
            <div class="px-3 py-4">
                <a>
                    <img src="/Simple_CSCS_Logo.jpg" class="max-w-[50%]" />
                </a>
            </div>
            <a-menu
                style="background-color: #263e8e"
                class="hover:bg-gray-200"
                v-model:selectedKeys="selectedKeys"
                theme="dark"
                mode="inline"
            >
                <a-menu-item v-for="link in links" :key="link.path">
                    <Link :href="link.path">
                        <template v-if="link.icon">
                            <component :is="link.icon"></component>
                        </template>
                        <span class="font-medium">{{ link.label }}</span>
                    </Link>
                </a-menu-item>
                <a-sub-menu
                    style="background-color: #263e8e"
                    title="Maintenance"
                >
                    <a-menu-item
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
                style="background-color: #263e8e"
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
                            <a-menu-item key="0">
                                <Link href="/account-settings"
                                    >Account Settings</Link
                                >
                            </a-menu-item>
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
            <a-layout-content class="bg-gray-100 p-6">
                <slot />
            </a-layout-content>
            <a-layout-footer class="text-center"
                >CSCS all rights reserved @2023</a-layout-footer
            >
        </a-layout>
    </a-layout>
</template>

<style scoped>
>>> .ant-menu-dark .ant-menu-inline.ant-menu-sub {
    background: #263e8e;
}
</style>
