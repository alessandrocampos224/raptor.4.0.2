<!-- CardBlock.vue -->
<template>
  <div 
    :class="[
      'card-block relative',
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
      <div v-if="displayTitle || displaySubtitle" class="mb-8">
        <h2 :class="[
          'text-' + titleSize,
          'font-' + titleWeight,
          'text-' + titleColor,
          titleFont,
          'leading-tight'
        ]">
          {{ displayTitle }}
        </h2>
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
      </div>

      <div v-if="loading" class="flex items-center justify-center py-8">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
      </div>

      <div v-else-if="displayCards.length === 0" class="text-center py-8">
        <p class="text-gray-500">Nenhum card disponível para exibição.</p>
      </div>

      <div v-else :class="[
        'grid',
        {
          'gap-4': spacing === 'compact',
          'gap-8': spacing === 'normal',
          'gap-12': spacing === 'relaxed'
        },
        {
          'grid-cols-1 md:grid-cols-2 lg:grid-cols-3': columns === 3,
          'grid-cols-1 md:grid-cols-2': columns === 2,
          'grid-cols-1 md:grid-cols-2 lg:grid-cols-4': columns === 4,
          'grid-cols-1': columns === 1
        }
      ]">
        <div v-for="(card, index) in displayCards" :key="index" 
          class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-lg hover:shadow-xl transition-shadow duration-300 ease-in-out"
        >
          <div class="p-6">
            <div class="flex items-center mb-4">
              <component 
                v-if="card?.icon" 
                :is="getIconComponent(card.icon)"
                class="h-6 w-6 text-primary-600 dark:text-primary-400 mr-3"
              />
              <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                {{ card?.title || 'Sem título' }}
              </h3>
            </div>
            
            <p class="text-gray-600 dark:text-gray-400 mb-4">
              {{ card?.description || 'Sem descrição' }}
            </p>

            <div v-if="card?.link?.text" class="mt-4">
              <a 
                :href="card.link.url"
                class="text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-medium inline-flex items-center"
              >
                {{ card.link.text }}
                <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Área para componentes aninhados -->
    <div v-if="allowNesting" class="nested-components-container">
      <slot></slot>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, onMounted, watch } from 'vue'
import axios from 'axios'
import {
  FileText,
  Users,
  Calendar,
  Phone,
  Mail,
  MapPin,
  Clock,
  MessageSquare
} from 'lucide-vue-next'

const props = defineProps({
  // Fonte de dados
  dataSource: {
    type: String,
    default: 'static' // 'static', 'dynamic'
  },
  contentLink: {
    type: Object,
    default: null
  },
  // Container
  containerWidth: {
    type: String,
    default: 'default' // 'default', 'full', 'narrow'
  },
  textAlignment: {
    type: String,
    default: 'left' // 'left', 'center', 'right'
  },
  // Background
  backgroundColor: {
    type: String,
    default: 'white' // 'white', 'gray-50', 'gray-100', etc
  },
  backgroundImage: {
    type: String,
    default: ''
  },
  overlayOpacity: {
    type: Number,
    default: 0 // 0 a 1
  },
  // Bordas e Sombras
  shadow: {
    type: String,
    default: 'none' // 'none', 'sm', 'md', 'lg', 'xl'
  },
  borderStyle: {
    type: String,
    default: 'none' // 'none', 'solid', 'dashed', 'dotted'
  },
  borderWidth: {
    type: String,
    default: '0' // '0', '1', '2', '4', '8'
  },
  borderColor: {
    type: String,
    default: 'gray-200'
  },
  borderRadius: {
    type: String,
    default: 'none' // 'none', 'sm', 'md', 'lg', 'full'
  },
  // Espaçamento
  paddingY: {
    type: String,
    default: '12' // '4', '8', '12', '16', '20'
  },
  paddingX: {
    type: String,
    default: '4' // '4', '8', '12', '16', '20'
  },
  // Título
  title: {
    type: String,
    default: ''
  },
  titleSize: {
    type: String,
    default: '4xl' // 'xl', '2xl', '3xl', '4xl', '5xl'
  },
  titleWeight: {
    type: String,
    default: 'bold' // 'normal', 'medium', 'semibold', 'bold'
  },
  titleColor: {
    type: String,
    default: 'gray-900'
  },
  titleFont: {
    type: String,
    default: 'font-sans' // 'font-sans', 'font-serif', 'font-mono'
  },
  // Subtítulo
  subtitle: {
    type: String,
    default: ''
  },
  subtitleSize: {
    type: String,
    default: 'xl'
  },
  subtitleWeight: {
    type: String,
    default: 'normal'
  },
  subtitleColor: {
    type: String,
    default: 'gray-600'
  },
  subtitleFont: {
    type: String,
    default: 'font-sans'
  },
  // Cards
  cards: {
    type: Array,
    default: () => []
  },
  columns: {
    type: Number,
    default: 3
  },
  // Espaçamento entre cards
  spacing: {
    type: String,
    default: 'normal' // 'compact', 'normal', 'relaxed'
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
    type: [Number, String],
    default: 6
  }
})

