<!-- PageBuilder.vue -->
<template>
  <div class="page-builder w-full bg-white dark:bg-gray-800">
    <!-- Barra de ferramentas -->
    <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 p-4 sticky top-0 z-50">
      <div class="flex items-center justify-between">
        <h3 class="text-lg font-medium">Construtor de Páginas</h3>
        <div class="flex items-center space-x-2">
          <button type="button"
            @click="savePageStructure"
            :disabled="isSaving"
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring disabled:opacity-25 transition"
            :class="[
              saveSuccess 
                ? 'bg-green-600 hover:bg-green-700 active:bg-green-800 focus:border-green-800 focus:ring-green-200' 
                : 'bg-blue-600 hover:bg-blue-700 active:bg-blue-800 focus:border-blue-800 focus:ring-blue-200'
            ]"
          >
            <template v-if="isSaving">
              <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Salvando...
            </template>
            <template v-else-if="saveSuccess">
              <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              Salvo!
            </template>
            <template v-else>
              <FileDown class="w-4 h-4 mr-2" />
              Salvar
            </template>
          </button>
          <button type="button"
            @click="previewPage"
            class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-800 focus:outline-none focus:border-gray-800 focus:ring focus:ring-gray-200 disabled:opacity-25 transition"
          >
            <Eye class="w-4 h-4 mr-2" />
            Pré-visualizar
          </button>
        </div>
      </div>
    </div>

    <!-- Área de construção -->
    <div class="flex min-h-screen bg-white dark:bg-gray-800">
      <!-- Componentes disponíveis -->
      <div class="w-64 flex-shrink-0 bg-gray-100 dark:bg-gray-800 p-4 border-r border-gray-200 dark:border-gray-700 overflow-y-auto sticky top-[73px] h-[calc(100vh-73px)]">
        <div class="space-y-4">
          <!-- Botão Adicionar Section -->
          <div class="mb-6">
            <button
              @click="addSection"
              class="w-full flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
            >
              <Plus class="w-4 h-4 mr-2" />
              Adicionar Section
            </button>
          </div>

          <!-- Seção de componentes -->
          <div>
            <h4 class="text-sm font-medium mb-4 text-gray-900 dark:text-gray-100">Componentes</h4>
            <draggable
              :list="availableComponents"
              v-bind="dragOptions"
              :clone="cloneComponent"
              item-key="type"
              :sort="false"
              class="space-y-2"
            >
              <template #item="{ element }">
                <div
                  class="bg-white dark:bg-gray-800 p-3 rounded-lg shadow-sm cursor-move hover:shadow-md transition-all hover:scale-[1.02] border border-gray-200 dark:border-gray-700"
                >
                  <div class="flex items-center">
                    <component :is="element.icon" class="w-5 h-5 mr-2 text-blue-500" />
                    <span class="text-sm text-gray-900 dark:text-gray-100">{{ element.title }}</span>
                  </div>
                </div>
              </template>
            </draggable>
          </div>

          <!-- Seção de estilos globais -->
          <div v-if="sections.length > 0">
            <h4 class="text-sm font-medium mb-4 text-gray-900 dark:text-gray-100">Estilos Globais</h4>
            <div class="space-y-3">
              <!-- Tema -->
              <div>
                <label class="text-xs font-medium mb-1 block text-gray-900 dark:text-gray-100">Tema</label>
                <select v-model="globalStyles.theme" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                  <option value="light">Claro</option>
                  <option value="dark">Escuro</option>
                </select>
              </div>

              <!-- Espaçamento entre Seções -->
              <div>
                <label class="text-xs font-medium mb-1 block text-gray-900 dark:text-gray-100">Espaçamento entre Seções</label>
                <select v-model="globalStyles.spacing" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                  <option value="compact">Compacto</option>
                  <option value="normal">Normal</option>
                  <option value="relaxed">Relaxado</option>
                </select>
              </div>

              <!-- Largura do conteúdo -->
              <div>
                <label class="text-xs font-medium mb-1 block text-gray-900 dark:text-gray-100">Largura do Conteúdo</label>
                <select v-model="globalStyles.contentWidth" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                  <option value="full">Largura Total</option>
                  <option value="contained">Contido</option>
                  <option value="narrow">Estreito</option>
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Área principal de drop -->
      <div class="flex-1 p-8 bg-gray-100 dark:bg-gray-900">
        <div 
          class="min-h-[500px] relative bg-white dark:bg-gray-800 rounded-lg shadow-sm w-full"
          :class="{
            'max-w-7xl mx-auto': globalStyles.contentWidth === 'contained',
            'max-w-4xl mx-auto': globalStyles.contentWidth === 'narrow',
            'w-full': globalStyles.contentWidth === 'full'
          }"
        >
          <!-- Lista de seções -->
          <div 
            class="min-h-[500px] bg-white dark:bg-gray-800 p-12 rounded-lg"
            :class="{
              'space-y-12': globalStyles.spacing === 'compact',
              'space-y-20': globalStyles.spacing === 'normal',
              'space-y-28': globalStyles.spacing === 'relaxed'
            }"
          >
            <!-- Mensagem quando não há seções -->
            <div v-if="!sections.length" class="flex flex-col items-center justify-center min-h-[500px] border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-lg p-8">
                <FilePlus class="w-12 h-12 text-gray-400 mb-4" />
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Comece a construir sua página</h3>
              <p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-4">Clique no botão "Adicionar Section" para começar a criar sua página.</p>
                  <button 
                @click="addSection"
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
              >
                <Plus class="w-4 h-4 mr-2" />
                Adicionar Section
                  </button>
                </div>

            <!-- Lista de seções -->
            <template v-else>
              <Section
                v-for="(section, index) in sections"
                :key="section.id"
                :section="section"
                :section-index="index"
                :is-first="index === 0"
                :is-last="index === sections.length - 1"
                :drop-options="dropOptions"
                @update:section="updateSection($event, index)"
                @move="moveSection(index, $event)"
                @duplicate="duplicateSection(index)"
                @remove="removeSection(index)"
                @component-added="handleComponentAdded"
                @component-dragstart="handleComponentDragStart"
                @component-move="moveComponent"
                @component-duplicate="duplicateComponent"
                @component-edit="editComponent"
                @component-remove="removeComponent"
              />
            </template>
                  </div>
                </div>
                </div>

      <!-- Painel lateral de configurações -->
      <div 
        v-if="drawerOpen"
        class="fixed inset-y-0 right-0 w-96 bg-white dark:bg-gray-800 shadow-xl border-l border-gray-200 dark:border-gray-700 z-50 overflow-y-auto"
      >
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 sticky top-0 bg-white dark:bg-gray-800 z-10">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium">Configurações</h3>
                        <button
              @click="closeDrawer"
              class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full"
            >
              <X class="w-5 h-5" />
                        </button>
                      </div>
                      </div>
                      
        <div class="p-6">
          <ComponentSettings 
            :component="selectedComponent" 
            @update="updateComponentSettings"
          />
      </div>
    </div>

      <!-- Modal de seleção de colunas -->
      <ColumnSelectorModal
        :is-open="columnSelectorOpen"
        :initial-columns="6"
        @close="closeColumnSelector"
        @confirm="confirmColumnSelection"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, nextTick, onMounted } from 'vue'
