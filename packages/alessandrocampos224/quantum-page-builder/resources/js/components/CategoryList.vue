<!-- CategoryList.vue -->
<template>
  <div class="categories-list">
    <h2 class="text-lg font-semibold mb-4">{{ title }}</h2>
    <div class="space-y-2">
      <div v-for="category in categories" :key="category.id" class="category-item">
        <a :href="category.url" class="flex items-center py-2 px-3 hover:bg-gray-100 rounded-md transition-colors">
          <span :class="[
            'flex-1',
            category.highlight ? 'text-blue-600 font-medium' : 'text-gray-700'
          ]">{{ category.name }}</span>
        </a>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  title: {
    type: String,
    default: 'Categorias'
  },
  categories: {
    type: Array,
    required: true,
    default: () => []
  }
})
</script>

<style scoped>
.categories-list {
  @apply bg-white p-4 rounded-lg shadow-sm border border-gray-200;
}

.category-item:not(:last-child) {
  @apply border-b border-gray-100;
}
</style> 