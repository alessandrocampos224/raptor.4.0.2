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
      <div class="mt-2 text-xs">
        IDs selecionados: {{ JSON.stringify(selectedCategories) }}
      </div>
    </div>
    <div v-else>
      <CategoryList 
        :title="title"
        :categories="formattedCategories"
      />
      <div class="mt-2 text-xs text-gray-500">
        Total de categorias exibidas: {{ formattedCategories.length }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import CategoryList from './CategoryList.vue'
import axios from 'axios'
import { useToast } from '@/components/ui/toast'

const props = defineProps({
  title: {
    type: String,
    default: 'Categorias'
  },
  selectedCategories: {
    type: Array,
    default: () => []
  },
  slug: {
    type: String,
    default: null
  }
})

const emit = defineEmits(['update:selected-categories'])

const { toast } = useToast()
const formattedCategories = ref([])
const isLoading = ref(false)
const error = ref(null)
const allCategories = ref([])

// Adiciona um watcher para detectar mudanças nas categorias selecionadas
watch(() => props.selectedCategories, (newVal, oldVal) => {
  console.log('SelectedCategoriesList - selectedCategories mudou:', newVal)
  if (JSON.stringify(newVal) !== JSON.stringify(oldVal)) {
    loadCategories()
  }
}, { deep: true })

onMounted(() => {
  console.log('SelectedCategoriesList - Componente montado com categorias:', props.selectedCategories)
  loadCategories()
})

const loadCategories = async () => {
  console.log('SelectedCategoriesList - loadCategories chamado com:', props.selectedCategories)
  
  // Verifica se há categorias selecionadas
  if (!props.selectedCategories || !Array.isArray(props.selectedCategories) || props.selectedCategories.length === 0) {
    console.log('SelectedCategoriesList - Nenhuma categoria selecionada ou array inválido:', props.selectedCategories)
    formattedCategories.value = []
    return
  }

  isLoading.value = true
  error.value = null

  try {
    console.log('SelectedCategoriesList - Carregando categorias selecionadas:', props.selectedCategories)
    
    // Constrói a URL com base na presença ou não do slug
    const url = props.slug 
      ? `/api/page-builder/categories?slug=${props.slug}` 
      : '/api/page-builder/categories'
    
    console.log('SelectedCategoriesList - URL da requisição:', url)
    
    const response = await axios.get(url)
    console.log('SelectedCategoriesList - Resposta da API:', response.data)
    
    if (response.data && response.data.data) {
      allCategories.value = response.data.data
      console.log('SelectedCategoriesList - Todas as categorias disponíveis:', allCategories.value)
      
      // Mapeia os IDs das categorias disponíveis para facilitar a comparação
      const availableCategoryIds = allCategories.value.map(cat => String(cat.id))
      console.log('SelectedCategoriesList - IDs de categorias disponíveis:', availableCategoryIds)
      
      // Verifica quais IDs selecionados existem nas categorias disponíveis
      const validSelectedIds = props.selectedCategories
        .map(id => String(id))
        .filter(id => availableCategoryIds.includes(id))
      
      console.log('SelectedCategoriesList - IDs selecionados válidos:', validSelectedIds)
      
      // Filtra as categorias com base nos IDs selecionados
      formattedCategories.value = allCategories.value
        .filter(category => validSelectedIds.includes(String(category.id)))
        .map(category => ({
          id: category.id,
          name: category.name,
          url: `/categorias/${category.slug || category.id}`,
          highlight: false
        }))
      
      console.log('SelectedCategoriesList - Categorias formatadas para exibição:', formattedCategories.value)
      
      // Verifica se todas as categorias selecionadas foram encontradas
      if (formattedCategories.value.length !== validSelectedIds.length) {
        console.warn('SelectedCategoriesList - Algumas categorias selecionadas não foram encontradas na API')
        const foundIds = formattedCategories.value.map(c => String(c.id))
        const missingIds = validSelectedIds.filter(id => !foundIds.includes(id))
        console.warn('SelectedCategoriesList - IDs não encontrados:', missingIds)
      }
    } else {
      console.error('SelectedCategoriesList - Formato de resposta da API inesperado:', response.data)
      formattedCategories.value = []
    }
  } catch (e) {
    console.error('SelectedCategoriesList - Erro ao carregar categorias:', e)
    error.value = 'Erro ao carregar categorias. Por favor, tente novamente.'
    toast({
      title: 'Erro',
      description: 'Não foi possível carregar as categorias selecionadas.',
      variant: 'destructive'
    })
  } finally {
    isLoading.value = false
  }
}
</script> 