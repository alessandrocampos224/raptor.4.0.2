<script setup lang="ts">
import { ref } from 'vue'; 
import AppLayout from '@/layouts/AppLayout.vue';
import { Search } from 'lucide-vue-next';
import { Input } from '@/components/ui/input';
import HeaderActions from '@/components/core/table/HeaderActions.vue';
import { type BreadcrumbItem } from '@/types'; 
import SCForm from '@/components/core/form/SCForm.vue';
import { FormConfig } from '@/components/core/form/types';
import PageBuilder from '@quantum/page-builder/components/PageBuilder.vue';
const search = ref('')

const props = defineProps<{
    breadcrumbs: BreadcrumbItem[];
    sections: any[];
    record: any;
    config: FormConfig & {
        action: string;
        method: 'post' | 'put' | 'patch';
    };
    actions: any[];
}>();

console.log('Seções da página:', props.sections);
</script>

<template>
    <AppLayout title="Tenants" :breadcrumbs="breadcrumbs" :full-width="true">
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
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12">
                    <PageBuilder 
                        :sections="sections" 
                        :record="record" 
                        :config="config" 
                        :actions="actions" 
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
