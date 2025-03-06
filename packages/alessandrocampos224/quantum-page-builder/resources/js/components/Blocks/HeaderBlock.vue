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
      containerWidth === 'full' ? 'w-full' : 'container mx-auto',
      globalTheme === 'dark' ? 'dark-theme text-white' : ''
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
      textAlignment === 'center' ? 'text-center' : '',
      textAlignment === 'right' ? 'text-right' : ''
    ]">
      <!-- Conteúdo estático ou item único -->
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

        <!-- Link para o post quando for item único -->
        <div v-if="dataSource === 'dynamic' && contentType === 'single' && contentLink?.data" class="mt-6">
          <a 
            :href="`/posts/${contentLink.data.slug}`"
            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-tenant-primary hover:bg-tenant-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-tenant-primary"
          >
            Ver post completo
          </a>
        </div>

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
        <div v-if="isLoading" class="flex items-center justify-center py-4">
          <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-primary-600"></div>
        </div>
        <div v-else>
          <!-- Layout Grid -->
          <div v-if="layout === 'grid'" class="categories-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div 
              v-for="category in loadedCategories" 
              :key="category.id"
              class="category-card bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-all p-6 border border-gray-200 dark:border-gray-700"
            >
              <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ category.name }}</h3>
              <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">{{ category.description || 'Sem descrição' }}</p>
              <a :href="`/categorias/${category.slug}`" class="text-tenant-primary hover:underline text-sm font-medium">
                Ver mais
              </a>
            </div>
          </div>
          
          <!-- Layout Lista -->          
          <div v-else class="categories-list space-y-2">
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Categorias</h2>
              <span class="text-sm text-gray-500">{{ loadedCategories.length }} categorias</span>
            <div 
              v-for="category in loadedCategories" 
              :key="category.id"
              class="category-item"
            >
              <a 
                :href="`/categorias/${category.slug}`" 
                class="text-gray-800 dark:text-gray-200 hover:text-tenant-primary dark:hover:text-tenant-primary transition-colors duration-200 font-medium text-base uppercase"
              >
                {{ category.name }}
              </a>
            </div>
          </div>
        </div>
      </template>

      <!-- Listagem de posts -->
      <template v-else-if="dataSource === 'dynamic' && contentType === 'posts'">
        <div v-if="isLoading" class="flex items-center justify-center py-4">
          <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-primary-600"></div>
        </div>
        <div v-else>
          <div class="posts-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div 
              v-for="post in posts" 
              :key="post.id"
              class="post-card bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-all overflow-hidden"
            >
              <div v-if="post.featured_image" class="aspect-w-16 aspect-h-9">
                <img 
                  :src="post.featured_image" 
                  :alt="post.name"
                  class="w-full h-48 object-cover"
                />
              </div>
              <div class="p-6">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">
                  {{ post.name }}
                </h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 line-clamp-3">
                  {{ post.description || 'Sem descrição' }}
                </p>
                <div class="flex justify-between items-center">
                  <a 
                    :href="`/posts/${post.slug}`"
                    class="text-tenant-primary hover:text-tenant-primary-dark font-medium text-sm"
                  >
                    Ler mais
                  </a>
                  <span v-if="post.category" class="text-xs text-gray-500">
                    {{ post.category.name }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </template>
    </div>

    <!-- Área para componentes aninhados -->
    <div v-if="allowNesting" class="nested-components-container">
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
  columnSpan: {
    type: [Number, String],
    default: 12
  },
  allowNesting: {
    type: Boolean,
    default: false
  },
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
  },
  layout: {
    type: String,
    default: 'grid'
  },
  globalTheme: {
    type: String,
    default: 'light'
  }
})

const isLoading = ref(false)
const error = ref(null)
const loadedCategories = ref([])
const posts = ref([])

const displayTitle = computed(() => {
  if (props.dataSource === 'dynamic' && props.contentLink?.data) {
    return props.contentLink.data.name || props.title
  }
  return props.title
})

const displaySubtitle = computed(() => {
  if (props.dataSource === 'dynamic' && props.contentLink?.data) {
    return props.contentLink.data.description || props.subtitle
  }
  return props.subtitle
})

const displayedPosts = computed(() => {
  return posts.value.slice(0, props.postsLimit)
})

const loadCategories = async () => {
  if (!props.selectedCategories?.length) return
  
  isLoading.value = true
  error.value = null
  
  try {
    const response = await axios.get('/api/page-builder/categories')
    if (response.data.success) {
      loadedCategories.value = response.data.data.filter(category => 
        props.selectedCategories.includes(category.id)
      )
    } else {
      error.value = response.data.message || 'Erro ao carregar categorias'
    }
  } catch (err) {
    error.value = 'Erro ao carregar categorias'
    console.error('Erro ao carregar categorias:', err)
  } finally {
    isLoading.value = false
  }
}

const loadPosts = async () => {
  if (!props.selectedPosts?.length) return
  
  isLoading.value = true
  error.value = null
  
  try {
    const url = props.filterCategory !== 'all' 
      ? `/api/page-builder/categories/${props.filterCategory}/posts`
      : '/api/page-builder/posts'
      
    const response = await axios.get(url)
    if (response.data.success) {
      posts.value = response.data.data.filter(post => props.selectedPosts.includes(post.id))
    } else {
      error.value = response.data.message || 'Erro ao carregar posts'
    }
  } catch (err) {
    error.value = 'Erro ao carregar posts'
    console.error('Erro ao carregar posts:', err)
  } finally {
    isLoading.value = false
  }
}

const updateSelectedPosts = (postId, checked) => {
  let updatedPosts = [...props.selectedPosts]
  if (checked) {
    if (!updatedPosts.includes(postId)) {
      if (updatedPosts.length < props.postsLimit) {
        updatedPosts.push(postId)
      } else {
        console.warn(`Limite de ${props.postsLimit} posts atingido`)
        return
      }
    }
  } else {
    updatedPosts = updatedPosts.filter(id => id !== postId)
  }
  emit('update:selectedPosts', updatedPosts)
}

watch(() => props.selectedCategories, () => {
  if (props.dataSource === 'dynamic' && props.contentType === 'category') {
    loadCategories()
  }
}, { deep: true })

watch(() => [props.filterCategory, props.contentType], () => {
  if (props.dataSource === 'dynamic' && props.contentType === 'posts') {
    loadPosts()
  }
}, { deep: true })

// Watch para mudanças nos posts selecionados
watch(() => props.selectedPosts, () => {
  if (props.dataSource === 'dynamic' && props.contentType === 'posts') {
    loadPosts()
  }
}, { deep: true })

onMounted(() => {
  if (props.dataSource === 'dynamic') {
    if (props.contentType === 'category') {
      loadCategories()
    } else if (props.contentType === 'posts') {
      loadPosts()
    }
  }
})

const getCategoryName = (categoryId) => {
  const category = loadedCategories.value.find(cat => cat.id === parseInt(categoryId))
  return category ? category.name : 'Categoria'
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

.category-item:not(:last-child) {
  @apply border-b border-gray-100;
}
</style> 