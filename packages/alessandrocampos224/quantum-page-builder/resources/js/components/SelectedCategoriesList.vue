<!-- SelectedCategoriesList.vue -->
<template>
  <CategoryList 
    :title="title"
    :categories="formattedCategories"
  />
</template>

<script setup>
import { ref, watch } from 'vue'
import CategoryList from './CategoryList.vue'
import axios from 'axios'

const props = defineProps({
  title: {
    type: String,
    default: 'Categorias'
  },
  selectedCategories: {
    type: Array,
    required: true
  }
})

const formattedCategories = ref([])

const loadCategories = async () => {
  try {
    const response = await axios.get('/api/page-builder/categories')
    if (response.data.success) {
      // Filtra apenas as categorias selecionadas e formata para o componente
      formattedCategories.value = response.data.data
        .filter(category => props.selectedCategories.includes(category.id))
        .map(category => ({
          id: category.id,
          name: category.name,
          url: `/categorias/${category.slug}`,
          highlight: false
        }))
    }
  } catch (error) {
    console.error('Erro ao carregar categorias:', error)
  }
}

// Recarrega as categorias quando a lista de selecionadas mudar
watch(() => props.selectedCategories, () => {
  loadCategories()
}, { deep: true })

// Carrega as categorias inicialmente
loadCategories()
</script> 