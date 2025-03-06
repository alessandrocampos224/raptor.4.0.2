<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center">
    <!-- Overlay de fundo -->
    <div class="absolute inset-0 bg-black bg-opacity-50" @click="close"></div>
    
    <!-- Modal -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md mx-auto z-10 overflow-hidden">
      <div class="p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
          Configurar Componente
        </h3>
        
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Quantas colunas este componente deve ocupar?
            </label>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
              A seção tem um total de 12 colunas disponíveis.
            </p>
            
            <div class="grid grid-cols-6 gap-2">
              <button
                v-for="n in 12"
                :key="n"
                @click="selectedColumns = n"
                class="py-2 px-3 text-center border rounded-md transition-colors"
                :class="[
                  selectedColumns === n 
                    ? 'bg-blue-100 dark:bg-blue-900 border-blue-500 text-blue-700 dark:text-blue-300' 
                    : 'bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600'
                ]"
              >
                {{ n }}
              </button>
            </div>
          </div>
          
          <!-- Visualização -->
          <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Visualização:
            </label>
            <div class="bg-gray-100 dark:bg-gray-700 p-3 rounded-md">
              <div class="grid grid-cols-12 gap-1 h-8">
                <div 
                  v-for="i in 12" 
                  :key="i"
                  class="h-full"
                  :class="[
                    i <= selectedColumns 
                      ? 'bg-blue-500 dark:bg-blue-600' 
                      : 'bg-gray-300 dark:bg-gray-600'
                  ]"
                ></div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="mt-6 flex justify-end space-x-3">
          <button 
            @click="close" 
            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
          >
            Cancelar
          </button>
          <button 
            @click="confirm" 
            class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700"
          >
            Confirmar
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  initialColumns: {
    type: Number,
    default: 6
  }
})

const emit = defineEmits(['close', 'confirm'])

const selectedColumns = ref(props.initialColumns)

// Atualizar o valor selecionado quando as props mudarem
watch(() => props.initialColumns, (newValue) => {
  selectedColumns.value = newValue
})

// Atualizar o estado quando o modal abrir/fechar
watch(() => props.isOpen, (newValue) => {
  if (newValue) {
    // Reset para o valor inicial quando o modal abrir
    selectedColumns.value = props.initialColumns
  }
})

const close = () => {
  emit('close')
}

const confirm = () => {
  emit('confirm', selectedColumns.value)
}
</script> 