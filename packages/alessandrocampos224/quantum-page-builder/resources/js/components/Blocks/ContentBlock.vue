<!-- ContentBlock.vue -->
<template>
  <div 
    :class="[
      'content-block relative',
      `bg-${props.backgroundColor}`,
      `shadow-${props.shadow}`,
      `border-${props.borderStyle}`,
      `border-${props.borderWidth}`,
      `border-${props.borderColor}`,
      `rounded-${props.borderRadius}`,
      `py-${props.paddingY}`,
      `px-${props.paddingX}`,
      props.backgroundImage ? 'bg-cover bg-center' : '',
      props.overlayOpacity ? 'relative' : '',
      props.textAlignment === 'center' ? 'text-center' : '',
      props.textAlignment === 'right' ? 'text-right' : '',
      props.globalTheme === 'dark' ? 'dark-theme text-white' : ''
    ]"
    :style="{
      backgroundImage: props.backgroundImage ? `url(${props.backgroundImage})` : null,
    }"
  >
    <!-- Overlay com opacidade -->
    <div 
      v-if="props.overlayOpacity"
      class="absolute inset-0 bg-black"
      :style="{ opacity: props.overlayOpacity }"
    ></div>

    <!-- Container principal -->
    <div :class="[
      'relative z-10',
      props.containerWidth === 'full' ? '' : 'container mx-auto',
      props.containerWidth === 'narrow' ? 'max-w-4xl' : ''
    ]">
      <!-- Conteúdo dinâmico -->
      <template v-if="props.dataSource === 'dynamic'">
        <!-- Exibição de categorias -->
        <template v-if="props.contentType === 'category' && props.selectedCategories?.length">
          <CategoryLinks
            :categories="categories"
            :selected-category-ids="props.selectedCategories"
            :title="props.title"
            :layout="props.layout"
            :columns="props.columns"
            :style="props.style"
            :theme="props.theme"
          />
        </template>

        <!-- Exibição de posts -->
        <template v-else-if="props.contentType === 'posts'">
          <div class="grid gap-6" :class="[
            props.layout === 'grid' ? `grid-cols-${props.columns}` : 'grid-cols-1'
          ]">
            <a 
              v-for="post in posts" 
              :key="post.id"
              :href="`/posts/${post.id}`"
              class="post-card"
              :class="[
                props.style === 'cards' ? 'bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6' : '',
                props.style === 'minimal' ? 'hover:bg-gray-50 dark:hover:bg-gray-800' : '',
                props.style === 'default' ? 'border border-gray-200 dark:border-gray-700 hover:border-blue-500 dark:hover:border-blue-500 rounded-lg p-6' : ''
              ]"
            >
              <div class="flex flex-col gap-4">
                <!-- Imagem do post se disponível -->
                <img 
                  v-if="post.image" 
                  :src="post.image" 
                  :alt="post.title"
                  class="w-full h-48 object-cover rounded-lg"
                />
                
                <!-- Título e meta informações -->
                <div class="space-y-2">
                  <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    {{ post.name }}
                  </h3>
                  
                  <div class="flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                    <span>{{ formatDate(post.created_at) }}</span>
                    <span v-if="post.category">
                      {{ post.category.name }}
                    </span>
                  </div>
                  
                  <!-- Descrição -->
                  <p class="text-gray-600 dark:text-gray-300 line-clamp-3">
                    {{ post.description }}
                  </p>
                </div>
              </div>
            </a>
          </div>
        </template>
      </template>

      <!-- Conteúdo estático -->
      <template v-else>
        <div class="prose dark:prose-invert max-w-none">
          <div v-if="props.useMarkdown" v-html="renderedContent"></div>
          <div v-else class="whitespace-pre-wrap">{{ displayContent }}</div>
        </div>
      </template>
    </div>

    <!-- Área para componentes aninhados -->
    <div v-if="allowNesting" class="nested-components-container">
      <slot></slot>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, onMounted, watch } from 'vue'
import { marked } from 'marked'
import axios from 'axios'
import CategoryLinks from './CategoryLinks.vue'

