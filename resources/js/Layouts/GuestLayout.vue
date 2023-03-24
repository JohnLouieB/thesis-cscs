<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-white-100">
        <div>
            
                <a>
                    <img src="/STICKY2.png" class="w-40 h-15 fill-current text-gray-500" />
                </a>
              
        </div>

        <div
            class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white opacity-95  shadow-md overflow-hidden sm:rounded-lg"
           
        >
            <slot />
        </div>
    </div>
</template>
