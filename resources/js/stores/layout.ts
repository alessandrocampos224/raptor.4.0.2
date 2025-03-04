import { ref, readonly } from 'vue';

// Create a simple reactive state
const sidebarCollapsed = ref(false);

// Initialize from localStorage if available
try {
  const savedState = localStorage.getItem('sidebarCollapsed');
  if (savedState) {
    sidebarCollapsed.value = JSON.parse(savedState);
  }
} catch (e) {
  console.error('Error loading sidebar state from localStorage:', e);
}

// Actions
function toggleSidebar() {
  sidebarCollapsed.value = !sidebarCollapsed.value;
  try {
    localStorage.setItem('sidebarCollapsed', JSON.stringify(sidebarCollapsed.value));
  } catch (e) {
    console.error('Error saving sidebar state to localStorage:', e);
  }
}

function collapseSidebar() {
  sidebarCollapsed.value = true;
  try {
    localStorage.setItem('sidebarCollapsed', JSON.stringify(true));
  } catch (e) {
    console.error('Error saving sidebar state to localStorage:', e);
  }
}

function expandSidebar() {
  sidebarCollapsed.value = false;
  try {
    localStorage.setItem('sidebarCollapsed', JSON.stringify(false));
  } catch (e) {
    console.error('Error saving sidebar state to localStorage:', e);
  }
}

// Export as a composable function
export function useLayoutStore() {
  return {
    sidebarCollapsed: readonly(sidebarCollapsed),
    toggleSidebar,
    collapseSidebar,
    expandSidebar
  };
}
