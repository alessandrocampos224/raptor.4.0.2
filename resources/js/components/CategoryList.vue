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
import { onMounted, watch } from 'vue'

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

// Log para depuração
onMounted(() => {
  console.log('CategoryList montado com categorias:', props.categories)
})

// Observar mudanças nas categorias
watch(() => props.categories, (newCategories) => {
  console.log('CategoryList - categorias atualizadas:', newCategories)
}, { deep: true })
</script>

<style scoped>
.categories-list {
  @apply bg-white p-4 rounded-lg shadow-sm border border-gray-200;
}

.category-item:not(:last-child) {
  @apply border-b border-gray-100;
}
</style> 