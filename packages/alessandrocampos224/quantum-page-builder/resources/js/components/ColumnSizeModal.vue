<template>
  <div v-if="isOpen" class="fixed inset-0 z-[100] overflow-hidden">
    <!-- Overlay para fechar o modal ao clicar fora -->
    <div class="absolute inset-0 bg-black/50" @click="close"></div>
    
    <!-- Modal -->
    <div class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[450px] max-w-full bg-white dark:bg-gray-800 shadow-xl rounded-lg p-6 z-[101]">
      <!-- Cabeçalho do modal -->
      <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Definir Tamanho do Componente</h3>
        <button 
          type="button" 
          @click="close" 
          class="rounded-sm opacity-70 transition-opacity hover:opacity-100 focus:outline-none"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
          <span class="sr-only">Fechar</span>
        </button>
      </div>
      
      <!-- Conteúdo do modal -->
      <div class="space-y-6">
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Quantas colunas este componente deve ocupar?
          </label>
          <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
            A seção tem um total de 12 colunas. Defina quantas colunas este componente deve ocupar.
          </p>
          
          <!-- Visualização do grid -->
          <div class="grid grid-cols-12 gap-1 mb-4 h-12">
            <div 
              v-for="n in 12" 
              :key="n" 
              class="h-full transition-colors duration-200"
              :class="[
                n <= columnSize 
                  ? 'bg-tenant-primary/80 dark:bg-tenant-primary' 
                  : 'bg-gray-200 dark:bg-gray-700'
              ]"
            ></div>
          </div>
          
          <!-- Seletor de colunas -->
          <div class="flex items-center space-x-4">
            <input 
              type="range" 
              min="1" 
              max="12" 
              v-model.number="columnSize" 
              class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700"
            />
            <span class="text-sm font-medium text-gray-900 dark:text-gray-100 min-w-[40px] text-center">
              {{ columnSize }} / 12
            </span>
          </div>
        </div>
        
        <!-- Botões de ação -->
        <div class="flex justify-end space-x-3">
          <button 
            type="button" 
            @click="close" 
            class="px-4 py-2 border border-red-300 dark:border-red-800 rounded-md text-sm font-medium text-white bg-red-600 hover:bg-red-700 dark:hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
          >
            Cancelar
          </button>
          <button 
            type="button" 
            @click="confirm" 
            class="px-4 py-2 border border-green-300 dark:border-green-800 rounded-md text-sm font-medium text-white bg-green-600 hover:bg-green-700 dark:hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
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

// Props
const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  initialSize: {
    type: Number,
    default: 6
  }
})

// Emits
const emit = defineEmits(['close', 'confirm'])

// Estado
const columnSize = ref(props.initialSize)

// Observar mudanças no initialSize
watch(() => props.initialSize, (newSize) => {
  columnSize.value = newSize
})

// Observar mudanças no isOpen
watch(() => props.isOpen, (isOpen) => {
  if (isOpen) {
    // Resetar para o valor inicial quando o modal é aberto
    columnSize.value = props.initialSize
    
    // Adicionar a classe overflow-hidden ao body quando o modal é aberto
    document.body.classList.add('overflow-hidden')
  } else {
    // Remover a classe overflow-hidden do body quando o modal é fechado
    document.body.classList.remove('overflow-hidden')
  }
})

// Métodos
const close = () => {
  emit('close')
}

const confirm = () => {
  emit('confirm', columnSize.value)
  close()
}
</script>

<style scoped>
/* Estilizar o input range */
input[type="range"] {
  -webkit-appearance: none;
  appearance: none;
  height: 8px;
  border-radius: 5px;
  background: #e5e7eb;
}

.dark input[type="range"] {
  background: #374151;
}

input[type="range"]::-webkit-slider-thumb {
  -webkit-appearance: none;
  appearance: none;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background: var(--tenant-primary, #3b82f6);
  cursor: pointer;
  border: 2px solid white;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
}

input[type="range"]::-moz-range-thumb {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background: var(--tenant-primary, #3b82f6);
  cursor: pointer;
  border: 2px solid white;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
}

.dark input[type="range"]::-webkit-slider-thumb {
  border-color: #1f2937;
}

.dark input[type="range"]::-moz-range-thumb {
  border-color: #1f2937;
}
</style> 