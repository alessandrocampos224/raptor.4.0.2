<template>
  <div class="category-links" :class="containerClasses">
    <!-- Título da seção se fornecido -->
    <h2 v-if="title" :class="titleClasses">{{ title }}</h2>
    
    <!-- Lista de categorias -->
    <div class="grid gap-4" :class="gridClasses">
      <a 
        v-for="category in selectedCategories" 
        :key="category.id"
        :href="getCategoryUrl(category)"
        class="category-link"
        :class="linkClasses"
      >
        <div class="flex items-center space-x-2">
          <component 
            :is="category.icon || 'FolderIcon'" 
            class="w-5 h-5"
            :class="iconClasses"
          />
          <span>{{ category.name }}</span>
        </div>
        
        <!-- Badge com contagem de posts se disponível -->
        <span 
          v-if="category.posts_count" 
          class="badge"
          :class="badgeClasses"
        >
          {{ category.posts_count }}
        </span>
      </a>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { FolderIcon } from 'lucide-vue-next'

const props = defineProps({
  // Dados das categorias
  categories: {
    type: Array,
    default: () => []
  },
  selectedCategoryIds: {
    type: Array,
    default: () => []
  },
  // Título da seção
  title: {
    type: String,
    default: ''
  },
  // Estilo e layout
  layout: {
    type: String,
    default: 'grid' // grid, list
  },
  columns: {
    type: Number,
    default: 3
  },
  style: {
    type: String,
    default: 'default' // default, cards, minimal
  },
  // Cores e tema
  theme: {
    type: String,
    default: 'light' // light, dark
  }
})

// Filtra apenas as categorias selecionadas
const selectedCategories = computed(() => {
  if (!props.selectedCategoryIds?.length) return props.categories
  return props.categories.filter(category => 
    props.selectedCategoryIds.includes(category.id)
  )
})

// Classes do container
const containerClasses = computed(() => ({
  'bg-white dark:bg-gray-800': props.style === 'cards',
  'p-6': props.style === 'cards',
  'rounded-lg': props.style === 'cards',
  'shadow-sm': props.style === 'cards'
}))

// Classes do título
const titleClasses = computed(() => ({
  'text-2xl font-bold mb-6': true,
  'text-gray-900 dark:text-white': props.theme === 'light',
  'text-white': props.theme === 'dark'
}))

// Classes do grid
const gridClasses = computed(() => ({
  [`grid-cols-${props.columns}`]: props.layout === 'grid',
  'grid-cols-1': props.layout === 'list'
}))

// Classes dos links
const linkClasses = computed(() => {
  const baseClasses = 'flex items-center justify-between p-4 rounded-lg transition-all duration-200'
  
  switch (props.style) {
    case 'cards':
      return `${baseClasses} bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600`
    case 'minimal':
      return `${baseClasses} hover:bg-gray-50 dark:hover:bg-gray-800`
    default:
      return `${baseClasses} border border-gray-200 dark:border-gray-700 hover:border-primary-500 dark:hover:border-primary-500`
  }
})

// Classes dos ícones
const iconClasses = computed(() => ({
  'text-primary-500': false,
  'text-blue-500': true
}))

// Classes das badges
const badgeClasses = computed(() => ({
  'px-2 py-1 text-xs font-medium rounded-full': true,
  'bg-blue-100 text-blue-800': props.theme === 'light',
  'bg-blue-800 text-blue-100': props.theme === 'dark'
}))

// Função para gerar URL da categoria
const getCategoryUrl = (category) => {
  return `/categories/${category.id}`
}
</script>

<style scoped>
.category-link {
  @apply no-underline text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400;
}

.badge {
  @apply inline-flex items-center justify-center;
}
</style> 