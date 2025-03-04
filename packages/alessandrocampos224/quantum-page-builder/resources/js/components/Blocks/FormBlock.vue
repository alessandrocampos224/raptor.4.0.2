<!-- FormBlock.vue -->
<template>
  <div 
    :class="[
      'form-block relative',
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
      <div class="max-w-2xl mx-auto">
        <h2 v-if="title" class="text-2xl font-bold mb-4 text-center">{{ title }}</h2>
        <p v-if="description" class="text-gray-600 mb-8 text-center">{{ description }}</p>
        
        <form @submit.prevent="handleSubmit" class="space-y-6">
          <div v-for="(field, index) in fields" :key="index" class="space-y-2">
            <label :for="field.name" class="block text-sm font-medium text-gray-700">
              {{ field.label }}
              <span v-if="field.required" class="text-red-500">*</span>
            </label>
            
            <!-- Input text/number/email -->
            <input
              v-if="['text', 'number', 'email', 'tel'].includes(field.type)"
              :type="field.type"
              :id="field.name"
              :name="field.name"
              v-model="formData[field.name]"
              :required="field.required"
              :placeholder="field.placeholder"
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >
            
            <!-- Textarea -->
            <textarea
              v-if="field.type === 'textarea'"
              :id="field.name"
              :name="field.name"
              v-model="formData[field.name]"
              :required="field.required"
              :placeholder="field.placeholder"
              :rows="field.rows || 4"
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            ></textarea>
            
            <!-- Select -->
            <select
              v-if="field.type === 'select'"
              :id="field.name"
              :name="field.name"
              v-model="formData[field.name]"
              :required="field.required"
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >
              <option value="">Selecione uma opção</option>
              <option v-for="option in field.options" :key="option.value" :value="option.value">
                {{ option.label }}
              </option>
            </select>
            
            <!-- Radio -->
            <div v-if="field.type === 'radio'" class="space-y-2">
              <div v-for="option in field.options" :key="option.value" class="flex items-center">
                <input
                  type="radio"
                  :id="option.value"
                  :name="field.name"
                  :value="option.value"
                  v-model="formData[field.name]"
                  :required="field.required"
                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                >
                <label :for="option.value" class="ml-3 block text-sm text-gray-700">
                  {{ option.label }}
                </label>
              </div>
            </div>
            
            <!-- Checkbox -->
            <div v-if="field.type === 'checkbox'" class="flex items-center">
              <input
                type="checkbox"
                :id="field.name"
                :name="field.name"
                v-model="formData[field.name]"
                :required="field.required"
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
              >
              <label :for="field.name" class="ml-3 block text-sm text-gray-700">
                {{ field.label }}
              </label>
            </div>
          </div>
          
          <div class="flex justify-center">
            <button 
              type="submit" 
              class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
            >
              {{ submitButtonText || 'Enviar' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Área para componentes aninhados -->
    <div v-if="allowNesting" class="nested-components-container mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
      <slot></slot>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
  title: {
    type: String,
    default: 'Entre em contato'
  },
  description: {
    type: String,
    default: 'Preencha o formulário abaixo para entrar em contato conosco.'
  },
  fields: {
    type: Array,
    default: () => [
      { 
        type: 'text', 
        name: 'name', 
        label: 'Nome', 
        required: true,
        placeholder: 'Seu nome completo'
      },
      { 
        type: 'email', 
        name: 'email', 
        label: 'Email', 
        required: true,
        placeholder: 'seu@email.com'
      },
      { 
        type: 'textarea', 
        name: 'message', 
        label: 'Mensagem', 
        required: true,
        placeholder: 'Digite sua mensagem aqui...'
      }
    ]
  },
  submitButtonText: {
    type: String,
    default: 'Enviar'
  },
  successMessage: {
    type: String,
    default: 'Mensagem enviada com sucesso!'
  },
  errorMessage: {
    type: String,
    default: 'Ocorreu um erro ao enviar a mensagem. Tente novamente.'
  },
  containerWidth: {
    type: String,
    default: 'default' // 'default', 'narrow', 'full'
  },
  backgroundColor: {
    type: String,
    default: 'white' // 'white', 'gray-50', 'gray-100', etc.
  },
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
    default: 'gray-200' // 'gray-200', 'gray-300', etc.
  },
  borderRadius: {
    type: String,
    default: 'md' // 'none', 'sm', 'md', 'lg', 'xl', 'full'
  },
  backgroundImage: {
    type: String,
    default: ''
  },
  overlayOpacity: {
    type: String,
    default: ''
  },
  textAlignment: {
    type: String,
    default: 'left' // 'left', 'center', 'right'
  },
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

const formData = ref({})

const emit = defineEmits(['submit'])

const handleSubmit = () => {
  emit('submit', formData.value)
}
</script>

<style scoped>
.form-block {
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

.form-success {
  @apply bg-blue-50 text-blue-700 p-4 rounded-md mb-4;
}

.form-error {
  @apply bg-red-50 text-red-700 p-4 rounded-md mb-4;
}
</style> 