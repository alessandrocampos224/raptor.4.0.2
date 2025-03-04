<template>
  <div 
    :class="[
      'contact-channels-block relative',
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
      <h2 v-if="title" class="text-2xl font-bold mb-8 text-center">{{ title }}</h2>
      
      <div :class="[
        'grid gap-8',
        columns === 1 ? 'grid-cols-1' : '',
        columns === 2 ? 'grid-cols-1 md:grid-cols-2' : '',
        columns === 3 ? 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3' : '',
        columns === 4 ? 'grid-cols-1 md:grid-cols-2 lg:grid-cols-4' : ''
      ]">
        <div v-for="(channel, index) in channels" :key="index" 
          :class="cardClasses">
          <div :class="iconClasses">
            <component 
              :is="getIconComponent(channel.icon)"
              class="w-8 h-8"
              aria-hidden="true"
            />
          </div>
          <h3 :class="titleClasses">{{ channel.title }}</h3>
          <div class="space-y-2">
            <div :class="descriptionClasses">{{ channel.description }}</div>
            <div v-if="channel.details && channel.details.length > 0" class="space-y-1">
              <div v-for="(detail, detailIndex) in channel.details" :key="detailIndex" 
                class="flex items-center space-x-2 text-gray-600">
                <component 
                  :is="getDetailIcon(detail.type)"
                  class="w-5 h-5 text-tenant-primary"
                  aria-hidden="true"
                />
                <span v-if="detail.type === 'email'">
                  <a :href="'mailto:' + detail.value" class="text-tenant-primary hover:underline">
                    {{ detail.value }}
                  </a>
                </span>
                <span v-else-if="detail.type === 'phone'">
                  <a :href="'tel:' + detail.value" class="text-tenant-primary hover:underline">
                    {{ formatPhone(detail.value) }}
                  </a>
                </span>
                <span v-else-if="detail.type === 'whatsapp'">
                  <a :href="'https://wa.me/' + formatWhatsappNumber(detail.value)" 
                    target="_blank" 
                    class="text-tenant-primary hover:underline">
                    {{ formatPhone(detail.value) }}
                  </a>
                </span>
                <span v-else>{{ detail.value }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Área para componentes aninhados -->
      <div v-if="allowNesting" class="nested-components-container mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
        <slot></slot>
      </div>
    </div>
  </div>
</template>

<script setup>
import { watch, computed } from 'vue'
import {
  Users,
  Mail,
  Phone,
  Calendar,
  Clock,
  MapPin,
  Globe,
  MessageSquare,
  Instagram,
  Facebook,
  Linkedin,
  Twitter,
  Youtube
} from 'lucide-vue-next'

const props = defineProps({
  title: {
    type: String,
    default: 'Entre em contato'
  },
  channels: {
    type: Array,
    default: () => []
  },
  // Layout
  columns: {
    type: Number,
    default: 3 // 1, 2, 3, 4
  },
  // Estilo
  style: {
    type: [String, Object],
    default: 'cards' // 'default', 'cards', 'minimal'
  },
  // Opções de Container
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
    default: 'md' // 'none', 'sm', 'md', 'lg', 'full'
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
  // Novas propriedades para suporte a colunas e aninhamento
  columnSpan: {
    type: Number,
    default: 12
  },
  allowNesting: {
    type: Boolean,
    default: false
  },
  contentType: {
    type: String,
    default: 'static' // 'static', 'posts', 'categories'
  },
  selectedCategories: {
    type: Array,
    default: () => []
  },
  filterCategory: {
    type: String,
    default: ''
  },
  postsLimit: {
    type: Number,
    default: 3
  }
})

// Debug do valor do estilo
watch(() => props.style, (newValue) => {
  const currentStyle = typeof newValue === 'object' ? newValue.value : newValue
  console.log('ContactChannelsBlock - Estilo atualizado:', currentStyle)
}, { immediate: true })

// Função para obter o componente de ícone correto
const getIconComponent = (iconName) => {
  const icons = {
    'Users': Users,
    'Mail': Mail,
    'Phone': Phone,
    'Calendar': Calendar,
    'Clock': Clock,
    'MapPin': MapPin,
    'Globe': Globe,
    'MessageSquare': MessageSquare,
    'Instagram': Instagram,
    'Facebook': Facebook,
    'Linkedin': Linkedin,
    'Twitter': Twitter,
    'Youtube': Youtube
  }
  return icons[iconName] || Users
}

// Função para obter o ícone do detalhe
const getDetailIcon = (type) => {
  const icons = {
    'email': Mail,
    'phone': Phone,
    'whatsapp': MessageSquare,
    'schedule': Clock,
    'address': MapPin,
    'time': Clock
  }
  return icons[type] || Globe
}

// Computed property para as classes do card
const cardClasses = computed(() => {
  const currentStyle = typeof props.style === 'object' ? props.style.value : props.style
  
  return [
    'flex flex-col items-center text-center transition-all duration-300',
    {
      'p-6 bg-white dark:bg-[#0f172a] rounded-lg shadow-sm hover:shadow-md': currentStyle === 'default',
      'p-8 bg-white dark:bg-[#0f172a] rounded-xl shadow-md hover:shadow-lg': currentStyle === 'cards',
      'p-4 border border-gray-100 dark:border-gray-700 hover:border-tenant-primary dark:hover:border-tenant-primary': currentStyle === 'minimal'
    }
  ]
})

// Computed property para as classes do ícone
const iconClasses = computed(() => {
  const currentStyle = typeof props.style === 'object' ? props.style.value : props.style
  
  return [
    'mb-4 flex items-center justify-center rounded-full transition-all duration-300',
    {
      'w-16 h-16 bg-tenant-primary text-white dark:text-gray-100': currentStyle === 'default',
      'w-20 h-20 bg-tenant-primary text-white dark:text-gray-100': currentStyle === 'cards',
      'w-12 h-12 bg-tenant-primary/10 dark:bg-tenant-primary/20 text-tenant-primary dark:text-tenant-primary': currentStyle === 'minimal'
    }
  ]
})

// Computed property para as classes do título
const titleClasses = computed(() => {
  const currentStyle = typeof props.style === 'object' ? props.style.value : props.style
  
  return {
    'font-semibold mb-2 text-gray-900 dark:text-white': true,
    'text-xl': currentStyle === 'default',
    'text-2xl': currentStyle === 'cards',
    'text-lg': currentStyle === 'minimal'
  }
})

// Computed property para as classes da descrição
const descriptionClasses = computed(() => {
  const currentStyle = typeof props.style === 'object' ? props.style.value : props.style
  
  return {
    'text-gray-600 dark:text-gray-300': currentStyle === 'default',
    'text-gray-700 dark:text-gray-200': currentStyle === 'cards',
    'text-gray-500 dark:text-gray-400 text-sm': currentStyle === 'minimal'
  }
})

// Lista de ícones disponíveis para referência
const availableIcons = {
  // Comunicação
  'fas fa-phone': 'Telefone',
  'fas fa-envelope': 'Email',
  'fab fa-whatsapp': 'WhatsApp',
  'fas fa-comments': 'Chat',
  'fas fa-video': 'Vídeo',
  'fas fa-globe': 'Online',
  
  // Localização
  'fas fa-map-marker-alt': 'Localização',
  'fas fa-building': 'Prédio',
  'fas fa-home': 'Casa',
  
  // Tempo
  'fas fa-clock': 'Relógio',
  'fas fa-calendar': 'Calendário',
  'fas fa-calendar-alt': 'Calendário Alt',
  
  // Pessoas
  'fas fa-user': 'Usuário',
  'fas fa-users': 'Usuários',
  'fas fa-user-tie': 'Profissional',
  
  // Outros
  'fas fa-info-circle': 'Informação',
  'fas fa-question-circle': 'Dúvida',
  'fas fa-exclamation-circle': 'Atenção',
  'fas fa-check-circle': 'Sucesso',
  'fas fa-cog': 'Configuração',
  'fas fa-tools': 'Ferramentas'
}

const formatPhone = (phone) => {
  if (!phone) return ''
  
  // Remove tudo que não for número
  const numbers = phone.replace(/\D/g, '')
  
  // Verifica se é celular (9 dígitos) ou fixo (8 dígitos)
  if (numbers.length === 11) {
    return numbers.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3')
  } else if (numbers.length === 10) {
    return numbers.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3')
  }
  
  return phone
}

const formatWhatsappNumber = (phone) => {
  if (!phone) return ''
  return phone.replace(/\D/g, '')
}
</script>

<style scoped>
.contact-channels-block {
  transition: all 0.3s ease-in-out;
}

.contact-channels-block [class*='shadow-'] {
  transition: box-shadow 0.3s ease-in-out;
}

.contact-channels-block [class*='border-'] {
  transition: border-color 0.3s ease-in-out;
}

.contact-channels-block [class*='bg-'] {
  transition: background-color 0.3s ease-in-out;
}

.contact-channels-block [class*='text-'] {
  transition: color 0.3s ease-in-out;
}

.contact-channels-block [class*='rounded-'] {
  transition: border-radius 0.3s ease-in-out;
}

.contact-channels-block [class*='p-'] {
  transition: padding 0.3s ease-in-out;
}

.contact-channels-block [class*='w-'],
.contact-channels-block [class*='h-'] {
  transition: width 0.3s ease-in-out, height 0.3s ease-in-out;
}
</style> 