<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, Settings2 } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import { ref, computed, provide } from 'vue';

const page = usePage();
const mainNavItems: NavItem[] = page.props.routes as NavItem[];

// Add state for sidebar collapsed mode
const isSidebarCollapsed = ref(false);

// Toggle sidebar collapsed state
function toggleSidebar() {
  isSidebarCollapsed.value = !isSidebarCollapsed.value;
  
  // Store preference in localStorage
  try {
    localStorage.setItem('sidebarCollapsed', JSON.stringify(isSidebarCollapsed.value));
  } catch (e) {
    console.error('Error saving sidebar state:', e);
  }
}

// Initialize from localStorage if available
try {
  const savedState = localStorage.getItem('sidebarCollapsed');
  if (savedState) {
    isSidebarCollapsed.value = JSON.parse(savedState);
  }
} catch (e) {
  console.error('Error loading sidebar state:', e);
}

// Provide collapsed state to child components
provide('isSidebarCollapsed', computed(() => isSidebarCollapsed.value));

const footerNavItems: NavItem[] = [];
</script>

<template>
  <Sidebar :collapsed="isSidebarCollapsed" class="border-r">
    <SidebarHeader>
      <SidebarMenu>
        <SidebarMenuItem>
          <SidebarMenuButton size="lg" as-child>
            <Link :href="route('dashboard')">
              <AppLogo />
            </Link>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </SidebarMenu>
      
      <!-- Add toggle button -->
      <button 
        @click="toggleSidebar" 
        class="absolute top-3 right-2 h-6 w-6 rounded-md flex items-center justify-center hover:bg-muted"
      >
        <svg 
          xmlns="http://www.w3.org/2000/svg" 
          width="16" height="16" 
          viewBox="0 0 24 24" 
          fill="none" 
          stroke="currentColor" 
          stroke-width="2" 
          stroke-linecap="round" 
          stroke-linejoin="round"
          :class="{'rotate-180': !isSidebarCollapsed}"
        >
          <path d="m15 18-6-6 6-6"/>
        </svg>
      </button>
    </SidebarHeader>

    <SidebarContent>
      <NavMain :items="mainNavItems" />
    </SidebarContent>

    <SidebarFooter>
      <NavFooter :items="footerNavItems" />
      <NavUser />
    </SidebarFooter>
  </Sidebar>
  <slot />
</template>

<style scoped>
.sidebar-collapsed {
  width: 80px;
}

/* Ensure flyout menu has proper z-index to appear above other content */
:deep(.hover-card-content) {
  z-index: 50;
}
</style>
