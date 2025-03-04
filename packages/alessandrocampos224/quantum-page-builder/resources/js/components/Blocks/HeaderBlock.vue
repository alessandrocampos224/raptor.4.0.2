<!-- HeaderBlock.vue -->
<template>
  <header 
    :class="[
      'header-block relative',
      `bg-${backgroundColor}`,
      `shadow-${shadow}`,
      `border-${borderStyle}`,
      `border-${borderWidth}`,
      `border-${borderColor}`,
      `rounded-${borderRadius}`,
      `py-${paddingY}`,
      `px-${paddingX}`,
      backgroundImage ? 'bg-cover bg-center' : '',
      overlayOpacity ? 'relative' : '',
      textAlignment === 'center' ? 'text-center' : '',
      textAlignment === 'right' ? 'text-right' : '',
    ]"
    :style="{
      backgroundImage: backgroundImage ? `url(${backgroundImage})` : null,
    }"
  >
    <!-- Overlay com opacidade -->
    <div 
      v-if="overlayOpacity"
      class="absolute inset-0 bg-black"
      :style="{ opacity: overlayOpacity }"
    ></div>

    <div :class="[
      'relative z-10',
      containerWidth === 'full' ? '' : 'container mx-auto',
      containerWidth === 'narrow' ? 'max-w-4xl' : ''
    ]">
      <!-- Conteúdo estático -->
      <template v-if="dataSource !== 'dynamic' || contentType === 'single'">
        <h1 :class="[
          {
            'text-xl': titleSize === 'xl',
            'text-2xl': titleSize === '2xl',
            'text-3xl': titleSize === '3xl',
            'text-5xl': titleSize === '5xl',
            'font-normal': titleWeight === 'normal',
            'font-medium': titleWeight === 'medium',
            'font-semibold': titleWeight === 'semibold',
            'font-bold': titleWeight === 'bold',
            'text-gray-900': titleColor === 'gray-900',
            'text-gray-700': titleColor === 'gray-700',
            'text-tenant-primary': titleColor === 'tenant-primary'
          },
          titleFont,
          'leading-tight'
        ]">
          {{ displayTitle }}
        </h1>
        
        <p 
          v-if="displaySubtitle"
          :class="[
            'mt-4',
            'text-' + subtitleSize,
            'font-' + subtitleWeight,
            'text-' + subtitleColor,
            subtitleFont
          ]"
        >
          {{ displaySubtitle }}
        </p>

        <!-- Botões de ação -->
        <div 
          v-if="actions && actions.length > 0"
          :class="[
            'mt-8',
            'flex',
            textAlignment === 'center' ? 'justify-center' : '',
            textAlignment === 'right' ? 'justify-end' : '',
            'gap-4'
          ]"
        >
          <a
            v-for="(action, index) in actions"
            :key="index"
            :href="action.url"
            :class="[
              'px-6 py-3 rounded-md font-medium transition-all duration-200',
              action.primary ? 'bg-tenant-primary text-white hover:bg-tenant-primary-dark' : 'border border-tenant-primary text-tenant-primary hover:bg-tenant-primary hover:text-white'
            ]"
          >
            {{ action.text }}
          </a>
        </div>
      </template>

      <!-- Listagem de categorias -->
      <template v-else-if="dataSource === 'dynamic' && contentType === 'category' && selectedCategories && selectedCategories.length > 0">
        <div class="categories-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div 
            v-for="(category, index) in selectedCategories" 
            :key="index"
            class="category-card bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-all p-6 border border-gray-200 dark:border-gray-700"
          >
            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ category.name }}</h3>
            <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">{{ category.description || 'Sem descrição' }}</p>
            <a :href="`/categorias/${category.slug}`" class="text-tenant-primary hover:underline text-sm font-medium">
              Ver mais
            </a>
          </div>
        </div>
      </template>

      <!-- Listagem de posts -->
      <template v-else-if="dataSource === 'dynamic' && contentType === 'posts'">
        <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-6">
          {{ filterCategory === 'all' ? 'Todos os Posts' : `Posts da categoria: ${getCategoryName(filterCategory)}` }}
        </h2>
        
        <div v-if="isLoading" class="flex items-center justify-center py-4">
          <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-primary-600"></div>
        </div>
        <div v-else-if="error" class="p-2 text-sm text-red-500">
          {{ error }}
        </div>
        <div v-else class="posts-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div 
            v-for="post in filteredPosts.slice(0, postsLimit)" 
            :key="post.id"
            class="post-card bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-all overflow-hidden"
          >
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
              <div class="flex items-center space-x-2">
                <Checkbox 
                  :id="`post-${post.id}`"
                  :checked="localSelectedPosts.includes(post.id)"
                  @update:checked="(checked) => updateSelectedPosts(post.id, checked)"
                />
                <label :for="`post-${post.id}`" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                  Selecionar
                </label>
              </div>
            </div>
            <div v-if="post.image" class="post-image h-40 bg-gray-200 dark:bg-gray-700">
              <img :src="post.image" :alt="post.name" class="w-full h-full object-cover">
            </div>
            <div class="p-6">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ post.name }}</h3>
              <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">{{ post.description }}</p>
              <div class="flex justify-between items-center">
                <span class="text-xs text-gray-500 dark:text-gray-500">{{ post.created_at }}</span>
                <a :href="`/posts/${post.slug}`" class="text-tenant-primary hover:underline text-sm font-medium">
                  Ler mais
                </a>
              </div>
            </div>
          </div>
        </div>
      </template>
    </div>

    <!-- Área para componentes aninhados -->
    <div v-if="allowNesting" class="nested-components-container mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
      <slot></slot>
    </div>
  </header>
