<template>
  <div 
    :class="[
      'simulator-block relative',
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
            
            <!-- Input text/number -->
            <input
              v-if="['text', 'number', 'email'].includes(field.type)"
              :type="field.type"
              :id="field.name"
              :name="field.name"
              v-model="formData[field.name]"
              :required="field.required"
              :placeholder="field.placeholder"
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-tenant-primary focus:ring-tenant-primary"
            >
            
            <!-- Select -->
            <select
              v-if="field.type === 'select'"
              :id="field.name"
              :name="field.name"
              v-model="formData[field.name]"
              :required="field.required"
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-tenant-primary focus:ring-tenant-primary"
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
                  class="h-4 w-4 text-tenant-primary focus:ring-tenant-primary border-gray-300"
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
                class="h-4 w-4 text-tenant-primary focus:ring-tenant-primary border-gray-300 rounded"
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
              {{ submitButtonText || 'Simular' }}
            </button>
          </div>
        </form>
      </div>
      
      <!-- Área para componentes aninhados -->
      <div v-if="allowNesting" class="nested-components-container mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
        <slot></slot>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

defineProps({
  title: {
    type: String,
    default: 'Simulador'
  },
  description: {
    type: String,
    default: 'Preencha os campos abaixo para realizar uma simulação.'
  },
  fields: {
    type: Array,
    default: () => [
      { 
        type: 'number', 
        name: 'value', 
        label: 'Valor', 
        required: true,
        placeholder: 'Digite o valor'
      },
      { 
        type: 'select', 
        name: 'period', 
        label: 'Período', 
        required: true,
        options: [
          { value: '12', label: '12 meses' },
          { value: '24', label: '24 meses' },
          { value: '36', label: '36 meses' },
          { value: '48', label: '48 meses' },
          { value: '60', label: '60 meses' }
        ]
      }
    ]
  },
  submitButtonText: {
    type: String,
    default: 'Simular'
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

const formData = ref({})

const emit = defineEmits(['submit'])

const handleSubmit = () => {
  emit('submit', formData.value)
}
</script> 