const props = defineProps({
  content: { type: String, default: '' },
  useMarkdown: { type: Boolean, default: true },
  dataSource: { type: String, default: 'static' },
  contentLink: { type: Object, default: null },
  containerWidth: { type: String, default: 'default' },
  textAlignment: { type: String, default: 'left' },
  backgroundColor: { type: String, default: 'white' },
  backgroundImage: { type: String, default: '' },
  overlayOpacity: { type: Number, default: 0 },
  shadow: { type: String, default: 'none' },
  borderStyle: { type: String, default: 'none' },
  borderWidth: { type: String, default: '0' },
  borderColor: { type: String, default: 'gray-200' },
  borderRadius: { type: String, default: 'none' },
  paddingY: { type: String, default: '12' },
  paddingX: { type: String, default: '4' },
  columnSpan: { type: [Number, String], default: 12 },
  allowNesting: { type: Boolean, default: false },
  contentType: { type: String, default: 'single' },
  selectedCategories: { type: Array, default: () => [] },
  filterCategory: { type: [String, Number], default: 'all' },
  postsLimit: { type: [Number, String], default: 6 },
  title: { type: String, default: '' },
  layout: { type: String, default: 'grid' },
  columns: { type: Number, default: 3 },
  style: { type: [String, Object], default: 'default' },
  theme: { type: String, default: 'light' },
  globalTheme: {
    type: String,
    default: 'light'
  }
})

// Estado
const categories = ref([])
const posts = ref([])
const isLoading = ref(false)

// Computed
const displayContent = computed(() => {
  if (props.dataSource === 'dynamic' && props.contentLink?.data) {
    return props.contentLink.data.description || ''
  }
  return props.content
})

const renderedContent = computed(() => {
  const contentToRender = displayContent.value
  if (!contentToRender) return ''
  return marked(contentToRender)
})

// Métodos
const loadCategories = async () => {
  try {
    isLoading.value = true
    const response = await axios.get('/api/page-builder/categories')
    categories.value = response.data.data
  } catch (error) {
    console.error('Erro ao carregar categorias:', error)
  } finally {
    isLoading.value = false
  }
}

const loadPosts = async () => {
  try {
    isLoading.value = true
    const url = props.filterCategory && props.filterCategory !== 'all'
      ? `/api/page-builder/categories/${props.filterCategory}/posts`
      : '/api/page-builder/posts'
    
    const response = await axios.get(url, {
      params: {
        limit: props.postsLimit
      }
    })

    if (response.data.success) {
      posts.value = response.data.data
    } else {
      console.error('Erro na resposta da API:', response.data.message)
    }
  } catch (error) {
    console.error('Erro ao carregar posts:', error)
    if (error.response) {
      console.error('Detalhes do erro:', error.response.data)
    }
  } finally {
    isLoading.value = false
  }
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('pt-BR', {
    day: '2-digit',
    month: 'long',
    year: 'numeric'
  })
}

// Lifecycle
onMounted(async () => {
  if (props.contentType === 'category' && props.selectedCategories?.length) {
    await loadCategories()
  } else if (props.contentType === 'posts') {
    await loadPosts()
  }
})

// Adicionar watch para filterCategory
watch(() => props.filterCategory, async (newValue) => {
  if (props.contentType === 'posts') {
    await loadPosts()
  }
})
</script>

<style scoped>
.content-block {
  @apply w-full;
}

:deep(.prose) {
  @apply max-w-none;
}

:deep(.prose h1) {
  @apply text-3xl font-bold text-gray-900 dark:text-white mt-8 mb-4;
}

:deep(.prose h2) {
  @apply text-2xl font-semibold text-gray-900 dark:text-white mt-6 mb-4;
}

:deep(.prose h3) {
  @apply text-xl font-semibold text-gray-900 dark:text-white mt-4 mb-2;
}

:deep(.prose p) {
  @apply text-base text-gray-700 dark:text-gray-300 leading-relaxed mb-4;
}

:deep(.prose ul) {
  @apply list-disc list-inside text-gray-700 dark:text-gray-300 mb-4;
}

:deep(.prose ol) {
  @apply list-decimal list-inside text-gray-700 dark:text-gray-300 mb-4;
}

:deep(.prose a) {
  @apply text-blue-600 hover:underline;
}

:deep(.prose blockquote) {
  @apply border-l-4 border-gray-200 dark:border-gray-700 pl-4 italic text-gray-600 dark:text-gray-400;
}

:deep(.prose code) {
  @apply bg-gray-100 dark:bg-gray-800 rounded px-1 py-0.5 text-sm font-mono text-gray-800 dark:text-gray-200;
}

:deep(.prose pre) {
  @apply bg-gray-100 dark:bg-gray-800 rounded p-4 overflow-x-auto;
}

:deep(.prose img) {
  @apply rounded-lg shadow-lg max-w-full h-auto;
}

:deep(.prose table) {
  @apply min-w-full divide-y divide-gray-200 dark:divide-gray-700;
}

:deep(.prose th) {
  @apply px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider;
}

:deep(.prose td) {
  @apply px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100;
}
</style> 