<script setup lang="ts">
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { useTableActions } from '@/composables/useTableActions'
import { BulkAction, Column, Filter, TableConfig } from '@/components/core/table/types';
import { Search } from 'lucide-vue-next';
import { Input } from '@/components/ui/input';
import HeaderActions from '@/components/core/table/HeaderActions.vue';
import SCTable from '@/components/core/table/SCTable.vue';

interface Props {
    breadcrumbs?: BreadcrumbItem[];
    data: any;
    columns: Column[];
    filters: Filter[];
    bulkActions: BulkAction[];
    config: TableConfig;
    actions: any[];
}

const props = withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});


const search = ref('')
const {
    columns,
    pagination,
    bulkActions,
    filters,
    actions,
    handleBulkAction,
    handleSelectAllRecords,
    handleRowAction
} = useTableActions(props); 
</script>

<template>
    <AppLayout title="Tenants" :breadcrumbs="breadcrumbs" :full-width="config.fullWidth">
        <template #header>
            <div class="flex items-center justify-between w-full">
                <div class="relative w-full max-w-sm items-center">
                    <Input id="search" type="text" placeholder="Search..." class="pl-10" v-model="search" />
                    <span class="absolute start-0 inset-y-0 flex items-center justify-center px-2">
                        <Search class="size-4 text-muted-foreground" />
                    </span>
                </div>
            </div>
        </template>

        <div class="py-12">

            <div class="p-4 sm:p-6 lg:p-8">
                <SCTable :data="data.data" :columns="columns" :filters="filters" :bulk-actions="bulkActions"
                    :config="config" :pagination="pagination" @bulk-action="handleBulkAction"
                    @select-all-records="handleSelectAllRecords" @row-action="handleRowAction"
                    :routeName="config.routeName">
                    <template #actions>
                        <HeaderActions :actions="actions" />
                        <!-- Header actios aqui -->
                    </template>
                </SCTable>
            </div>
        </div>
    </AppLayout>
</template>
