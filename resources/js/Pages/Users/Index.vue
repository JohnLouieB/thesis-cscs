<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { useForm } from "@inertiajs/vue3";

const form = useForm({
    name: null,
    email: null,
    role: 'cashier'
})
</script>
<template>
    <GuestLayout>

    </GuestLayout>
</template>