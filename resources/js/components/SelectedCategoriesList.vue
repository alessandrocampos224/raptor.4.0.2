<!-- SelectedCategoriesList.vue -->
<template>
  <div>
    <div v-if="isLoading" class="flex items-center justify-center py-4">
      <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-primary-600"></div>
    </div>
    <div v-else-if="error" class="p-2 text-sm text-red-500">
      {{ error }}
    </div>
    <div v-else-if="formattedCategories.length === 0" class="p-2 text-sm text-gray-500">
      Nenhuma categoria selecionada ou as categorias não puderam ser carregadas.
    </div>
    <CategoryList 
      v-else
      :title="title"
      :categories="formattedCategories"
    />
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
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
const isLoading = ref(true)
const error = ref(null)

const loadCategories = async () => {
  // Resetar estado
  isLoading.value = true
  error.value = null
  
  // Se não houver categorias selecionadas, não fazer a requisição
  if (!props.selectedCategories || props.selectedCategories.length === 0) {
    formattedCategories.value = []
    isLoading.value = false
    console.log('Nenhuma categoria selecionada')
    return
  }
  
  console.log('Carregando categorias selecionadas:', props.selectedCategories)
  
  try {
    const response = await axios.get('/api/page-builder/categories')
    console.log('Resposta da API:', response.data)
    
    if (response.data.success) {
      // Filtra apenas as categorias selecionadas e formata para o componente
      const allCategories = response.data.data
      console.log('Todas as categorias disponíveis:', allCategories)
      
      formattedCategories.value = allCategories
        .filter(category => {
          const isSelected = props.selectedCategories.includes(category.id)
          console.log(`Categoria ${category.id} (${category.name}) está selecionada? ${isSelected}`)
          return isSelected
        })
        .map(category => ({
          id: category.id,
          name: category.name,
          url: `/categorias/${category.slug}`,
          highlight: false
        }))
      
      console.log('Categorias formatadas:', formattedCategories.value)
    } else {
      error.value = 'Erro ao carregar categorias: ' + (response.data.message || 'Resposta inválida')
      console.error('Erro na resposta da API:', response.data)
    }
  } catch (err) {
    error.value = 'Erro ao carregar categorias: ' + (err.message || 'Erro desconhecido')
    console.error('Erro ao carregar categorias:', err)
  } finally {
    isLoading.value = false
  }
}

// Recarrega as categorias quando a lista de selecionadas mudar
watch(() => props.selectedCategories, (newValue) => {
  console.log('Lista de categorias selecionadas alterada:', newValue)
  loadCategories()
}, { deep: true, immediate: true })

// Carrega as categorias inicialmente
onMounted(() => {
  loadCategories()
})
</script> 