</template>

<script setup>
import { computed, ref, onMounted, watch } from 'vue'
import axios from 'axios'
import { Checkbox } from '../ui/checkbox'

const emit = defineEmits(['update:selectedPosts'])

const props = defineProps({
  title: {
    type: String,
    default: 'Título do Cabeçalho'
  },
  subtitle: {
    type: String,
    default: ''
  },
  titleSize: {
    type: String,
    default: '3xl'
  },
  titleWeight: {
    type: String,
    default: 'bold'
  },
  titleColor: {
    type: String,
    default: 'gray-900'
  },
  titleFont: {
    type: String,
    default: 'font-sans'
  },
  subtitleSize: {
    type: String,
    default: 'lg'
  },
  subtitleWeight: {
    type: String,
    default: 'normal'
  },
  subtitleColor: {
    type: String,
    default: 'gray-700'
  },
  subtitleFont: {
    type: String,
    default: 'font-sans'
  },
  backgroundColor: {
    type: String,
    default: 'white'
  },
  backgroundImage: {
    type: String,
    default: ''
  },
  overlayOpacity: {
    type: [String, Number],
    default: 0
  },
  shadow: {
    type: String,
    default: 'none'
  },
  borderStyle: {
    type: String,
    default: 'none'
  },
  borderWidth: {
    type: String,
    default: '0'
  },
  borderColor: {
    type: String,
    default: 'gray-200'
  },
  borderRadius: {
    type: String,
    default: 'none'
  },
  paddingY: {
    type: String,
    default: '12'
  },
  paddingX: {
    type: String,
    default: '6'
  },
  containerWidth: {
    type: String,
    default: 'default'
  },
  textAlignment: {
    type: String,
    default: 'left'
  },
  actions: {
    type: Array,
    default: () => []
  },
  contentLink: {
    type: Object,
    default: null
  },
  dataSource: {
    type: String,
    default: 'static'
  },
  // Novas propriedades para o sistema de colunas e aninhamento
  columnSpan: {
    type: [Number, String],
    default: 12
  },
  allowNesting: {
    type: Boolean,
    default: false
  },
  // Novas propriedades para listagens dinâmicas
  contentType: {
    type: String,
    default: 'single'
  },
  selectedCategories: {
    type: Array,
    default: () => []
  },
  filterCategory: {
    type: [String, Number],
    default: 'all'
  },
  postsLimit: {
    type: Number,
    default: 6
  },
  selectedPosts: {
    type: Array,
    default: () => []
  }
})

// Título dinâmico ou estático
const displayTitle = computed(() => {
  if (props.dataSource === 'dynamic' && props.contentLink?.data) {
    return props.contentLink.data.name || props.contentLink.data.title || props.title
  }
  return props.title
})

