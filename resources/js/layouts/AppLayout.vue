<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
    fullWidth?: boolean;
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
    fullWidth: false,
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs" :fullWidth="fullWidth">
        <template #header>
            <slot name="header" />
        </template>
        <slot />
        <div class="grid grid-cols-12 gap-4">
            <div class="col-span-1 sm:col-span-1 md:col-span-1 lg:col-span-1 xl:col-span-1"></div>
            <div class="col-span-2 sm:col-span-2 md:col-span-2 lg:col-span-2 xl:col-span-2"></div>
            <div class="col-span-3 sm:col-span-3 md:col-span-3 lg:col-span-3 xl:col-span-3"></div>
            <div class="col-span-4 sm:col-span-4 md:col-span-4 lg:col-span-4 xl:col-span-4"></div>
            <div class="col-span-5 sm:col-span-5 md:col-span-5 lg:col-span-5 xl:col-span-5"></div>
            <div class="col-span-6 sm:col-span-6 md:col-span-6 lg:col-span-6 xl:col-span-6"></div>
            <div class="col-span-7 sm:col-span-7 md:col-span-7 lg:col-span-7 xl:col-span-7"></div>
            <div class="col-span-8 sm:col-span-8 md:col-span-8 lg:col-span-8 xl:col-span-8"></div>
            <div class="col-span-9 sm:col-span-9 md:col-span-9 lg:col-span-9 xl:col-span-9"></div>
            <div class="col-span-10 sm:col-span-10 md:col-span-10 lg:col-span-10 xl:col-span-10"></div>
            <div class="col-span-11 sm:col-span-11 md:col-span-11 lg:col-span-11 xl:col-span-11"></div>
            <div class="col-span-12 sm:col-span-12 md:col-span-12 lg:col-span-12 xl:col-span-12"></div>
        </div>
        <div class="grid-cols-2 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-2"></div>
        <div class="grid-cols-3 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-3"></div>
        <div class="grid-cols-4 sm:grid-cols-4 md:grid-cols-4 lg:grid-cols-4 xl:grid-cols-4"></div>
        <div class="grid-cols-5 sm:grid-cols-5 md:grid-cols-5 lg:grid-cols-5 xl:grid-cols-5"></div>
        <div class="grid-cols-6 sm:grid-cols-6 md:grid-cols-6 lg:grid-cols-6 xl:grid-cols-6"></div>
        <div class="grid-cols-7 sm:grid-cols-7 md:grid-cols-7 lg:grid-cols-7 xl:grid-cols-7"></div>
        <div class="grid-cols-8 sm:grid-cols-8 md:grid-cols-8 lg:grid-cols-8 xl:grid-cols-8"></div>
        <div class="grid-cols-9 sm:grid-cols-9 md:grid-cols-9 lg:grid-cols-9 xl:grid-cols-9"></div>
        <div class="grid-cols-10 sm:grid-cols-10 md:grid-cols-10 lg:grid-cols-10 xl:grid-cols-10"></div>
        <div class="grid-cols-11 sm:grid-cols-11 md:grid-cols-11 lg:grid-cols-11 xl:grid-cols-11"></div>
        <div class="grid-cols-12 sm:grid-cols-12 md:grid-cols-12 lg:grid-cols-12 xl:grid-cols-12"></div> 
    </AppLayout>
</template>
