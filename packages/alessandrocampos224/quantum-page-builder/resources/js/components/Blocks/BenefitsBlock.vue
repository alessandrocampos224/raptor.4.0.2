<template>
  <div 
    :class="[
      'benefits-block relative',
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
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div v-for="(benefit, index) in benefits" :key="index" class="flex flex-col items-center text-center">
          <div class="w-16 h-16 mb-4 flex items-center justify-center rounded-full bg-tenant-primary text-white">
            <i :class="benefit.icon" class="text-2xl"></i>
          </div>
          <h3 class="text-xl font-semibold mb-2">{{ benefit.title }}</h3>
          <p class="text-gray-600">{{ benefit.description }}</p>
          <a v-if="benefit.link" :href="benefit.link" class="mt-4 text-tenant-primary hover:underline">
            Saiba mais
          </a>
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
const props = defineProps({
  benefits: {
    type: Array,
    default: () => [
      {
        icon: 'fas fa-rocket',
        title: 'Rápido e Eficiente',
        description: 'Nossos serviços são otimizados para oferecer a melhor performance possível.',
        link: ''
      },
      {
        icon: 'fas fa-shield-alt',
        title: 'Seguro e Confiável',
        description: 'Segurança em primeiro lugar, seus dados estão protegidos conosco.',
        link: ''
      },
      {
        icon: 'fas fa-headset',
        title: 'Suporte 24/7',
        description: 'Nossa equipe está sempre disponível para ajudar quando você precisar.',
        link: ''
      }
    ]
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
</script> 