// Subtítulo dinâmico ou estático
const displaySubtitle = computed(() => {
  if (props.dataSource === 'dynamic' && props.contentLink?.data) {
    return props.contentLink.data.description || props.contentLink.data.excerpt || props.subtitle
  }
  return props.subtitle
})

// Estado para os posts
const posts = ref([])
const isLoading = ref(false)
const error = ref(null)

// Estado local para seleção de posts
const localSelectedPosts = ref([])

// Inicializar com os posts já selecionados
watch(() => props.selectedPosts, (newValue) => {
  localSelectedPosts.value = [...newValue]
}, { immediate: true })

// Função para atualizar seleção de posts
const updateSelectedPosts = (postId, checked) => {
  if (checked) {
    if (!localSelectedPosts.value.includes(postId)) {
      localSelectedPosts.value.push(postId)
    }
  } else {
    const index = localSelectedPosts.value.indexOf(postId)
    if (index !== -1) {
      localSelectedPosts.value.splice(index, 1)
    }
  }
  emit('update:selectedPosts', localSelectedPosts.value)
}

// Função para carregar posts
const loadPosts = async (categoryId = null) => {
  try {
    isLoading.value = true
    error.value = null
    const url = categoryId 
      ? `/api/page-builder/categories/${categoryId}/posts`
      : '/api/page-builder/posts'
    const response = await axios.get(url)
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
    isLoading.value = false
  }
}

// Carregar posts quando o componente é montado
onMounted(async () => {
  if (props.dataSource === 'dynamic' && props.contentType === 'posts') {
    await loadPosts(props.filterCategory !== 'all' ? props.filterCategory : null)
  }
})

// Watch para mudanças na categoria selecionada
watch(() => props.filterCategory, async (newValue) => {
  if (props.dataSource === 'dynamic' && props.contentType === 'posts') {
    if (newValue && newValue !== 'all') {
      await loadPosts(newValue)
    } else {
      await loadPosts()
    }
  }
})

// Computed para filtrar os posts
const filteredPosts = computed(() => {
  if (!posts.value) return []
  return props.selectedPosts && props.selectedPosts.length > 0
    ? posts.value.filter(post => props.selectedPosts.includes(post.id))
    : posts.value
})

// Obter nome da categoria pelo ID
const getCategoryName = (categoryId) => {
  const categories = [
    { id: 1, name: 'Audiência Pública' },
    { id: 2, name: 'Prova de Vida' },
    { id: 3, name: 'Auditoria' },
    { id: 4, name: 'Certificação' },
    { id: 5, name: 'Notícias' },
    { id: 6, name: 'Convivência' },
    { id: 7, name: 'Serviços' },
    { id: 8, name: 'Formação' },
    { id: 9, name: 'Informativo' }
  ]
  
  const category = categories.find(cat => cat.id === parseInt(categoryId))
  return category ? category.name : 'Categoria Desconhecida'
}
</script>

<style scoped>
.header-block {
  @apply py-12;
}

/* Estilos */
.style-default {
  @apply bg-white dark:bg-gray-800;
}

.style-highlighted {
  @apply bg-gray-50 dark:bg-gray-800;
}

.style-bordered {
  @apply border-b border-gray-200 dark:border-gray-700;
}

/* Layouts */
.layout-default {
  @apply w-full;
}

.layout-centered {
  @apply text-center;
}

.layout-narrow {
  @apply max-w-4xl mx-auto;
}

.layout-wide {
  @apply max-w-7xl mx-auto;
}

/* Espaçamentos Verticais */
.spacing-v-default {
  @apply py-12;
}

.spacing-v-compact {
  @apply py-8;
}

.spacing-v-comfortable {
  @apply py-16;
}

.spacing-v-spacious {
  @apply py-20;
}

/* Espaçamentos Horizontais */
.spacing-h-default {
  @apply px-4 sm:px-6 lg:px-8;
}

.spacing-h-compact {
  @apply px-4;
}

.spacing-h-comfortable {
  @apply px-6 sm:px-8 lg:px-10;
}

.spacing-h-spacious {
  @apply px-8 sm:px-10 lg:px-12;
}
</style> 