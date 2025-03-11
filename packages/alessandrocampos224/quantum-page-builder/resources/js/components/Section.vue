<template>
  <div class="section w-full">
    <!-- Container da seção com grid de 12 colunas -->
    <div 
      class="section-container grid grid-cols-12 gap-4 relative"
      :class="[
        sectionClasses,
        {
          'bg-white dark:bg-gray-800': backgroundColor === 'white',
          'bg-gray-50 dark:bg-gray-900': backgroundColor === 'gray-50',
          'bg-gray-100 dark:bg-gray-800': backgroundColor === 'gray-100',
          'bg-tenant-primary text-white': backgroundColor === 'tenant-primary',
          'bg-tenant-secondary text-white': backgroundColor === 'tenant-secondary',
          'shadow-sm': shadow === 'sm',
          'shadow-md': shadow === 'md',
          'shadow-lg': shadow === 'lg',
          'shadow-xl': shadow === 'xl',
          'border-solid': borderStyle === 'solid',
          'border-dashed': borderStyle === 'dashed',
          'border-dotted': borderStyle === 'dotted',
          'rounded-sm': borderRadius === 'sm',
          'rounded-md': borderRadius === 'md',
          'rounded-lg': borderRadius === 'lg',
          'rounded-full': borderRadius === 'full',
          'py-4': paddingY === '4',
          'py-8': paddingY === '8',
          'py-12': paddingY === '12',
          'py-16': paddingY === '16',
          'py-20': paddingY === '20',
          'px-4': paddingX === '4',
          'px-8': paddingX === '8',
          'px-12': paddingX === '12',
          'px-16': paddingX === '16',
          'px-20': paddingX === '20',
          'gap-4': spacing === 'compact',
          'gap-6': spacing === 'normal',
          'gap-8': spacing === 'relaxed',
        }
      ]"
      :style="backgroundImage ? `background-image: url(${backgroundImage}); background-size: cover; background-position: center;` : ''"
    >
      <!-- Overlay para imagem de fundo, se necessário -->
      <div 
        v-if="backgroundImage" 
        class="absolute inset-0 z-0" 
        :style="`background-color: rgba(0,0,0,${overlayOpacity});`"
      ></div>
      
      <!-- Conteúdo da seção -->
      <div class="relative z-10 col-span-12">
        <!-- Título da seção (opcional) -->
        <div v-if="title" class="mb-6">
          <h2 
            class="font-medium"
            :class="[
              titleClasses,
              {
                'text-xl': titleSize === 'xl',
                'text-2xl': titleSize === '2xl',
                'text-3xl': titleSize === '3xl',
                'text-4xl': titleSize === '4xl',
                'text-5xl': titleSize === '5xl',
                'font-normal': titleWeight === 'normal',
                'font-medium': titleWeight === 'medium',
                'font-semibold': titleWeight === 'semibold',
                'font-bold': titleWeight === 'bold',
                'text-gray-900 dark:text-gray-100': titleColor === 'gray-900',
                'text-gray-700 dark:text-gray-300': titleColor === 'gray-700',
                'text-tenant-primary': titleColor === 'tenant-primary',
                'text-center': textAlignment === 'center',
                'text-right': textAlignment === 'right',
                'text-left': textAlignment === 'left',
              }
            ]"
          >
            {{ title }}
          </h2>
          <p 
            v-if="description" 
            class="mt-2"
            :class="[
              {
                'text-base': descriptionSize === 'base',
                'text-lg': descriptionSize === 'lg',
                'text-xl': descriptionSize === 'xl',
                'text-gray-600 dark:text-gray-400': descriptionColor === 'gray-600',
                'text-gray-500 dark:text-gray-500': descriptionColor === 'gray-500',
                'text-tenant-primary': descriptionColor === 'tenant-primary',
                'text-center': textAlignment === 'center',
                'text-right': textAlignment === 'right',
                'text-left': textAlignment === 'left',
              }
            ]"
          >
            {{ description }}
          </p>
        </div>
        
        <!-- Slot para os componentes filhos -->
        <slot></slot>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

// Props
const props = defineProps({
  // Identificação
  id: {
    type: String,
    required: true
  },
  // Título e descrição
  title: {
    type: String,
    default: ''
  },
  description: {
    type: String,
    default: ''
  },
  // Estilo do container
  containerWidth: {
    type: String,
    default: 'default'
  },
  textAlignment: {
    type: String,
    default: 'left'
  },
  // Background
  backgroundColor: {
    type: String,
    default: 'white'
  },
  backgroundImage: {
    type: String,
    default: ''
  },
  overlayOpacity: {
    type: Number,
    default: 0
  },
  // Bordas e Sombras
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
  // Espaçamento
  paddingY: {
    type: String,
    default: '12'
  },
  paddingX: {
    type: String,
    default: '4'
  },
  spacing: {
    type: String,
    default: 'normal'
  },
  // Título
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
  // Descrição
  descriptionSize: {
    type: String,
    default: 'lg'
  },
  descriptionColor: {
    type: String,
    default: 'gray-600'
  },
  // Classes adicionais
  sectionClasses: {
    type: String,
    default: ''
  },
  titleClasses: {
    type: String,
    default: ''
  }
})
</script>

<style scoped>
.section {
  position: relative;
}

.section-container {
  position: relative;
  width: 100%;
}

/* Adicionar borda se o estilo de borda não for 'none' */
.section-container[class*="border-"] {
  @apply border;
}
</style> 