// Estado para armazenar posts carregados dinamicamente
const dynamicPosts = ref([])
const loading = ref(false)

// Título dinâmico ou estático
const displayTitle = computed(() => {
  if (props.dataSource === 'dynamic' && props.contentLink?.data) {
    return props.contentLink.data.name
  }
  return props.title
})

// Subtítulo dinâmico ou estático
const displaySubtitle = computed(() => {
  if (props.dataSource === 'dynamic' && props.contentLink?.data) {
    return props.contentLink.data.description || ''
  }
  return props.subtitle
})

// Cards dinâmicos ou estáticos
const displayCards = computed(() => {
  console.log('CardBlock - Calculando displayCards')
  console.log('CardBlock - dataSource:', props.dataSource)
  console.log('CardBlock - contentLink:', props.contentLink)
  console.log('CardBlock - cards estáticos:', props.cards)
  
  if (props.dataSource === 'dynamic' && props.contentLink?.type === 'category') {
    console.log('CardBlock - Usando posts dinâmicos da categoria:', dynamicPosts.value)
    return dynamicPosts.value.map(post => ({
      title: post.name,
      description: post.description || 'Sem descrição',
      icon: 'document',
      link: {
        text: 'Ver mais',
        url: `/posts/${post.id}`
      }
    }))
  } else if (props.dataSource === 'dynamic' && props.contentLink?.type === 'post') {
    // Se for um post único, criamos um card para ele
    console.log('CardBlock - Usando post único:', props.contentLink.data)
    const post = props.contentLink.data
    return [{
      title: post.name,
      description: post.description || 'Sem descrição',
      icon: 'document',
      link: {
        text: 'Ver mais',
        url: `/posts/${post.id}`
      }
    }]
  }
  
  // Caso contrário, retorna os cards estáticos
  if (!Array.isArray(props.cards) || props.cards.length === 0) {
    console.log('CardBlock - Nenhum card estático disponível')
    return []
  }
  
  console.log('CardBlock - Usando cards estáticos:', props.cards)
  return props.cards
})

// Carregar posts de uma categoria
const loadPostsFromCategory = async (categoryId) => {
  if (!categoryId) {
    console.log('CardBlock - Nenhum ID de categoria fornecido para carregar posts')
    return
  }
  
  console.log('CardBlock - Carregando posts da categoria:', categoryId)
  loading.value = true
  try {
    const response = await axios.get(`/api/page-builder/categories/${categoryId}/posts`)
    console.log('CardBlock - Resposta da API de posts:', response.data)
    
    if (response.data.success) {
      dynamicPosts.value = response.data.data
      console.log('CardBlock - Posts carregados com sucesso:', dynamicPosts.value)
    } else {
      console.error('CardBlock - Erro ao carregar posts da categoria:', response.data.message)
    }
  } catch (error) {
    console.error('CardBlock - Erro ao carregar posts da categoria:', error)
  } finally {
    loading.value = false
  }
}

// Observar mudanças no contentLink
watch(() => props.contentLink, (newValue) => {
  console.log('CardBlock - contentLink mudou:', newValue)
  if (props.dataSource === 'dynamic' && newValue?.type === 'category') {
    loadPostsFromCategory(newValue.id)
  }
}, { deep: true })

// Carregar dados iniciais se necessário
onMounted(() => {
  console.log('CardBlock - Componente montado')
  console.log('CardBlock - dataSource:', props.dataSource)
  console.log('CardBlock - contentLink:', props.contentLink)
  
  if (props.dataSource === 'dynamic' && props.contentLink?.type === 'category') {
    loadPostsFromCategory(props.contentLink.id)
  }
})

const getIconComponent = (iconName) => {
  if (!iconName) return FileText
  
  const icons = {
    'document': FileText,
    'users': Users,
    'calendar': Calendar,
    'phone': Phone,
    'email': Mail,
    'location': MapPin,
    'clock': Clock,
    'chat': MessageSquare
  }
  
  return icons[iconName] || FileText
}
</script>

<style scoped>
.card-block {
  @apply w-full;
}
</style> 