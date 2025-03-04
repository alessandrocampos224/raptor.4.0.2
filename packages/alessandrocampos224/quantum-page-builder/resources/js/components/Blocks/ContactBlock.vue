<!-- ContactBlock.vue -->
<template>
  <div :class="[
    'contact-block',
    `style-${style}`,
    `layout-${layout}`
  ]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div v-if="title || subtitle" class="text-center mb-8">
        <h2 v-if="title" class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
          {{ title }}
        </h2>
        <p v-if="subtitle" class="mt-2 text-lg text-gray-600 dark:text-gray-400">
          {{ subtitle }}
        </p>
      </div>

      <div :class="[
        'grid gap-6',
        columns === 1 ? 'grid-cols-1' : '',
        columns === 2 ? 'grid-cols-1 md:grid-cols-2' : '',
        columns === 3 ? 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3' : '',
        columns === 4 ? 'grid-cols-1 md:grid-cols-2 lg:grid-cols-4' : ''
      ]">
        <div v-for="(channel, index) in channels" :key="index" 
          class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-lg hover:shadow-xl transition-shadow duration-300 ease-in-out p-6"
        >
          <div class="flex items-center mb-4">
            <component 
              :is="getIconComponent(channel.icon)"
              class="h-6 w-6 text-blue-600 dark:text-blue-400 mr-3"
            />
            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
              {{ channel.title }}
            </h3>
          </div>
          
          <p class="text-gray-600 dark:text-gray-400 mb-4">
            {{ channel.description }}
          </p>

          <div class="space-y-3">
            <div v-if="channel.address" class="flex items-start">
              <MapPin class="h-5 w-5 text-gray-400 mr-2 mt-0.5" />
              <span class="text-gray-600 dark:text-gray-400">{{ channel.address }}</span>
            </div>

            <div v-if="channel.schedule" class="flex items-start">
              <Clock class="h-5 w-5 text-gray-400 mr-2 mt-0.5" />
              <span class="text-gray-600 dark:text-gray-400">{{ channel.schedule }}</span>
            </div>

            <div v-if="channel.phone" class="flex items-start">
              <Phone class="h-5 w-5 text-gray-400 mr-2 mt-0.5" />
              <a :href="'tel:' + channel.phone" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">
                {{ channel.phone }}
              </a>
            </div>

            <div v-if="channel.email" class="flex items-start">
              <Mail class="h-5 w-5 text-gray-400 mr-2 mt-0.5" />
              <a :href="'mailto:' + channel.email" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">
                {{ channel.email }}
              </a>
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
import {
  MapPin,
  Clock,
  Phone,
  Mail,
  Users,
  Calendar,
  MessageSquare
} from 'lucide-vue-next'

const props = defineProps({
  title: {
    type: String,
    default: 'Entre em contato'
  },
  subtitle: {
    type: String,
    default: 'Estamos disponíveis através dos seguintes canais'
  },
  channels: {
    type: Array,
    default: () => [
      {
        icon: 'phone',
        title: 'Telefone',
        description: 'Estamos disponíveis para atendimento telefônico em horário comercial.',
        phone: '(11) 1234-5678',
        whatsapp: '(11) 98765-4321'
      },
      {
        icon: 'email',
        title: 'Email',
        description: 'Envie-nos um email e responderemos o mais breve possível.',
        email: 'contato@empresa.com'
      },
      {
        icon: 'location',
        title: 'Endereço',
        description: 'Visite nossa sede para um atendimento presencial.',
        address: 'Av. Paulista, 1000 - São Paulo, SP',
        schedule: 'Segunda a Sexta, 9h às 18h'
      }
    ]
  },
  columns: {
    type: Number,
    default: 3
  },
  style: {
    type: [String, Object],
    default: () => ({}),
  },
  layout: {
    type: String,
    default: 'default'
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

const getIconComponent = (iconName) => {
  const icons = {
    'users': Users,
    'calendar': Calendar,
    'phone': Phone,
    'email': Mail,
    'location': MapPin,
    'clock': Clock,
    'chat': MessageSquare
  }
  return icons[iconName] || Users
}
</script>

<style scoped>
.contact-block {
  @apply py-12;
}

.style-default {
  @apply bg-gray-50 dark:bg-gray-800;
}

.style-white {
  @apply bg-white dark:bg-gray-800;
}

.style-highlighted {
  @apply bg-blue-50 dark:bg-blue-900;
}

.layout-default {
  @apply w-full;
}

.layout-narrow {
  @apply max-w-4xl mx-auto;
}

.layout-wide {
  @apply max-w-7xl mx-auto;
}
</style> 