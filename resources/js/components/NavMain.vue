<script setup lang="ts">
import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { ChevronRight } from 'lucide-vue-next';
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible';
import { SidebarMenuSub, SidebarMenuSubButton, SidebarMenuSubItem } from '@/components/ui/sidebar';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { HoverCard, HoverCardContent, HoverCardTrigger } from '@/components/ui/hover-card';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { computed, inject } from 'vue';

const props = defineProps<{
  items: NavItem[];
}>();

// Get sidebar collapsed state from the parent component
const isSidebarCollapsed = inject('isSidebarCollapsed', computed(() => true));
</script>

<template>
  <SidebarGroup class="px-2 py-0">
    <SidebarGroupLabel>Platform</SidebarGroupLabel>
    <SidebarMenu>
      <template v-for="(item, key) in items" :key="item.modelPluralName || item.navigationGroup">
        <!-- Single menu item without subitems -->
        <template v-if="!item.items">
          <TooltipProvider v-if="isSidebarCollapsed">
            <Tooltip>
              <TooltipTrigger asChild>
                <SidebarMenuItem :isActive="item.isActive">
                  <SidebarMenuButton as-child>
                    <Link :href="item.href">
                      <component :is="item.navigationIcon" v-if="item.navigationIcon" class="h-4 w-4" />
                      <span v-if="!isSidebarCollapsed">{{ item.modelPluralName }}</span>
                    </Link>
                  </SidebarMenuButton>
                </SidebarMenuItem>
              </TooltipTrigger>
              <TooltipContent side="right">
                {{ item.modelPluralName }}
              </TooltipContent>
            </Tooltip>
          </TooltipProvider>
          
          <SidebarMenuItem v-else :isActive="item.isActive">
            <SidebarMenuButton as-child>
              <Link :href="item.href">
                <component :is="item.navigationIcon" v-if="item.navigationIcon" class="h-4 w-4" />
                <span>{{ item.modelPluralName }}</span>
              </Link>
            </SidebarMenuButton>
          </SidebarMenuItem>
        </template>

        <!-- Menu item with subitems -->
        <template v-else>
          <!-- When collapsed, use HoverCard for fly-out menu -->
          <HoverCard v-if="isSidebarCollapsed" :openDelay="100" :closeDelay="200">
            <HoverCardTrigger asChild>
              <SidebarMenuItem :isActive="item.isActive">
                <SidebarMenuButton>
                  <component :is="item.navigationIcon || item.navigationGroupIcon" class="h-4 w-4" />
                </SidebarMenuButton>
              </SidebarMenuItem>
            </HoverCardTrigger>
            <HoverCardContent side="right" class="w-56 p-0">
              <div class="p-2">
                <div class="font-medium mb-1">{{ item.navigationGroup }}</div>
                <div class="space-y-1">
                  <Link 
                    v-for="subItem in item.items" 
                    :key="subItem.modelPluralName"
                    :href="subItem.href" 
                    class="block w-full p-2 text-sm rounded-md hover:bg-muted"
                    :class="{ 'bg-muted': subItem.isActive }"
                  >
                    {{ subItem.modelPluralName }}
                  </Link>
                </div>
              </div>
            </HoverCardContent>
          </HoverCard>
          
          <!-- When expanded, use Collapsible -->
          <Collapsible v-else as-child :default-open="item.isActive" class="group/collapsible">
            <SidebarMenuItem :isActive="item.isActive">
              <CollapsibleTrigger as-child>
                <SidebarMenuButton>
                  <component :is="item.navigationGroupIcon" v-if="item.navigationGroupIcon" class="h-4 w-4" />
                  <span>{{ item.navigationGroup }}</span>
                  <ChevronRight 
                    class="ml-auto h-4 w-4 shrink-0 transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90" 
                  />
                </SidebarMenuButton>
              </CollapsibleTrigger>
              <CollapsibleContent>
                <SidebarMenuSub>
                  <SidebarMenuSubItem 
                    v-for="subItem in item.items" 
                    :key="subItem.modelPluralName" 
                    :isActive="subItem.isActive"
                  >
                    <SidebarMenuSubButton as-child>
                      <Link :href="subItem.href">
                        <component 
                          :is="subItem.navigationIcon" 
                          v-if="subItem.navigationIcon" 
                          class="h-3.5 w-3.5 mr-2" 
                        />
                        <span>{{ subItem.modelPluralName }}</span>
                      </Link>
                    </SidebarMenuSubButton>
                  </SidebarMenuSubItem>
                </SidebarMenuSub>
              </CollapsibleContent>
            </SidebarMenuItem>
          </Collapsible>
        </template>
      </template>
    </SidebarMenu>
  </SidebarGroup>
</template>
