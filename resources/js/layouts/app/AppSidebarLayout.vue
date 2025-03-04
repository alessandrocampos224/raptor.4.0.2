<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
import { Toaster } from '@/components/ui/toast';
import FlashMessageHandler from '@/components/FlashMessageHandler.vue';
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
    <AppShell variant="sidebar">
        <AppSidebar />
        <AppContent variant="sidebar">
            <AppSidebarHeader :breadcrumbs="breadcrumbs">
                <slot name="header" />
            </AppSidebarHeader>
           <div class="w-full" :class="{ 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8': !fullWidth }">
            <slot />
           </div>
        </AppContent>
        <!-- Toast System -->
        <Toaster />
        <FlashMessageHandler />
    </AppShell>
</template>