import draggable from 'vuedraggable'
import { v4 as uuidv4 } from 'uuid'
import axios from 'axios'
import { 
  FileDown,
  Eye,
  ArrowUp,
  ArrowDown,
  Copy,
  Pencil,
  Trash,
  FilePlus,
  X,
  Plus
} from 'lucide-vue-next'

import Section from './Section.vue'
import ComponentSettings from './ComponentSettings.vue'
import ColumnSelectorModal from './ColumnSelectorModal.vue'

// Estado local
const sections = ref([])
const globalStyles = ref({
  theme: 'light',
  spacing: 'normal',
  contentWidth: 'contained'
})
const selectedComponent = ref(null)
const drawerOpen = ref(false)
const columnSelectorOpen = ref(false)
const pendingComponent = ref(null)
const pendingSectionId = ref(null)
const isSaving = ref(false)
const saveSuccess = ref(false)

// Props
const props = defineProps({
  modelValue: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['update:modelValue'])

// Opções para o draggable
const dragOptions = {
  animation: 150,
  group: {
    name: 'components',
    pull: 'clone',
    put: false
  }
}

const dropOptions = {
  animation: 150,
  group: {
    name: 'components',
    pull: true,
    put: true
  }
}

// Lista de componentes disponíveis
const availableComponents = [
  {
    type: 'header',
    title: 'Cabeçalho',
    icon: 'Type'
  },
  {
    type: 'content',
    title: 'Conteúdo',
    icon: 'FileText'
  },
  {
    type: 'benefits',
    title: 'Benefícios',
    icon: 'Award'
  },
  {
    type: 'simulator',
    title: 'Simulador',
    icon: 'Calculator'
  },
  {
    type: 'contact-channels',
    title: 'Canais de Atendimento',
    icon: 'PhoneCall'
  },
  {
    type: 'cards',
    title: 'Cards',
    icon: 'LayoutGrid'
  },
  {
    type: 'form',
    title: 'Formulário',
    icon: 'FormInput'
  }
]

// Métodos para gerenciar seções
const addSection = () => {
  const newSection = {
    id: uuidv4(),
    components: [],
    props: {
      spacing: 'normal',
      backgroundColor: 'white',
      paddingY: '12',
      paddingX: '4'
    }
  }
  sections.value.push(newSection)
  emit('update:modelValue', sections.value)
}

const updateSection = (updatedSection, index) => {
  sections.value[index] = updatedSection
  emit('update:modelValue', sections.value)
}

const moveSection = (index, direction) => {
  if (direction === 'up' && index > 0) {
    const temp = sections.value[index - 1]
    sections.value[index - 1] = sections.value[index]
    sections.value[index] = temp
  } else if (direction === 'down' && index < sections.value.length - 1) {
    const temp = sections.value[index + 1]
    sections.value[index + 1] = sections.value[index]
    sections.value[index] = temp
  }
  emit('update:modelValue', sections.value)
}

const duplicateSection = (index) => {
  const sectionToDuplicate = sections.value[index]
  const newSection = JSON.parse(JSON.stringify(sectionToDuplicate))
  newSection.id = uuidv4()
  newSection.components = newSection.components.map(component => ({
    ...component,
    id: uuidv4()
  }))
  sections.value.splice(index + 1, 0, newSection)
  emit('update:modelValue', sections.value)
}

const removeSection = (index) => {
  sections.value.splice(index, 1)
  emit('update:modelValue', sections.value)
}

// Métodos para gerenciar componentes
const handleComponentAdded = (component, sectionId) => {
  pendingComponent.value = component
  pendingSectionId.value = sectionId
  columnSelectorOpen.value = true
}

const closeColumnSelector = () => {
  columnSelectorOpen.value = false
  pendingComponent.value = null
  pendingSectionId.value = null
}

const confirmColumnSelection = (columns) => {
  if (pendingComponent.value && pendingSectionId.value) {
    const sectionIndex = sections.value.findIndex(s => s.id === pendingSectionId.value)
    if (sectionIndex !== -1) {
      pendingComponent.value.props.columnSpan = columns
      sections.value[sectionIndex].components.push(pendingComponent.value)
      emit('update:modelValue', sections.value)
    }
  }
  closeColumnSelector()
}

const editComponent = (component) => {
  selectedComponent.value = component
  drawerOpen.value = true
}

const closeDrawer = () => {
  drawerOpen.value = false
  selectedComponent.value = null
}

const updateComponentSettings = (componentId, newSettings, sectionId) => {
  const sectionIndex = sections.value.findIndex(s => s.id === sectionId)
  if (sectionIndex !== -1) {
    const componentIndex = sections.value[sectionIndex].components.findIndex(c => c.id === componentId)
    if (componentIndex !== -1) {
      sections.value[sectionIndex].components[componentIndex].props = newSettings
      emit('update:modelValue', sections.value)
    }
  }
}

const moveComponent = (component, direction, sectionId) => {
  const sectionIndex = sections.value.findIndex(s => s.id === sectionId)
  if (sectionIndex !== -1) {
    const components = sections.value[sectionIndex].components
    const componentIndex = components.findIndex(c => c.id === component.id)
    if (direction === 'up' && componentIndex > 0) {
      const temp = components[componentIndex - 1]
      components[componentIndex - 1] = components[componentIndex]
      components[componentIndex] = temp
    } else if (direction === 'down' && componentIndex < components.length - 1) {
      const temp = components[componentIndex + 1]
      components[componentIndex + 1] = components[componentIndex]
      components[componentIndex] = temp
    }
    emit('update:modelValue', sections.value)
  }
}

const duplicateComponent = (component, sectionId) => {
  const sectionIndex = sections.value.findIndex(s => s.id === sectionId)
  if (sectionIndex !== -1) {
    const newComponent = JSON.parse(JSON.stringify(component))
    newComponent.id = uuidv4()
    const componentIndex = sections.value[sectionIndex].components.findIndex(c => c.id === component.id)
    sections.value[sectionIndex].components.splice(componentIndex + 1, 0, newComponent)
    emit('update:modelValue', sections.value)
  }
}

const removeComponent = (component, sectionId) => {
  const sectionIndex = sections.value.findIndex(s => s.id === sectionId)
  if (sectionIndex !== -1) {
    const componentIndex = sections.value[sectionIndex].components.findIndex(c => c.id === component.id)
    if (componentIndex !== -1) {
      sections.value[sectionIndex].components.splice(componentIndex, 1)
      emit('update:modelValue', sections.value)
    }
  }
}

// Salvar e carregar
const savePageStructure = async () => {
  try {
    isSaving.value = true
    const structure = {
      sections: sections.value,
      globalStyles: globalStyles.value
    }
    
    await axios.post('/api/page-builder/save', structure)
    
    saveSuccess.value = true
    setTimeout(() => {
      saveSuccess.value = false
    }, 2000)
  } catch (error) {
    console.error('Erro ao salvar a estrutura da página:', error)
  } finally {
    isSaving.value = false
  }
}

const loadPageStructure = async () => {
  try {
    const response = await axios.get('/api/page-builder/load')
    if (response.data.success) {
      sections.value = response.data.data.sections || []
      globalStyles.value = response.data.data.globalStyles || {
        theme: 'light',
        spacing: 'normal',
        contentWidth: 'contained'
      }
      emit('update:modelValue', sections.value)
    }
  } catch (error) {
    console.error('Erro ao carregar a estrutura da página:', error)
  }
}

const previewPage = () => {
  // Implementar lógica de pré-visualização
  console.log('Pré-visualizando página:', sections.value)
}

// Observar mudanças no modelValue
watch(() => props.modelValue, (newValue) => {
  if (newValue) {
    sections.value = newValue
  }
}, { deep: true })

// Inicialização
onMounted(() => {
  loadPageStructure()
})
</script>

<style>
.nest-highlight {
  @apply ring-2 ring-blue-500 ring-opacity-50;
}

.nested-component {
  @apply cursor-move;
}

.resize-handle {
  @apply absolute w-3 h-3 bg-blue-500 rounded-full opacity-0 group-hover:opacity-100 transition-opacity;
}

.resize-handle-n { @apply top-0 left-1/2 -translate-x-1/2 -translate-y-1/2 cursor-ns-resize; }
.resize-handle-e { @apply top-1/2 right-0 -translate-y-1/2 translate-x-1/2 cursor-ew-resize; }
.resize-handle-s { @apply bottom-0 left-1/2 -translate-x-1/2 translate-y-1/2 cursor-ns-resize; }
.resize-handle-w { @apply top-1/2 left-0 -translate-y-1/2 -translate-x-1/2 cursor-ew-resize; }
.resize-handle-nw { @apply top-0 left-0 -translate-x-1/2 -translate-y-1/2 cursor-nw-resize; }
.resize-handle-ne { @apply top-0 right-0 translate-x-1/2 -translate-y-1/2 cursor-ne-resize; }
.resize-handle-se { @apply bottom-0 right-0 translate-x-1/2 translate-y-1/2 cursor-se-resize; }
.resize-handle-sw { @apply bottom-0 left-0 -translate-x-1/2 translate-y-1/2 cursor-sw-resize; }
</style>