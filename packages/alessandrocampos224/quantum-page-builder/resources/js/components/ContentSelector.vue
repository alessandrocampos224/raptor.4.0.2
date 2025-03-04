<!-- ContentSelector.vue -->
<template>
  <div class="space-y-4">
    <div>
      <Label class="mb-1">Tipo de Conteúdo</Label>
      <Select v-model="contentType" @update:modelValue="handleContentTypeChange">
        <SelectTrigger>
          <SelectValue :placeholder="contentType ? contentTypeLabels[contentType] : 'Selecione o tipo de conteúdo'" />
        </SelectTrigger>
        <SelectContent>
          <SelectItem value="post">Post</SelectItem>
          <SelectItem value="category">Categoria</SelectItem>
        </SelectContent>
      </Select>
    </div>

    <div v-if="contentType === 'post'">
      <Label class="mb-1">Selecionar Post</Label>
      <Select v-model="selectedPostId" @update:modelValue="handlePostChange">
        <SelectTrigger :disabled="loading">
          <SelectValue :placeholder="loading ? 'Carregando...' : 'Selecione um post'" />
        </SelectTrigger>
        <SelectContent>
          <div v-if="loading" class="flex items-center justify-center p-4">
            <div class="animate-spin h-5 w-5 border-2 border-gray-500 rounded-full border-t-transparent"></div>
          </div>
          <div v-else-if="error" class="p-2 text-sm text-red-500">
            {{ error }}
          </div>
          <div v-else-if="posts.length === 0" class="p-2 text-sm text-gray-500">
            Nenhum post encontrado
          </div>
          <template v-else>
            <SelectItem v-for="post in posts" :key="post.id" :value="post.id">
              {{ post.name }}
            </SelectItem>
          </template>
        </SelectContent>
      </Select>
    </div>

    <div v-if="contentType === 'category'">
      <Label class="mb-1">Selecionar Categoria</Label>
      <Select v-model="selectedCategoryId" @update:modelValue="handleCategoryChange">
        <SelectTrigger :disabled="loading">
          <SelectValue :placeholder="loading ? 'Carregando...' : 'Selecione uma categoria'" />
        </SelectTrigger>
        <SelectContent>
          <div v-if="loading" class="flex items-center justify-center p-4">
            <div class="animate-spin h-5 w-5 border-2 border-gray-500 rounded-full border-t-transparent"></div>
          </div>
          <div v-else-if="error" class="p-2 text-sm text-red-500">
            {{ error }}
          </div>
          <div v-else-if="categories.length === 0" class="p-2 text-sm text-gray-500">
            Nenhuma categoria encontrada
          </div>
          <template v-else>
            <SelectItem v-for="category in categories" :key="category.id" :value="category.id">
              {{ category.name }} ({{ category.posts_count }} posts)
            </SelectItem>
          </template>
        </SelectContent>
      </Select>
    </div>

    <div v-if="selectedContent" class="p-4 bg-gray-50 dark:bg-gray-800 rounded-md">
      <div class="text-sm font-medium mb-2">Conteúdo selecionado:</div>
      <div class="text-sm">{{ selectedContent.name }}</div>
      <div v-if="selectedContent.description" class="text-xs text-gray-500 mt-1">
        {{ selectedContent.description }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, computed } from 'vue'
import { Label } from './ui/label'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from './ui/select'
import axios from 'axios'

const props = defineProps({
  modelValue: {
    type: Object,
    default: () => ({
      type: null,
      id: null,
      data: null
    })
  }
})

const emit = defineEmits(['update:modelValue'])

// Estado local
const contentType = ref(props.modelValue?.type || null)
const selectedPostId = ref(props.modelValue?.type === 'post' ? props.modelValue.id : null)
const selectedCategoryId = ref(props.modelValue?.type === 'category' ? props.modelValue.id : null)
const posts = ref([])
const categories = ref([])
const loading = ref(false)
const error = ref(null)

// Mapeamento de tipos para labels
const contentTypeLabels = {
  post: 'Post',
  category: 'Categoria'
}

// Conteúdo selecionado
const selectedContent = computed(() => {
  if (contentType.value === 'post' && selectedPostId.value) {
    return posts.value.find(post => post.id === selectedPostId.value)
  } else if (contentType.value === 'category' && selectedCategoryId.value) {
    return categories.value.find(category => category.id === selectedCategoryId.value)
  }
  return null
})

// Configuração do Axios para incluir o token CSRF
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
axios.defaults.withCredentials = true;

// Carregar posts
const loadPosts = async () => {
  loading.value = true
  error.value = null
  try {
    const response = await axios.get('/api/page-builder/posts')
    if (response.data.success) {
      posts.value = response.data.data
    } else {
      error.value = response.data.message || 'Erro ao carregar posts'
      console.error('Erro ao carregar posts:', response.data.message)
    }
  } catch (error) {
    if (error.response && error.response.status === 401) {
      error.value = 'Você precisa estar autenticado para acessar os posts'
    } else {
      error.value = 'Erro ao carregar posts. Tente novamente mais tarde.'
    }
    console.error('Erro ao carregar posts:', error)
  } finally {
    loading.value = false
  }
}

// Carregar categorias
const loadCategories = async () => {
  loading.value = true
  error.value = null
  try {
    const response = await axios.get('/api/page-builder/categories')
    if (response.data.success) {
      categories.value = response.data.data
    } else {
      error.value = response.data.message || 'Erro ao carregar categorias'
      console.error('Erro ao carregar categorias:', response.data.message)
    }
  } catch (error) {
    if (error.response && error.response.status === 401) {
      error.value = 'Você precisa estar autenticado para acessar as categorias'
    } else {
      error.value = 'Erro ao carregar categorias. Tente novamente mais tarde.'
    }
    console.error('Erro ao carregar categorias:', error)
  } finally {
    loading.value = false
  }
}

// Manipuladores de eventos
const handleContentTypeChange = (value) => {
  if (value === 'post') {
    selectedCategoryId.value = null
    if (posts.value.length === 0) {
      loadPosts()
    }
  } else if (value === 'category') {
    selectedPostId.value = null
    if (categories.value.length === 0) {
      loadCategories()
    }
  }
  
  updateModelValue()
}

const handlePostChange = (value) => {
  updateModelValue()
}

const handleCategoryChange = (value) => {
  updateModelValue()
}

// Atualizar o v-model
const updateModelValue = () => {
  let data = null
  
  if (contentType.value === 'post' && selectedPostId.value) {
    data = posts.value.find(post => post.id === selectedPostId.value)
  } else if (contentType.value === 'category' && selectedCategoryId.value) {
    data = categories.value.find(category => category.id === selectedCategoryId.value)
  }
  
  emit('update:modelValue', {
    type: contentType.value,
    id: contentType.value === 'post' ? selectedPostId.value : selectedCategoryId.value,
    data
  })
}

// Observar mudanças no v-model
watch(() => props.modelValue, (newValue) => {
  if (newValue) {
    contentType.value = newValue.type
    if (newValue.type === 'post') {
      selectedPostId.value = newValue.id
      if (posts.value.length === 0) {
        loadPosts()
      }
    } else if (newValue.type === 'category') {
      selectedCategoryId.value = newValue.id
      if (categories.value.length === 0) {
        loadCategories()
      }
    }
  }
}, { deep: true })

// Carregar dados iniciais se necessário
onMounted(() => {
  if (contentType.value === 'post' && posts.value.length === 0) {
    loadPosts()
  } else if (contentType.value === 'category' && categories.value.length === 0) {
    loadCategories()
  }
})
</script> 