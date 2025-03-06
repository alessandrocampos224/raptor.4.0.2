<template>
  <div class="section w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 mb-6">
    <!-- Cabeçalho da Section com controles -->
    <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
      <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">
        Section {{ sectionIndex + 1 }}
      </h3>
      <div class="flex items-center space-x-1">
        <button 
          type="button"
          @click="$emit('move', 'up')"
          class="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded"
          :disabled="isFirst"
        >
          <ArrowUp class="w-4 h-4" />
        </button>
        <button
          type="button"
          @click="$emit('move', 'down')"
          class="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded"
          :disabled="isLast"
        >
          <ArrowDown class="w-4 h-4" />
        </button>
        <button
          type="button"
          @click="$emit('duplicate')"
          class="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded"
        >
          <Copy class="w-4 h-4" />
        </button>
        <button
          type="button"
          @click="$emit('remove')"
          class="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-red-500 hover:text-red-600"
        >
          <Trash class="w-4 h-4" />
        </button>
      </div>
    </div>

    <!-- Grid de 12 colunas para os componentes -->
    <div class="grid grid-cols-12 gap-4">
      <!-- Componentes dentro da Section -->
      <draggable
        v-model="sectionComponents"
        v-bind="dropOptions"
        item-key="id"
        @add="handleComponentAdd"
        class="col-span-12 grid grid-cols-12 gap-4 min-h-[100px]"
      >
        <template #header v-if="!sectionComponents.length">
          <div class="flex flex-col items-center justify-center min-h-[100px] border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-lg p-4 col-span-12">
            <p class="text-sm text-gray-500 dark:text-gray-400 text-center">
              Arraste componentes para esta seção
            </p>
          </div>
        </template>
        
        <template #item="{ element }">
          <div 
            :class="[
              'relative group bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all duration-200',
              `col-span-${element.props?.columnSpan || 12}`
            ]"
            :data-component-id="element.id"
            draggable="true"
            @dragstart="$emit('component-dragstart', element, $event)"
          >
            <!-- Barra de ações do componente -->
            <div class="absolute right-4 top-4 opacity-0 group-hover:opacity-100 transition-opacity flex space-x-1 bg-white dark:bg-gray-800 rounded-lg shadow-lg p-1 z-50">
              <button 
                type="button"
                @click="$emit('component-move', element, 'up')"
                class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded"
                :disabled="isFirstComponent(element)"
              >
                <ArrowUp class="w-4 h-4" />
              </button>
              <button
                type="button"
                @click="$emit('component-move', element, 'down')"
                class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded"
                :disabled="isLastComponent(element)"
              >
                <ArrowDown class="w-4 h-4" />
              </button>
              <button
                type="button"
                @click="$emit('component-duplicate', element)"
                class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded"
              >
                <Copy class="w-4 h-4" />
              </button>
              <button
                type="button"
                @click="$emit('component-edit', element)"
                class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded"
              >
                <Pencil class="w-4 h-4" />
              </button>
              <button
                type="button"
                @click="$emit('component-remove', element)"
                class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-red-500 hover:text-red-600"
              >
                <Trash class="w-4 h-4" />
              </button>
            </div>

            <!-- Renderização do componente -->
            <component 
              :is="getComponentByType(element.type)" 
              v-bind="element.props" 
              class="w-full"
            />
          </div>
        </template>
      </draggable>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { ArrowUp, ArrowDown, Copy, Pencil, Trash } from 'lucide-vue-next'
import draggable from 'vuedraggable'
import { v4 as uuidv4 } from 'uuid'

// Importar componentes dinâmicos
const getComponentByType = (type) => {
  // Aqui você pode mapear os tipos para os componentes reais
  // Exemplo: return type === 'header' ? HeaderComponent : DefaultComponent
  return 'div' // Placeholder
}

const props = defineProps({
  section: {
    type: Object,
    required: true
  },
  sectionIndex: {
    type: Number,
    required: true
  },
  isFirst: {
    type: Boolean,
    default: false
  },
  isLast: {
    type: Boolean,
    default: false
  },
  dropOptions: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits([
  'update:section', 
  'move', 
  'duplicate', 
  'remove', 
  'component-dragstart',
  'component-move',
  'component-duplicate',
  'component-edit',
  'component-remove'
])

// Componentes dentro da seção
const sectionComponents = computed({
  get: () => props.section.components || [],
  set: (value) => {
    emit('update:section', { ...props.section, components: value })
  }
})

// Verificar se um componente é o primeiro ou o último
const isFirstComponent = (component) => {
  const index = sectionComponents.value.findIndex(c => c.id === component.id)
  return index === 0
}

const isLastComponent = (component) => {
  const index = sectionComponents.value.findIndex(c => c.id === component.id)
  return index === sectionComponents.value.length - 1
}

// Manipular a adição de um componente
const handleComponentAdd = (event) => {
  // Quando um componente é adicionado, abrimos um modal para perguntar quantas colunas ele deve ocupar
  const addedComponent = sectionComponents.value[event.newIndex]
  
  if (addedComponent) {
    // Inicializa as propriedades necessárias
    if (!addedComponent.props) {
      addedComponent.props = {}
    }
    
    // Emitir evento para abrir o modal de configuração de colunas
    emit('component-added', addedComponent, props.section.id)
  }
}
</script> 