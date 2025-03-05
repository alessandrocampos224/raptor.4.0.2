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
              <!-- Ícone de carregamento animado -->
              <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Salvando...
            </template>
            <template v-else-if="saveSuccess">
              <!-- Ícone de check -->
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
          <div v-if="pageComponents.length > 0">
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

              <!-- Espaçamento entre Componentes -->
              <div>
                <label class="text-xs font-medium mb-1 block text-gray-900 dark:text-gray-100">Espaçamento entre Componentes</label>
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
          <!-- Lista de componentes -->
          <draggable
            v-model="pageComponents"
            v-bind="dropOptions"
            item-key="id"
            @add="handleMainDragAdd"
            :class="[
                'min-h-[500px] bg-white dark:bg-gray-800 p-12 rounded-lg grid grid-cols-12 gap-4',
                {
                    'gap-y-8': globalStyles.spacing === 'compact',
                    'gap-y-16': globalStyles.spacing === 'normal',
                    'gap-y-24': globalStyles.spacing === 'relaxed'
                }
            ]"
          >
            <template #header v-if="!pageComponents.length">
              <div class="flex flex-col items-center justify-center min-h-[500px] border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-lg p-8 col-span-12">
                <FilePlus class="w-12 h-12 text-gray-400 mb-4" />
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Comece a construir sua página</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-4">Arraste e solte os componentes da barra lateral para começar a criar sua página.</p>
              </div>
            </template>
            
            <template #item="{ element }">
              <div 
                :class="[
                  'relative group bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all duration-200',
                  `col-span-${element.props?.columnSpan || 12}`,
                  { 'nest-highlight': isDragging && canNestComponent(element) }
                ]"
                :data-component-id="element.id"
                draggable="true"
                @dragstart="handleDragStart(element, $event)"
              >
                <!-- Barra de ações do componente -->
                <div class="absolute right-4 top-4 opacity-0 group-hover:opacity-100 transition-opacity flex space-x-1 bg-white dark:bg-gray-800 rounded-lg shadow-lg p-1 z-50">
                  <button 
                    type="button"
                    @click="moveComponent(element, 'up')"
                    class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded"
                    :disabled="isFirstComponent(element)"
                  >
                    <ArrowUp class="w-4 h-4" />
                  </button>
                  <button
                    type="button"
                    @click="moveComponent(element, 'down')"
                    class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded"
                    :disabled="isLastComponent(element)"
                  >
                    <ArrowDown class="w-4 h-4" />
                  </button>
                  <button
                    type="button"
                    @click="duplicateComponent(element)"
                    class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded"
                  >
                    <Copy class="w-4 h-4" />
                  </button>
                  <button
                    type="button"
                    @click="editComponent(element)"
                    class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded"
                  >
                    <Pencil class="w-4 h-4" />
                  </button>
                  <button
                    type="button"
                    @click="removeComponent(element)"
                    class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-red-500 hover:text-red-600"
                  >
                    <Trash class="w-4 h-4" />
                  </button>
                </div>

                <!-- Container do componente -->
                <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-t-xl border-b border-gray-200 dark:border-gray-700">
                  <div class="flex items-center space-x-2">
                    <component :is="element.icon" class="w-5 h-5 text-blue-500" />
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ element.type === 'header' ? 'Cabeçalho' : element.type === 'content' ? 'Conteúdo' : element.type === 'benefits' ? 'Benefícios' : element.type === 'simulator' ? 'Simulador' : element.type === 'contact-channels' ? 'Canais de Atendimento' : element.type === 'cards' ? 'Cards' : 'Formulário' }}</span>
                  </div>
                </div>

                <!-- Renderização do componente -->
                <div class="p-8">
                  <component
                    :is="getComponentByType(element.type)"
                    v-bind="element.props || getDefaultProps(element.type)"
                    @update:selectedPosts="(posts) => updateComponentPosts(element, posts)"
                    class="w-full"
                  />
                </div>

                <!-- Área para componentes aninhados com posicionamento livre -->
                <div 
                  class="relative p-4 border-t border-gray-200 dark:border-gray-700 min-h-[200px]"
                  :class="{ 'nest-dropzone-active': isNestingTarget(element) }"
                >
                  <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">Componentes Aninhados</h4>
                  
                  <!-- Área de posicionamento livre para componentes aninhados -->
                  <div 
                    class="relative min-h-[200px] border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-lg p-4 overflow-hidden nest-container"
                    :id="`nest-container-${element.id}`"
                    @dragover.prevent="handleDragOver(element, $event)"
                    @dragleave.prevent="handleDragLeave(element, $event)"
                    @drop.prevent="handleDrop(element, $event)"
                  >
                    <!-- Mensagem quando não há componentes -->
                    <div 
                      v-if="!element.children || !element.children.length" 
                      class="flex flex-col items-center justify-center h-full"
                    >
                      <p class="text-sm text-gray-500 dark:text-gray-400 text-center">
                        Arraste componentes para esta área
                      </p>
                    </div>
                    
                    <!-- Componentes aninhados com posicionamento livre -->
                    <div 
                      v-for="childElement in element.children" 
                      :key="childElement.id"
                      class="absolute bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all duration-200 cursor-move nested-component"
                      :style="{
                        left: `${childElement.position?.x || 0}px`,
                        top: `${childElement.position?.y || 0}px`,
                        width: `${childElement.position?.width || 200}px`,
                        height: childElement.position?.height === 'auto' ? 'auto' : `${childElement.position?.height || 'auto'}px`,
                        zIndex: childElement.position?.zIndex || 1
                      }"
                      draggable="true"
                      @dragstart="handleDragStart(childElement, $event)"
                      @mousedown="startDrag($event, element, childElement)"
                      :data-child-id="childElement.id"
                      :data-parent-id="element.id"
                    >
                      <!-- Barra de ações do componente aninhado -->
                      <div class="absolute right-2 top-2 opacity-0 group-hover:opacity-100 transition-opacity flex space-x-1 bg-white dark:bg-gray-800 rounded-lg shadow-lg p-1 z-50">
                        <button
                          type="button"
                          @click="editNestedComponent(element, childElement)"
                          class="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded"
                        >
                          <Pencil class="w-3 h-3" />
                        </button>
                        <button
                          type="button"
                          @click="removeNestedComponent(element, childElement)"
                          class="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-red-500"
                        >
                          <Trash class="w-3 h-3" />
                        </button>
                        <button
                          type="button"
                          @click="bringToFront(element, childElement)"
                          class="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded"
                        >
                          <ArrowUp class="w-3 h-3" />
                        </button>
                        <button
                          type="button"
                          @click="sendToBack(element, childElement)"
                          class="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded"
                        >
                          <ArrowDown class="w-3 h-3" />
                        </button>
                        <button
                          type="button"
                          @click="unnestComponent(element, childElement)"
                          class="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-blue-500"
                        >
                          <ExternalLink class="w-3 h-3" />
                        </button>
                      </div>
                      
                      <!-- Indicador de tipo de componente -->
                      <div class="absolute left-2 top-2 bg-gray-100 dark:bg-gray-700 text-xs px-2 py-1 rounded">
                        {{ childElement.type }}
                      </div>
                      
                      <!-- Renderização do componente aninhado -->
                      <div class="p-4 pt-8">
                        <component
                          :is="getComponentByType(childElement.type)"
                          v-bind="childElement.props || getDefaultProps(childElement.type)"
                          class="w-full"
                        />
                      </div>
                      
                      <!-- Alças de redimensionamento -->
                      <div class="resize-handle resize-handle-se"></div>
                      <div class="resize-handle resize-handle-sw"></div>
                      <div class="resize-handle resize-handle-ne"></div>
                      <div class="resize-handle resize-handle-nw"></div>
                      <div class="resize-handle resize-handle-n"></div>
                      <div class="resize-handle resize-handle-e"></div>
                      <div class="resize-handle resize-handle-s"></div>
                      <div class="resize-handle resize-handle-w"></div>
                    </div>
                  </div>
                </div>
              </div>
            </template>
          </draggable>
        </div>
      </div>
    </div>

    <!-- Painel de configurações -->
    <div v-if="drawerOpen" class="fixed inset-0 z-[50] overflow-hidden">
      <!-- Overlay para fechar o painel ao clicar fora -->
      <div class="absolute inset-0 bg-black/50" @click="closeDrawer"></div>
      
      <!-- Painel de configuração -->
      <div class="fixed top-0 right-0 bottom-0 w-[450px] max-w-full bg-white dark:bg-gray-800 shadow-xl flex flex-col h-screen z-[51]">
        <!-- Cabeçalho do painel -->
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between bg-white dark:bg-gray-800">
          <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Configurações</h3>
          <button 
            type="button" 
            @click="closeDrawer" 
            class="rounded-sm opacity-70 transition-opacity hover:opacity-100 focus:outline-none"
          >
            <X class="h-5 w-5" />
            <span class="sr-only">Fechar</span>
          </button>
        </div>
        
        <!-- Conteúdo do painel -->
        <div class="flex-1 overflow-y-auto p-6">
          <ComponentSettings 
            v-if="selectedComponent" 
            :component="selectedComponent" 
            @update="updateComponentSettings"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, nextTick, onMounted, onBeforeUnmount } from 'vue'
import draggable from 'vuedraggable/src/vuedraggable'
import { 
  Pencil, 
  Trash, 
  FileText, 
  Image, 
  Menu, 
  Columns, 
  ClipboardList, 
  Phone,
  Eye,
  FileDown,
  ArrowUp,
  ArrowDown,
  Copy,
  X,
  FilePlus,
  Gift,
  Calculator,
  Users,
  Mail,
  Calendar,
  Clock,
  ExternalLink,
  MapPin
} from 'lucide-vue-next'
import { v4 as uuidv4 } from 'uuid'
import ComponentSettings from './ComponentSettings.vue'
import HeaderBlock from './Blocks/HeaderBlock.vue'
import ContentBlock from './Blocks/ContentBlock.vue'
import GalleryBlock from './Blocks/GalleryBlock.vue'
import CardBlock from './Blocks/CardBlock.vue'
import FormBlock from './Blocks/FormBlock.vue'
import ContactBlock from './Blocks/ContactBlock.vue'
import BenefitsBlock from './Blocks/BenefitsBlock.vue'
import SimulatorBlock from './Blocks/SimulatorBlock.vue'
import ContactChannelsBlock from './Blocks/ContactChannelsBlock.vue'
import axios from 'axios'

// Props
const props = defineProps({
    modelValue: {
        type: Array,
        default: () => []
    },
    globalStylesValue: {
        type: Object,
        default: () => ({
            theme: 'light',
            spacing: 'normal',
            contentWidth: 'contained'
        })
    },
    // Props adicionais para compatibilidade
    breadcrumbs: {
        type: Array,
        default: () => []
    },
    sections: {
        type: Array,
        default: () => []
    },
    record: {
        type: Object,
        default: () => ({})
    },
    config: {
        type: Object,
        default: () => ({})
    },
    actions: {
        type: Array,
        default: () => []
    }
})

// Emits
const emit = defineEmits(['update:modelValue', 'update:globalStylesValue'])

// Estado da página
// Se modelValue estiver vazio mas sections não, use sections
const pageComponents = ref(JSON.parse(JSON.stringify(
    props.modelValue?.length ? props.modelValue : props.sections || []
)))
const selectedComponent = ref(null)
const parentComponent = ref(null)
const drawerOpen = ref(false)
const isSaving = ref(false)
const saveSuccess = ref(false)
const globalStyles = ref(JSON.parse(JSON.stringify(props.globalStylesValue || {
    theme: 'light',
    spacing: 'normal',
    contentWidth: 'contained'
})))

// Estado para o aninhamento flexível
const isDragging = ref(false)
const draggedComponent = ref(null)
const nestingTarget = ref(null)
const nestContainer = ref(null)
const isResizing = ref(false)
const resizeDirection = ref(null)
const resizeStartX = ref(0)
const resizeStartY = ref(0)
const resizeStartWidth = ref(0)
const resizeStartHeight = ref(0)
const dragStartX = ref(0)
const dragStartY = ref(0)
const dragOffsetX = ref(0)
const dragOffsetY = ref(0)
const currentDragElement = ref(null)
const currentDragParent = ref(null)
const dragStartPosX = ref(0)
const dragStartPosY = ref(0)

// Adicionar uma flag para controlar se o drop já foi processado
const dropProcessed = ref(false);

// Função para comparar objetos profundamente
const isEqual = (obj1, obj2) => {
    return JSON.stringify(obj1) === JSON.stringify(obj2)
}

// Observa mudanças nos componentes
watch(pageComponents, (newValue) => {
    console.log('PageBuilder - Componentes alterados:', newValue);
    
    // Garante que allowNesting seja sempre um booleano em todos os componentes
    const updatedComponents = newValue.map(component => ensureAllowNestingIsBoolean(component));
    
    // Atualiza os componentes apenas se houver mudanças
    if (JSON.stringify(updatedComponents) !== JSON.stringify(newValue)) {
        pageComponents.value = updatedComponents;
    }
    
    if (!isEqual(newValue, props.modelValue)) {
        emit('update:modelValue', JSON.parse(JSON.stringify(updatedComponents)));
    }
}, { deep: true })

// Observa mudanças nos estilos globais
watch(globalStyles, (newValue) => {
    console.log('PageBuilder - Estilos globais alterados:', newValue);
    // Força a emissão imediata dos novos valores
    emit('update:globalStylesValue', JSON.parse(JSON.stringify(newValue)));
    // Força uma atualização dos componentes também para garantir re-renderização
    emit('update:modelValue', JSON.parse(JSON.stringify(pageComponents.value)));
}, { deep: true, immediate: true })

// Observa mudanças nas props
watch(() => props.modelValue, (newValue) => {
    console.log('PageBuilder - Props modelValue alterado:', newValue);
    if (!isEqual(newValue, pageComponents.value)) {
        pageComponents.value = JSON.parse(JSON.stringify(newValue || []));
    }
}, { immediate: true })

watch(() => props.globalStylesValue, (newValue) => {
    console.log('PageBuilder - Props globalStylesValue alterado:', newValue);
    if (!isEqual(newValue, globalStyles.value)) {
        globalStyles.value = JSON.parse(JSON.stringify(newValue || {
            theme: 'light',
            spacing: 'normal',
            contentWidth: 'contained'
        }));
    }
}, { immediate: true })

// Adiciona evento de teclado para fechar o drawer com ESC
onMounted(() => {
  window.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && drawerOpen.value) {
      closeDrawer()
    }
  })
  
  // Adicionar event listeners para as alças de redimensionamento
  document.addEventListener('mousedown', (event) => {
    const resizeHandle = event.target.closest('.resize-handle');
    if (resizeHandle) {
      const direction = Array.from(resizeHandle.classList)
        .find(cls => cls.startsWith('resize-handle-'))
        ?.replace('resize-handle-', '');
      
      if (direction) {
        const childElement = resizeHandle.closest('[data-child-id]');
        const parentElement = childElement?.closest('[data-parent-id]');
        
        if (childElement && parentElement) {
          const childId = childElement.dataset.childId;
          const parentId = parentElement.dataset.parentId;
          
          const parent = pageComponents.value.find(c => c.id === parentId);
          const child = parent?.children?.find(c => c.id === childId);
          
          if (parent && child) {
            startResize(event, parent, child, direction);
          }
        }
      }
    }
  });
  
  // Inicializar o estado com os valores do modelo
  if (props.modelValue && props.modelValue.length > 0) {
    pageComponents.value = JSON.parse(JSON.stringify(props.modelValue));
  }
});

onBeforeUnmount(() => {
  window.removeEventListener('keydown', (e) => {
    if (e.key === 'Escape' && drawerOpen.value) {
      closeDrawer()
    }
  });
  
  // Remover event listeners
  document.removeEventListener('mousemove', handleDragMove);
  document.removeEventListener('mouseup', handleDragEnd);
  document.removeEventListener('mousemove', handleResizeMove);
  document.removeEventListener('mouseup', handleResizeEnd);
});

// Componentes disponíveis
const availableComponents = ref([
  {
    type: 'header',
    title: 'Cabeçalho',
    icon: Menu,
    defaultProps: {
      title: 'Novo Cabeçalho',
      subtitle: 'Subtítulo opcional',
      // Container
      containerWidth: 'default',
      textAlignment: 'left',
      // Background
      backgroundColor: 'white',
      backgroundImage: '',
      overlayOpacity: 0,
      // Bordas e Sombras
      shadow: 'none',
      borderStyle: 'none',
      borderWidth: '0',
      borderColor: 'gray-200',
      borderRadius: 'none',
      // Espaçamento
      paddingY: '12',
      paddingX: '4',
      marginY: '0',
      marginX: '0',
      spacing: 'normal',
      // Título
      titleSize: '4xl',
      titleWeight: 'bold',
      titleColor: 'gray-900',
      titleFont: 'font-sans',
      // Subtítulo
      subtitleSize: 'xl',
      subtitleWeight: 'normal',
      subtitleColor: 'gray-600',
      subtitleFont: 'font-sans',
      // Botões de ação
      actions: []
    }
  },
  {
    type: 'content',
    title: 'Conteúdo',
    icon: FileText,
    defaultProps: {
      content: '',
      // Container
      containerWidth: 'default',
      textAlignment: 'left',
      // Background
      backgroundColor: 'white',
      backgroundImage: '',
      overlayOpacity: 0,
      // Bordas e Sombras
      shadow: 'none',
      borderStyle: 'none',
      borderWidth: '0',
      borderColor: 'gray-200',
      borderRadius: 'none',
      // Espaçamento
      paddingY: '12',
      paddingX: '4',
      marginY: '0',
      marginX: '0',
      spacing: 'normal',
      // Texto
      textSize: 'base',
      textColor: 'gray-900',
      textFont: 'font-sans',
      style: 'default',
      layout: 'default'
    }
  },
  {
    type: 'benefits',
    title: 'Cards de Benefícios',
    icon: Gift,
    defaultProps: {
      items: [
        {
          icon: 'Clock',
          title: 'Título do Benefício',
          description: 'Descrição do benefício',
          link: {
            text: 'Ver mais',
            url: '#'
          }
        }
      ],
      // Container
      containerWidth: 'default',
      textAlignment: 'left',
      // Background
      backgroundColor: 'white',
      backgroundImage: '',
      overlayOpacity: 0,
      // Bordas e Sombras
      shadow: 'none',
      borderStyle: 'none',
      borderWidth: '0',
      borderColor: 'gray-200',
      borderRadius: 'none',
      // Espaçamento
      paddingY: '12',
      paddingX: '4',
      marginY: '0',
      marginX: '0',
      spacing: 'normal',
      // Cards
      cardBackgroundColor: 'white',
      cardShadow: 'sm',
      cardBorderRadius: 'lg',
      // Título dos Cards
      cardTitleSize: 'xl',
      cardTitleWeight: 'bold',
      cardTitleColor: 'gray-900',
      cardTitleFont: 'font-sans',
      // Descrição dos Cards
      cardDescriptionSize: 'base',
      cardDescriptionColor: 'gray-600',
      cardDescriptionFont: 'font-sans',
      // Layout
      layout: 'grid',
      columns: 3,
      style: 'default',
      spacingVertical: 'default',
      spacingHorizontal: 'default'
    }
  },
  {
    type: 'simulator',
    title: 'Simulador',
    icon: Calculator,
    defaultProps: {
      title: 'Simulador de Benefícios',
      description: 'Faça uma simulação do seu benefício previdenciário.',
      // Container
      containerWidth: 'default',
      textAlignment: 'left',
      // Background
      backgroundColor: 'white',
      backgroundImage: '',
      overlayOpacity: 0,
      // Bordas e Sombras
      shadow: 'none',
      borderStyle: 'none',
      borderWidth: '0',
      borderColor: 'gray-200',
      borderRadius: 'none',
      // Espaçamento
      paddingY: '12',
      paddingX: '4',
      // Título
      titleSize: '3xl',
      titleWeight: 'bold',
      titleColor: 'gray-900',
      titleFont: 'font-sans',
      // Descrição
      descriptionSize: 'lg',
      descriptionColor: 'gray-600',
      descriptionFont: 'font-sans',
      // Formulário
      formBackgroundColor: 'gray-50',
      formBorderRadius: 'lg',
      formPadding: '6',
      // Campos
      fields: [
        {
          type: 'select',
          name: 'benefitType',
          label: 'Tipo de Benefício',
          options: []
        },
        {
          type: 'date',
          name: 'birthDate',
          label: 'Data de Nascimento'
        },
        {
          type: 'number',
          name: 'contributionTime',
          label: 'Tempo de Contribuição (anos)'
        },
        {
          type: 'currency',
          name: 'lastSalary',
          label: 'Última Remuneração'
        }
      ],
      submitText: 'Calcular'
    }
  },
  {
    type: 'contact-channels',
    title: 'Canais de Atendimento',
    icon: Phone,
    defaultProps: {
      title: 'Canais de Atendimento',
      // Container
      containerWidth: 'default',
      textAlignment: 'left',
      // Background
      backgroundColor: 'white',
      backgroundImage: '',
      overlayOpacity: 0,
      // Bordas e Sombras
      shadow: 'none',
      borderStyle: 'none',
      borderWidth: '0',
      borderColor: 'gray-200',
      borderRadius: 'none',
      // Espaçamento
      paddingY: '12',
      paddingX: '4',
      // Título Principal
      titleSize: '3xl',
      titleWeight: 'bold',
      titleColor: 'gray-900',
      titleFont: 'font-sans',
      // Cards
      cardBackgroundColor: 'white',
      cardShadow: 'sm',
      cardBorderRadius: 'lg',
      cardPadding: '6',
      // Título dos Cards
      cardTitleSize: 'xl',
      cardTitleWeight: 'bold',
      cardTitleColor: 'gray-900',
      cardTitleFont: 'font-sans',
      // Descrição dos Cards
      cardDescriptionSize: 'base',
      cardDescriptionColor: 'gray-600',
      cardDescriptionFont: 'font-sans',
      channels: [
        {
          icon: 'Users',
          title: 'Atendimento Presencial',
          description: 'Atendimento personalizado na sede do Instituto',
          details: [
            { type: 'address', value: 'Rua Principal, 123 - Centro' },
            { type: 'schedule', value: 'Segunda a Sexta, 8h às 17h' }
          ]
        },
        {
          icon: 'Mail',
          title: 'Atendimento Online',
          description: 'Tire suas dúvidas por e-mail ou portal',
          details: [
            { type: 'email', value: 'atendimento@exemplo.gov.br' },
            { type: 'time', value: '24 horas por dia' }
          ]
        },
        {
          icon: 'Phone',
          title: 'Central de Atendimento',
          description: 'Atendimento por telefone',
          details: [
            { type: 'phone', value: '(67) 3239-1500' },
            { type: 'schedule', value: 'Segunda a Sexta, 8h às 18h' }
          ]
        }
      ],
      layout: 'grid',
      columns: 3,
      style: 'cards'
    }
  },
  {
    type: 'cards',
    title: 'Cards',
    icon: Columns,
    defaultProps: {
      // Título
      title: 'Novo Card',
      titleSize: '3xl',
      titleWeight: 'bold',
      titleColor: 'gray-900',
      titleFont: 'font-sans',
      // Subtítulo
      subtitle: 'Subtítulo opcional',
      subtitleSize: 'xl',
      subtitleWeight: 'normal',
      subtitleColor: 'gray-600',
      subtitleFont: 'font-sans',
      // Cards
      cards: [
        {
          title: 'Primeiro Card',
          description: 'Descrição do card',
          icon: 'document',
          link: {
            text: 'Ver mais',
            url: '#'
          }
        }
      ],
      // Container
      containerWidth: 'default',
      textAlignment: 'left',
      // Background
      backgroundColor: 'white',
      backgroundImage: '',
      overlayOpacity: 0,
      // Bordas e Sombras
      shadow: 'none',
      borderStyle: 'none',
      borderWidth: '0',
      borderColor: 'gray-200',
      borderRadius: 'none',
      // Espaçamento
      paddingY: '12',
      paddingX: '4',
      // Layout
      columns: 3,
      style: 'default',
      layout: 'default',
      // Espaçamento entre cards
      spacing: 'normal' // compact, normal, relaxed
    }
  },
  {
    type: 'form',
    title: 'Formulário',
    icon: ClipboardList,
    defaultProps: {
      title: '',
      description: '',
      // Container
      containerWidth: 'default',
      textAlignment: 'left',
      // Background
      backgroundColor: 'white',
      backgroundImage: '',
      overlayOpacity: 0,
      // Bordas e Sombras
      shadow: 'none',
      borderStyle: 'none',
      borderWidth: '0',
      borderColor: 'gray-200',
      borderRadius: 'none',
      // Espaçamento
      paddingY: '12',
      paddingX: '4',
      // Título
      titleSize: '3xl',
      titleWeight: 'bold',
      titleColor: 'gray-900',
      titleFont: 'font-sans',
      // Descrição
      descriptionSize: 'lg',
      descriptionColor: 'gray-600',
      descriptionFont: 'font-sans',
      // Formulário
      formBackgroundColor: 'gray-50',
      formBorderRadius: 'lg',
      formPadding: '6',
      // Campos
      fields: [],
      submitText: 'Enviar',
      style: 'default',
      layout: 'default'
    }
  }
])

// Métodos
const cloneComponent = (component, isNested = false) => {
  console.log('PageBuilder - Clonando componente:', component);
  
  // Cria uma cópia profunda dos defaultProps ou inicializa um objeto vazio
  const props = component.defaultProps ? JSON.parse(JSON.stringify(component.defaultProps)) : {};
  
  // Garante que as propriedades básicas existam e sejam do tipo correto
  props.style = typeof props.style === 'string' ? props.style : 'default';
  props.layout = typeof props.layout === 'string' ? props.layout : 'default';
  props.spacingVertical = typeof props.spacingVertical === 'string' ? props.spacingVertical : 'default';
  props.spacingHorizontal = typeof props.spacingHorizontal === 'string' ? props.spacingHorizontal : 'default';
  
  // Define o columnSpan padrão com base se é um componente aninhado ou não
  props.columnSpan = isNested ? 6 : 6;
  
  // Sempre define allowNesting como true para todos os componentes
  props.allowNesting = true;
  
  console.log('PageBuilder - Props do componente clonado com allowNesting:', props.allowNesting);
  
  // Retorna um novo componente com os props mapeados
  const newComponent = {
    id: uuidv4(),
    type: component.type,
    props: props,
    children: []
  };
  
  // Se for um componente aninhado, adiciona propriedades de posicionamento
  if (isNested) {
    newComponent.position = {
      x: 0,
      y: 0,
      width: 200,
      height: 'auto',
      zIndex: 1
    };
  }
  
  // Sempre armazena o componente original para uso no handleDrop
  draggedComponent.value = component;
  console.log('PageBuilder - draggedComponent definido:', draggedComponent.value);
  
  console.log('PageBuilder - Novo componente criado:', newComponent);
  return newComponent;
}

const handleComponentChange = (event) => {
    console.log('PageBuilder - Evento de mudança:', event);
    if (event.added) {
        console.log('PageBuilder - Componente adicionado:', event.added.element);
        // Garante que o componente adicionado tenha todas as propriedades necessárias
        const addedComponent = event.added.element;
        if (!addedComponent.props) {
            addedComponent.props = {};
        }
        if (!addedComponent.id) {
            addedComponent.id = uuidv4();
        }
    }
    if (event.removed) {
        console.log('PageBuilder - Componente removido:', event.removed.element);
    }
    if (event.moved) {
        console.log('PageBuilder - Componente movido:', event.moved.element);
    }
    
    // Força a atualização do v-model
    emit('update:modelValue', JSON.parse(JSON.stringify(pageComponents.value)));
}

const editComponent = (component, parentComp = null) => {
  console.log('PageBuilder - Editando componente:', component)
  
  // Garante que allowNesting seja um booleano
  const componentCopy = ensureAllowNestingIsBoolean(component);
  
  selectedComponent.value = componentCopy
  parentComponent.value = parentComp
  
  console.log('PageBuilder - Componente selecionado para edição com allowNesting:', componentCopy.props.allowNesting)
  
  drawerOpen.value = true
  
  // Adiciona a classe overflow-hidden ao body quando o Drawer é aberto
  nextTick(() => {
    document.body.classList.add('overflow-hidden')
  })
}

const closeDrawer = () => {
  // Não precisamos atualizar o componente aqui, pois isso já é feito pelo updateComponentSettings
  // Apenas fechamos o drawer e limpamos as referências
  
  drawerOpen.value = false;
  selectedComponent.value = null;
  parentComponent.value = null;
  
  // Remove a classe overflow-hidden do body quando o Drawer é fechado
  nextTick(() => {
    document.body.classList.remove('overflow-hidden')
  })
}

const removeComponent = (component) => {
  const index = pageComponents.value.findIndex(c => c.id === component.id)
  if (index !== -1) {
    pageComponents.value.splice(index, 1)
  }
}

const duplicateComponent = (component) => {
  // Cria uma cópia profunda dos props
  const props = JSON.parse(JSON.stringify(component.props))
  
  // Garante que style e layout sejam strings
  props.style = typeof props.style === 'string' ? props.style : 'default'
  props.layout = typeof props.layout === 'string' ? props.layout : 'default'
  
  const newComponent = {
    id: uuidv4(),
    type: component.type,
    props: props,
    children: []
  }
  const index = pageComponents.value.findIndex(c => c.id === component.id)
  pageComponents.value.splice(index + 1, 0, newComponent)
}

const moveComponent = (component, direction) => {
  const index = pageComponents.value.findIndex(c => c.id === component.id)
  if (direction === 'up' && index > 0) {
    const temp = pageComponents.value[index - 1]
    pageComponents.value[index - 1] = component
    pageComponents.value[index] = temp
  } else if (direction === 'down' && index < pageComponents.value.length - 1) {
    const temp = pageComponents.value[index + 1]
    pageComponents.value[index + 1] = component
    pageComponents.value[index] = temp
  }
}

const isFirstComponent = (component) => {
  return pageComponents.value[0]?.id === component.id
}

const isLastComponent = (component) => {
  return pageComponents.value[pageComponents.value.length - 1]?.id === component.id
}

const updateComponentSettings = (componentId, newSettings, parentComponentId = null) => {
  console.log('PageBuilder - Atualizando configurações do componente:', {
    componentId,
    newSettings,
    parentComponentId
  })
  
  // Garantir que allowNesting seja um booleano
  const updatedSettings = {
    ...newSettings,
    allowNesting: newSettings.allowNesting === true
  }
  
  console.log('PageBuilder - Configurações atualizadas com allowNesting:', updatedSettings.allowNesting)
  
  // Cria uma cópia profunda dos componentes para evitar mutações diretas
  const updatedComponents = JSON.parse(JSON.stringify(pageComponents.value));
  
  // Se tiver um componente pai, atualiza o componente aninhado
  if (parentComponentId) {
    const parentIndex = updatedComponents.findIndex(c => c.id === parentComponentId);
    if (parentIndex !== -1) {
      // Garante que o componente pai tenha um array children
      if (!updatedComponents[parentIndex].children) {
        updatedComponents[parentIndex].children = [];
      }
      
      const childIndex = updatedComponents[parentIndex].children.findIndex(c => c.id === componentId);
      if (childIndex !== -1) {
        // Substitui completamente as props do componente filho
        updatedComponents[parentIndex].children[childIndex].props = updatedSettings;
        
        // Aplica a função ensureAllowNestingIsBoolean para garantir a consistência
        updatedComponents[parentIndex] = ensureAllowNestingIsBoolean(updatedComponents[parentIndex]);
        
        console.log('PageBuilder - Componente aninhado atualizado com allowNesting:', 
          updatedComponents[parentIndex].children[childIndex].props.allowNesting);
      }
    }
  } else {
    // Atualiza o componente principal
    const componentIndex = updatedComponents.findIndex(c => c.id === componentId);
    if (componentIndex !== -1) {
      // Substitui completamente as props do componente
      updatedComponents[componentIndex].props = updatedSettings;
      
      // Aplica a função ensureAllowNestingIsBoolean para garantir a consistência
      updatedComponents[componentIndex] = ensureAllowNestingIsBoolean(updatedComponents[componentIndex]);
      
      console.log('PageBuilder - Componente atualizado com allowNesting:', 
        updatedComponents[componentIndex].props.allowNesting);
    }
  }
  
  // Atualiza o estado e emite a atualização
  pageComponents.value = updatedComponents;
  
  // Força a atualização do v-model
  nextTick(() => {
    emit('update:modelValue', JSON.parse(JSON.stringify(updatedComponents)));
  });
}

const savePageStructure = async () => {
  try {
    console.log('PageBuilder - Salvando estrutura da página')
    isSaving.value = true
    saveSuccess.value = false
    
    // Garante que os componentes e estilos globais estão definidos como arrays/objetos vazios se não existirem
    const components = pageComponents.value || []
    const styles = globalStyles.value || {
      theme: 'light',
      spacing: 'normal',
      contentWidth: 'contained'
    }
    
    // Verifica se há componentes com categorias selecionadas
    components.forEach(component => {
      if (component.props && component.props.selectedCategories) {
        console.log('PageBuilder - Componente com categorias selecionadas:', {
          componentId: component.id,
          componentType: component.type,
          selectedCategories: component.props.selectedCategories
        })
      }
    })
    
    // Prepara os dados para envio
    const pageData = {
      config: {
        components: components,
        globalStyles: styles
      }
    }

    // Adiciona o token CSRF se existir
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    if (csrfToken) {
      pageData._token = csrfToken
    }
    
    // Salva os dados no servidor usando axios
    if (props.record && props.record.id) {
      console.log('PageBuilder - Salvando no servidor para o registro:', props.record.id)
      
      // Faz a requisição para o servidor
      const response = await axios.post(`/pages/${props.record.id}/save-builder`, pageData)
      
      console.log('PageBuilder - Resposta do servidor:', response.data)
      
      if (response.data.success) {
        saveSuccess.value = true
        console.log('PageBuilder - Estrutura da página salva com sucesso')
      } else {
        console.error('PageBuilder - Erro ao salvar estrutura da página:', response.data.message)
        alert('Erro ao salvar a página: ' + (response.data.message || 'Erro desconhecido'))
      }
    }
    
    // Reseta o estado de sucesso após 3 segundos
    setTimeout(() => {
      saveSuccess.value = false
    }, 3000)
  } catch (error) {
    console.error('Erro ao atualizar a página:', error)
    alert('Erro ao salvar a página: ' + (error.response?.data?.message || error.message || 'Erro desconhecido'))
  } finally {
    isSaving.value = false
  }
}

const previewPage = () => {
  // Implementar lógica de pré-visualização
  console.log('Pré-visualizando página')
}

// Computed
const getComponentByType = computed(() => (type) => {
  const components = {
    'header': HeaderBlock,
    'content': ContentBlock,
    'cards': CardBlock,
    'form': FormBlock,
    'benefits': BenefitsBlock,
    'simulator': SimulatorBlock,
    'contact-channels': ContactChannelsBlock
  }
  return components[type] || 'div'
})

const getDefaultProps = (type) => {
  const component = availableComponents.value.find(c => c.type === type)
  const defaultProps = component ? component.defaultProps : {}
  
  // Garantir que style seja uma string se existir
  if (defaultProps.style && typeof defaultProps.style !== 'string') {
    defaultProps.style = 'default';
  }
  
  // Adicionar propriedades padrão para o sistema de colunas e aninhamento
  return {
    ...defaultProps,
    columnSpan: 6,
    allowNesting: false,
    children: []
  }
}

// Configurações do draggable
const dragOptions = computed(() => ({
  animation: 200,
  group: { name: 'components', pull: 'clone', put: false },
  disabled: false,
  ghostClass: 'ghost'
}))

const dropOptions = computed(() => ({
  animation: 200,
  group: { name: 'components', put: true },
  disabled: false,
  ghostClass: 'ghost'
}))

const nestedDropOptions = computed(() => ({
  animation: 200,
  group: { name: 'components', put: true },
  disabled: false,
  ghostClass: 'ghost'
}))

const editNestedComponent = (parent, child) => {
  selectedComponent.value = child
  parentComponent.value = parent
  drawerOpen.value = true
}

const removeNestedComponent = (parent, child) => {
  if (!parent.children) return;
  
  // Cria uma cópia profunda do componente pai para evitar mutações diretas
  const updatedParent = JSON.parse(JSON.stringify(parent));
  
  // Encontra o índice do componente filho
  const index = updatedParent.children.findIndex(c => c.id === child.id);
  if (index !== -1) {
    // Remove o componente filho
    updatedParent.children.splice(index, 1);
    
    // Atualiza o componente pai no array principal
    const parentIndex = pageComponents.value.findIndex(c => c.id === updatedParent.id);
    if (parentIndex !== -1) {
      // Cria uma cópia profunda dos componentes para evitar mutações diretas
      const updatedComponents = JSON.parse(JSON.stringify(pageComponents.value));
      
      // Atualiza o componente pai com as alterações
      updatedComponents[parentIndex] = updatedParent;
      
      // Atualiza o estado
      pageComponents.value = updatedComponents;
      
      // Força a atualização do v-model
      nextTick(() => {
        emit('update:modelValue', JSON.parse(JSON.stringify(updatedComponents)));
      });
    }
  }
}

// Manipuladores de eventos para draggable
const handleMainDragAdd = (event) => {
  console.log('Componente adicionado ao container principal:', event);
  // Garantir que o componente adicionado tenha a propriedade children
  const addedComponent = pageComponents.value[event.newIndex];
  if (addedComponent) {
    // Inicializa as propriedades necessárias
    if (!addedComponent.props) {
      addedComponent.props = getDefaultProps(addedComponent.type);
    }
    // Sempre inicializa children como array vazio
    addedComponent.children = [];
    // Garantir que o columnSpan seja definido
    if (!addedComponent.props.columnSpan) {
      addedComponent.props.columnSpan = 6;
    }
    // Garantir que allowNesting seja definido como booleano
    addedComponent.props.allowNesting = true;
    
    // Garantir que style seja uma string se existir
    if (addedComponent.props.style !== undefined && typeof addedComponent.props.style !== 'string') {
      addedComponent.props.style = 'default';
      console.log('Convertendo style de objeto para string para componente adicionado');
    }
    
    console.log('Componente após inicialização com allowNesting:', addedComponent.props.allowNesting);
    
    // Força a atualização do v-model para garantir que as mudanças sejam persistidas
    nextTick(() => {
      emit('update:modelValue', JSON.parse(JSON.stringify(pageComponents.value)));
    });
    
    // Log para debug
    console.log('Componente após inicialização:', addedComponent);
  }
}

const handleNestedDragAdd = (parentComponent, event) => {
  console.log('Componente adicionado a um container aninhado:', event);
  
  try {
    // Cria uma cópia profunda do componente pai para evitar mutações diretas
    const updatedParent = JSON.parse(JSON.stringify(parentComponent));
    
    // Garantir que o componente pai tenha a propriedade children
    if (!updatedParent.children) {
      updatedParent.children = [];
    }
    
    // Garantir que o componente aninhado tenha as propriedades necessárias
    if (updatedParent.children[event.newIndex]) {
      const addedComponent = updatedParent.children[event.newIndex];
      
      // Inicializa props se não existir
      if (!addedComponent.props) {
        const componentType = addedComponent.type;
        const componentTemplate = availableComponents.value.find(c => c.type === componentType);
        if (componentTemplate) {
          addedComponent.props = cloneComponent(componentTemplate, true).props;
        } else {
          addedComponent.props = getDefaultProps(addedComponent.type);
        }
      }
      
      // Sempre inicializa children como array vazio
      addedComponent.children = [];
      
      // Define columnSpan padrão para componentes aninhados (metade da largura)
      addedComponent.props.columnSpan = 6;
      
      // Garante que allowNesting seja um booleano
      addedComponent.props.allowNesting = true;
      
      // Garantir que style seja uma string se existir
      if (addedComponent.props.style !== undefined && typeof addedComponent.props.style !== 'string') {
        addedComponent.props.style = 'default';
        console.log('Convertendo style de objeto para string para componente aninhado');
      }
      
      // Calcular posição com base no evento ou usar valores padrão
      const container = document.getElementById(`nest-container-${updatedParent.id}`);
      let x = 0;
      let y = 0;
      
      if (container && event.originalEvent) {
        const containerRect = container.getBoundingClientRect();
        x = event.originalEvent.clientX - containerRect.left;
        y = event.originalEvent.clientY - containerRect.top;
        
        // Garantir que as coordenadas estejam dentro do container
        x = Math.max(0, Math.min(x, containerRect.width - 200)); // 200 é a largura padrão
        y = Math.max(0, y);
        
        console.log('Posição calculada para componente aninhado:', { x, y, containerRect });
      }
      
      addedComponent.position = {
        x: x,
        y: y,
        width: 200,
        height: 'auto',
        zIndex: updatedParent.children.length
      };
      
      // Atualizar o componente pai no array principal
      const parentIndex = pageComponents.value.findIndex(c => c.id === updatedParent.id);
      if (parentIndex !== -1) {
        // Cria uma cópia profunda dos componentes para evitar mutações diretas
        const updatedComponents = JSON.parse(JSON.stringify(pageComponents.value));
        
        // Atualiza o componente pai com as alterações
        updatedComponents[parentIndex] = updatedParent;
        
        // Atualiza o estado
        pageComponents.value = updatedComponents;
        
        // Força a atualização do v-model
        nextTick(() => {
          emit('update:modelValue', JSON.parse(JSON.stringify(updatedComponents)));
        });
        
        console.log('Componente aninhado adicionado com sucesso via handleNestedDragAdd:', addedComponent);
      }
    }
  } catch (error) {
    console.error('Erro ao adicionar componente aninhado via handleNestedDragAdd:', error);
  }
}

// Função para atualizar os posts selecionados
const updateComponentPosts = (component, selectedPosts) => {
  if (!component.props) {
    component.props = {}
  }
  component.props.selectedPosts = selectedPosts
}

// Função para garantir que allowNesting seja sempre um booleano
const ensureAllowNestingIsBoolean = (component) => {
  if (!component) return component;
  
  // Cria uma cópia profunda do componente
  const componentCopy = JSON.parse(JSON.stringify(component));
  
  // Garante que props exista
  if (!componentCopy.props) {
    componentCopy.props = {};
  }
  
  // Garante que allowNesting seja um booleano, mas mantém o valor atual se já estiver definido
  if (componentCopy.props.allowNesting === undefined) {
    componentCopy.props.allowNesting = true;
  } else {
    componentCopy.props.allowNesting = componentCopy.props.allowNesting === true;
  }
  
  // Garante que style seja sempre uma string
  if (componentCopy.props.style !== undefined && typeof componentCopy.props.style !== 'string') {
    // Se for um objeto, converte para string 'default'
    componentCopy.props.style = 'default';
    console.log(`Convertendo style de objeto para string para componente ${componentCopy.id}`);
  }
  
  console.log(`Garantindo que allowNesting seja booleano para componente ${componentCopy.id}:`, componentCopy.props.allowNesting);
  
  // Garante que children sempre seja um array
  if (!componentCopy.children || !Array.isArray(componentCopy.children)) {
    componentCopy.children = [];
  }
  
  // Também processa componentes filhos
  if (componentCopy.children.length > 0) {
    componentCopy.children = componentCopy.children.map(child => ensureAllowNestingIsBoolean(child));
  }
  
  return componentCopy;
}

// Métodos para aninhamento flexível
const canNestComponent = (component) => {
  // Todos os componentes aceitam aninhamento por padrão
  return true
}

const isNestingTarget = (component) => {
  return nestingTarget.value && nestingTarget.value.id === component.id
}

const handleDragOver = (component, event) => {
  if (canNestComponent(component)) {
    event.preventDefault();
    
    // Se não temos um componente sendo arrastado, vamos tentar obter do dataTransfer
    if (!draggedComponent.value) {
      try {
        const componentData = event.dataTransfer.getData('application/json');
        if (componentData) {
          const parsedData = JSON.parse(componentData);
          console.log('Dados do componente arrastado obtidos do dataTransfer:', parsedData);
          
          // Se o componente arrastado for aninhado, precisamos encontrá-lo no componente pai
          if (parsedData.isNested) {
            // Tentar obter os dados do pai
            const parentData = event.dataTransfer.getData('parent-data');
            if (parentData) {
              const parsedParentData = JSON.parse(parentData);
              const parentId = parsedParentData.id;
              
              // Encontrar o componente pai
              const parentComponent = pageComponents.value.find(c => c.id === parentId);
              if (parentComponent && parentComponent.children) {
                // Encontrar o componente aninhado no pai
                const nestedComponent = parentComponent.children.find(c => c.id === parsedData.id);
                if (nestedComponent) {
                  draggedComponent.value = nestedComponent;
                  console.log('Componente aninhado encontrado no pai:', nestedComponent);
                }
              }
            } else {
              // Se não temos os dados do pai, procurar em todos os componentes
              for (const parentComp of pageComponents.value) {
                if (parentComp.children && parentComp.children.length > 0) {
                  const nestedComp = parentComp.children.find(c => c.id === parsedData.id);
                  if (nestedComp) {
                    draggedComponent.value = nestedComp;
                    console.log('Componente aninhado encontrado:', nestedComp);
                    break;
                  }
                }
              }
            }
          } else {
            // Encontrar o componente original na lista de componentes disponíveis ou no array principal
            const originalComponent = pageComponents.value.find(c => c.id === parsedData.id) || 
                                     availableComponents.value.find(c => c.type === parsedData.type);
            if (originalComponent) {
              draggedComponent.value = originalComponent;
              console.log('Componente arrastado definido a partir do dataTransfer:', draggedComponent.value);
            }
          }
        }
      } catch (error) {
        console.error('Erro ao obter dados do componente arrastado:', error);
      }
    }
    
    // Não permitir aninhamento dentro do próprio componente
    if (draggedComponent.value && draggedComponent.value.id === component.id) {
      console.log('Não é possível aninhar um componente dentro dele mesmo');
      return;
    }
    
    nestingTarget.value = component;
    isDragging.value = true;
    
    // Adicionar classe de destaque ao container de aninhamento
    const nestContainer = document.getElementById(`nest-container-${component.id}`);
    if (nestContainer) {
      nestContainer.classList.add('drag-over');
      console.log('Drag over na área de aninhamento do componente:', component.id);
    } else {
      console.log('Container de aninhamento não encontrado para o componente:', component.id);
    }
  }
}

const handleDragLeave = (component, event) => {
  if (nestingTarget.value && nestingTarget.value.id === component.id) {
    // Verificar se o cursor realmente saiu do elemento ou apenas entrou em um filho
    const rect = event.currentTarget.getBoundingClientRect();
    const isInside = (
      event.clientX >= rect.left &&
      event.clientX <= rect.right &&
      event.clientY >= rect.top &&
      event.clientY <= rect.bottom
    );
    
    if (!isInside) {
      console.log('Cursor saiu da área de aninhamento do componente:', component.id);
      
      // Remover classe de destaque do container de aninhamento
      const nestContainer = document.getElementById(`nest-container-${component.id}`);
      if (nestContainer) {
        nestContainer.classList.remove('drag-over');
        console.log('Drag leave da área de aninhamento do componente:', component.id);
      } else {
        console.log('Container de aninhamento não encontrado para o componente:', component.id);
      }
      
      // Não limpar o draggedComponent aqui para permitir que o componente seja arrastado para outro local
      // Apenas limpar o nestingTarget
      nestingTarget.value = null;
    }
  }
}

const handleDrop = (component, event) => {
  // Impedir a propagação do evento para evitar que o drop seja tratado múltiplas vezes
  event.preventDefault();
  event.stopPropagation();
  
  // Verificar se o drop já foi processado neste ciclo de eventos
  if (dropProcessed.value) {
    console.log('Drop já processado, ignorando');
    return;
  }
  
  console.log('handleDrop chamado para o componente:', component.id);
  console.log('draggedComponent:', draggedComponent.value);
  
  if (!canNestComponent(component) || !draggedComponent.value) {
    console.log('Não é possível aninhar o componente ou não há componente sendo arrastado');
    return;
  }
  
  // Marcar o drop como processado
  dropProcessed.value = true;
  
  // Resetar a flag após um curto período
  setTimeout(() => {
    dropProcessed.value = false;
  }, 100);
  
  // Obter a área de aninhamento (container) usando o ID único
  const nestContainer = document.getElementById(`nest-container-${component.id}`);
  if (!nestContainer) {
    console.log('Container de aninhamento não encontrado para o componente:', component.id);
    return;
  }
  
  // Remover classe de destaque
  nestContainer.classList.remove('drag-over');
  
  // Obter a posição relativa dentro do container de aninhamento
  const containerRect = nestContainer.getBoundingClientRect();
  
  // Calcular a posição relativa ao container
  let x = event.clientX - containerRect.left;
  let y = event.clientY - containerRect.top;
  
  // Verificar se o drop ocorreu dentro da área de aninhamento
  if (x < 0 || y < 0 || x > containerRect.width || y > containerRect.height) {
    console.log('Drop fora da área de aninhamento');
    nestingTarget.value = null;
    draggedComponent.value = null;
    isDragging.value = false;
    return;
  }
  
  // Garantir que o componente fique dentro dos limites do container
  x = Math.max(0, Math.min(x, containerRect.width - 200)); // 200 é a largura padrão
  y = Math.max(0, y);
  
  console.log('Drop dentro da área de aninhamento:', { x, y, containerRect });
  
  try {
    // Verificar se o componente arrastado já existe no array principal
    // Se existir, vamos movê-lo em vez de criar um novo
    let newComponent;
    let shouldRemoveOriginal = false;
    let existingIndex = -1;
    const draggedId = draggedComponent.value.id;
    
    // Verificar se o componente arrastado já existe no array principal
    existingIndex = pageComponents.value.findIndex(c => c.id === draggedId);
    
    // Verificar se o componente está em algum componente aninhado
    let parentOfNested = null;
    let nestedIndex = -1;
    
    if (existingIndex === -1) {
      // Procurar em todos os componentes aninhados
      for (const parentComp of pageComponents.value) {
        if (parentComp.children && parentComp.children.length > 0) {
          const index = parentComp.children.findIndex(c => c.id === draggedId);
          if (index !== -1) {
            parentOfNested = parentComp;
            nestedIndex = index;
            break;
          }
        }
      }
    }
    
    // Verificar se o componente está sendo arrastado da barra lateral
    // Isso é indicado pela ausência do componente no array principal e em qualquer componente aninhado
    const isFromSidebar = existingIndex === -1 && !parentOfNested;
    
    if (existingIndex !== -1) {
      // É um componente existente no array principal, vamos movê-lo
      console.log('Movendo componente existente do array principal:', draggedComponent.value);
      newComponent = JSON.parse(JSON.stringify(pageComponents.value[existingIndex]));
      shouldRemoveOriginal = true;
    } else if (parentOfNested && nestedIndex !== -1) {
      // É um componente aninhado em outro componente, vamos movê-lo
      console.log('Movendo componente aninhado de outro componente:', draggedComponent.value);
      newComponent = JSON.parse(JSON.stringify(parentOfNested.children[nestedIndex]));
      
      // Remover o componente do pai original
      const updatedParent = JSON.parse(JSON.stringify(parentOfNested));
      updatedParent.children.splice(nestedIndex, 1);
      
      // Atualizar o pai original no array principal
      const parentIndex = pageComponents.value.findIndex(c => c.id === parentOfNested.id);
      if (parentIndex !== -1) {
        const updatedComponents = JSON.parse(JSON.stringify(pageComponents.value));
        updatedComponents[parentIndex] = updatedParent;
        pageComponents.value = updatedComponents;
      }
    } else {
      // É um novo componente da barra lateral, vamos criar um clone
      console.log('Criando novo componente da barra lateral:', draggedComponent.value);
      newComponent = cloneComponent(draggedComponent.value, true);
      
      // Se o componente foi arrastado da barra lateral, precisamos verificar se ele foi adicionado
      // automaticamente ao array principal pelo draggable e removê-lo para evitar duplicação
      if (isFromSidebar) {
        // Procurar o componente recém-adicionado no array principal com o mesmo ID
        const newComponentIndex = pageComponents.value.findIndex(c => c.id === newComponent.id);
        if (newComponentIndex !== -1) {
          console.log('Removendo componente duplicado do array principal:', newComponent.id);
          shouldRemoveOriginal = true;
          existingIndex = newComponentIndex;
        }
      }
    }
    
    // Adicionar posição ao componente
    newComponent.position = {
      x: x,
      y: y,
      width: 200,
      height: 'auto',
      zIndex: component.children?.length + 1 || 1
    };
    
    // Criar uma cópia profunda do componente pai para evitar mutações diretas
    const updatedComponent = JSON.parse(JSON.stringify(component));
    
    // Adicionar o componente como filho
    if (!updatedComponent.children) {
      updatedComponent.children = [];
    }
    
    // Adicionar o novo componente à lista de filhos
    updatedComponent.children.push(newComponent);
    
    // Atualizar o componente pai no array principal
    const parentIndex = pageComponents.value.findIndex(c => c.id === updatedComponent.id);
    if (parentIndex !== -1) {
      // Cria uma cópia profunda dos componentes para evitar mutações diretas
      const updatedComponents = JSON.parse(JSON.stringify(pageComponents.value));
      
      // Atualiza o componente pai com as alterações
      updatedComponents[parentIndex] = updatedComponent;
      
      // Se estamos movendo um componente existente do array principal, removê-lo
      if (shouldRemoveOriginal && existingIndex !== -1) {
        updatedComponents.splice(existingIndex, 1);
      }
      
      // Atualiza o estado
      pageComponents.value = updatedComponents;
      
      // Força a atualização do v-model
      nextTick(() => {
        emit('update:modelValue', JSON.parse(JSON.stringify(updatedComponents)));
      });
      
      console.log('Componente aninhado adicionado com sucesso via handleDrop:', newComponent);
    }
    
    // Limpar o estado
    nestingTarget.value = null;
    draggedComponent.value = null;
    isDragging.value = false;
  } catch (error) {
    console.error('Erro ao adicionar componente aninhado via handleDrop:', error);
    // Limpar o estado mesmo em caso de erro
    nestingTarget.value = null;
    draggedComponent.value = null;
    isDragging.value = false;
  }
}

const startDrag = (event, parent, child) => {
  console.log('Iniciando arrasto manual do componente aninhado:', child.id);
  
  // Evitar iniciar o drag se estiver clicando em um botão ou alça de redimensionamento
  if (
    event.target.closest('button') || 
    event.target.classList.contains('resize-handle')
  ) {
    return;
  }
  
  // Impedir o comportamento padrão de drag and drop do navegador
  event.preventDefault();
  
  // Armazenar o elemento sendo arrastado e seu pai
  currentDragParent.value = parent;
  currentDragElement.value = child;
  
  // Obter a posição inicial do mouse
  dragStartX.value = event.clientX;
  dragStartY.value = event.clientY;
  
  // Obter a posição inicial do componente
  dragStartPosX.value = child.position?.x || 0;
  dragStartPosY.value = child.position?.y || 0;
  
  // Obter o container de aninhamento
  const nestContainer = document.getElementById(`nest-container-${parent.id}`);
  if (!nestContainer) {
    console.log('Container de aninhamento não encontrado para o componente:', parent.id);
    return;
  }
  
  // Adicionar event listeners para mousemove e mouseup
  document.addEventListener('mousemove', handleDragMove);
  document.addEventListener('mouseup', handleDragEnd);
}

const handleDragMove = (event) => {
  if (!currentDragElement.value || !currentDragParent.value) return;
  
  // Calcular o deslocamento do mouse
  const deltaX = event.clientX - dragStartX.value;
  const deltaY = event.clientY - dragStartY.value;
  
  // Calcular a nova posição
  let newX = dragStartPosX.value + deltaX;
  let newY = dragStartPosY.value + deltaY;
  
  // Obter o container de aninhamento
  const nestContainer = document.getElementById(`nest-container-${currentDragParent.value.id}`);
  if (!nestContainer) return;
  
  // Obter as dimensões do container
  const containerRect = nestContainer.getBoundingClientRect();
  
  // Garantir que o componente fique dentro dos limites do container
  newX = Math.max(0, Math.min(newX, containerRect.width - 200)); // 200 é a largura padrão
  newY = Math.max(0, newY);
  
  // Atualizar a posição do elemento visualmente durante o arrasto
  const element = document.querySelector(`[data-child-id="${currentDragElement.value.id}"][data-parent-id="${currentDragParent.value.id}"]`);
  if (element) {
    element.style.left = `${newX}px`;
    element.style.top = `${newY}px`;
  }
}

const handleDragEnd = (event) => {
  if (!currentDragElement.value || !currentDragParent.value) return;
  
  // Remover os event listeners
  document.removeEventListener('mousemove', handleDragMove);
  document.removeEventListener('mouseup', handleDragEnd);
  
  // Calcular o deslocamento final do mouse
  const deltaX = event.clientX - dragStartX.value;
  const deltaY = event.clientY - dragStartY.value;
  
  // Calcular a nova posição
  let newX = dragStartPosX.value + deltaX;
  let newY = dragStartPosY.value + deltaY;
  
  // Obter o container de aninhamento
  const nestContainer = document.getElementById(`nest-container-${currentDragParent.value.id}`);
  if (!nestContainer) return;
  
  // Obter as dimensões do container
  const containerRect = nestContainer.getBoundingClientRect();
  
  // Garantir que o componente fique dentro dos limites do container
  newX = Math.max(0, Math.min(newX, containerRect.width - 200)); // 200 é a largura padrão
  newY = Math.max(0, newY);
  
  // Atualizar a posição do componente no estado
  moveNestedComponent(currentDragParent.value, currentDragElement.value, { x: newX, y: newY });
  
  // Limpar o estado
  currentDragParent.value = null;
  currentDragElement.value = null;
}

const startResize = (event, parent, child, direction) => {
  event.preventDefault()
  event.stopPropagation()
  
  // Armazenar o elemento sendo redimensionado e seu pai
  currentDragParent.value = parent
  currentDragElement.value = child
  
  // Armazenar a direção do redimensionamento
  resizeDirection.value = direction
  
  // Armazenar a posição inicial do mouse
  resizeStartX.value = event.clientX
  resizeStartY.value = event.clientY
  
  // Armazenar as dimensões iniciais do elemento
  resizeStartWidth.value = child.position?.width || 200
  resizeStartHeight.value = child.position?.height || 'auto'
  
  // Indicar que estamos redimensionando
  isResizing.value = true
  
  // Adicionar event listeners para o movimento e fim do redimensionamento
  document.addEventListener('mousemove', handleResizeMove)
  document.addEventListener('mouseup', handleResizeEnd)
}

const handleResizeMove = (event) => {
  if (!isResizing.value || !currentDragElement.value || !currentDragParent.value) return
  
  // Obter a posição atual do mouse
  const currentX = event.clientX
  const currentY = event.clientY
  
  // Calcular a diferença
  const deltaX = currentX - resizeStartX.value
  const deltaY = currentY - resizeStartY.value
  
  // Calcular as novas dimensões com base na direção
  let newWidth = resizeStartWidth.value
  let newHeight = resizeStartHeight.value
  
  // Ajustar largura e altura com base na direção
  if (resizeDirection.value.includes('e')) {
    newWidth = Math.max(100, resizeStartWidth.value + deltaX)
  } else if (resizeDirection.value.includes('w')) {
    newWidth = Math.max(100, resizeStartWidth.value - deltaX)
  }
  
  if (resizeDirection.value.includes('s')) {
    newHeight = Math.max(50, resizeStartHeight.value === 'auto' ? 100 : resizeStartHeight.value + deltaY)
  } else if (resizeDirection.value.includes('n')) {
    newHeight = Math.max(50, resizeStartHeight.value === 'auto' ? 100 : resizeStartHeight.value - deltaY)
  }
  
  // Atualizar as dimensões do elemento
  const updatedComponents = JSON.parse(JSON.stringify(pageComponents.value))
  const parentIndex = updatedComponents.findIndex(c => c.id === currentDragParent.value.id)
  
  if (parentIndex !== -1) {
    const childIndex = updatedComponents[parentIndex].children.findIndex(c => c.id === currentDragElement.value.id)
    
    if (childIndex !== -1) {
      if (!updatedComponents[parentIndex].children[childIndex].position) {
        updatedComponents[parentIndex].children[childIndex].position = {
          x: 0,
          y: 0,
          zIndex: childIndex + 1
        }
      }
      
      updatedComponents[parentIndex].children[childIndex].position.width = newWidth
      updatedComponents[parentIndex].children[childIndex].position.height = newHeight
      
      // Atualizar a posição se necessário (para n e w)
      if (resizeDirection.value.includes('w')) {
        const deltaPosition = resizeStartWidth.value - newWidth
        updatedComponents[parentIndex].children[childIndex].position.x += deltaPosition
      }
      
      if (resizeDirection.value.includes('n')) {
        const deltaPosition = resizeStartHeight.value === 'auto' ? 0 : resizeStartHeight.value - newHeight
        updatedComponents[parentIndex].children[childIndex].position.y += deltaPosition
      }
      
      // Atualizar o estado
      pageComponents.value = updatedComponents
    }
  }
}

const handleResizeEnd = () => {
  // Remover event listeners
  document.removeEventListener('mousemove', handleResizeMove)
  document.removeEventListener('mouseup', handleResizeEnd)
  
  // Resetar estado
  isResizing.value = false
  resizeDirection.value = null
  currentDragParent.value = null
  currentDragElement.value = null
  
  // Atualizar o modelo
  emit('update:modelValue', JSON.parse(JSON.stringify(pageComponents.value)))
}

const bringToFront = (parent, child) => {
  // Obter o maior z-index atual
  const maxZIndex = parent.children.reduce((max, c) => {
    return Math.max(max, c.position?.zIndex || 0)
  }, 0)
  
  // Atualizar o z-index do elemento
  const updatedComponents = JSON.parse(JSON.stringify(pageComponents.value))
  const parentIndex = updatedComponents.findIndex(c => c.id === parent.id)
  
  if (parentIndex !== -1) {
    const childIndex = updatedComponents[parentIndex].children.findIndex(c => c.id === child.id)
    
    if (childIndex !== -1) {
      if (!updatedComponents[parentIndex].children[childIndex].position) {
        updatedComponents[parentIndex].children[childIndex].position = {
          x: 0,
          y: 0,
          width: 200,
          height: 'auto'
        }
      }
      
      updatedComponents[parentIndex].children[childIndex].position.zIndex = maxZIndex + 1
      
      // Atualizar o estado
      pageComponents.value = updatedComponents
      
      // Atualizar o modelo
      emit('update:modelValue', JSON.parse(JSON.stringify(updatedComponents)))
    }
  }
}

const sendToBack = (parent, child) => {
  // Obter o menor z-index atual
  const minZIndex = parent.children.reduce((min, c) => {
    return Math.min(min, c.position?.zIndex || 0)
  }, Infinity)
  
  // Atualizar o z-index do elemento
  const updatedComponents = JSON.parse(JSON.stringify(pageComponents.value))
  const parentIndex = updatedComponents.findIndex(c => c.id === parent.id)
  
  if (parentIndex !== -1) {
    const childIndex = updatedComponents[parentIndex].children.findIndex(c => c.id === child.id)
    
    if (childIndex !== -1) {
      if (!updatedComponents[parentIndex].children[childIndex].position) {
        updatedComponents[parentIndex].children[childIndex].position = {
          x: 0,
          y: 0,
          width: 200,
          height: 'auto'
        }
      }
      
      updatedComponents[parentIndex].children[childIndex].position.zIndex = Math.max(0, minZIndex - 1)
      
      // Atualizar o estado
      pageComponents.value = updatedComponents
      
      // Atualizar o modelo
      emit('update:modelValue', JSON.parse(JSON.stringify(updatedComponents)))
    }
  }
}

const unnestComponent = (parent, child) => {
  // Remover o componente do pai
  const updatedComponents = JSON.parse(JSON.stringify(pageComponents.value))
  const parentIndex = updatedComponents.findIndex(c => c.id === parent.id)
  
  if (parentIndex !== -1) {
    const childIndex = updatedComponents[parentIndex].children.findIndex(c => c.id === child.id)
    
    if (childIndex !== -1) {
      // Remover o componente filho
      const removedChild = updatedComponents[parentIndex].children.splice(childIndex, 1)[0]
      
      // Remover a propriedade position
      delete removedChild.position
      
      // Adicionar o componente ao nível principal
      updatedComponents.splice(parentIndex + 1, 0, removedChild)
      
      // Atualizar o estado
      pageComponents.value = updatedComponents
      
      // Atualizar o modelo
      emit('update:modelValue', JSON.parse(JSON.stringify(updatedComponents)))
    }
  }
}

const handleDragStart = (component, event) => {
  console.log('Iniciando arrasto do componente:', component.id);
  
  // Definir o componente arrastado
  draggedComponent.value = component;
  isDragging.value = true;
  
  // Verificar se o componente está sendo arrastado de uma área aninhada
  const isNestedComponent = !!component.position;
  
  // Armazenar dados do componente no dataTransfer para uso posterior
  try {
    const componentData = {
      id: component.id,
      type: component.type,
      isNested: isNestedComponent
    };
    
    // Armazenar os dados do componente no dataTransfer
    event.dataTransfer.setData('application/json', JSON.stringify(componentData));
    event.dataTransfer.effectAllowed = 'move';
    
    // Se for um componente aninhado, precisamos encontrar o componente pai
    if (isNestedComponent) {
      // Encontrar o elemento pai usando os atributos data-
      const element = event.currentTarget;
      const parentId = element.getAttribute('data-parent-id');
      
      if (parentId) {
        // Armazenar o ID do pai no dataTransfer
        const parentData = {
          id: parentId
        };
        event.dataTransfer.setData('parent-data', JSON.stringify(parentData));
        console.log('Componente aninhado sendo arrastado, pai:', parentId);
      }
    }
    
    // Definir uma imagem de arrasto personalizada para melhorar o feedback visual
    const dragImage = document.createElement('div');
    dragImage.textContent = component.type;
    dragImage.style.padding = '8px';
    dragImage.style.background = '#3b82f6';
    dragImage.style.color = 'white';
    dragImage.style.borderRadius = '4px';
    dragImage.style.fontSize = '12px';
    dragImage.style.position = 'absolute';
    dragImage.style.top = '-1000px';
    document.body.appendChild(dragImage);
    
    event.dataTransfer.setDragImage(dragImage, 0, 0);
    
    // Remover o elemento após um curto período
    setTimeout(() => {
      document.body.removeChild(dragImage);
    }, 100);
  } catch (error) {
    console.error('Erro ao definir dados do dataTransfer:', error);
  }
}

const moveNestedComponent = (parentComponent, childComponent, newPosition) => {
  console.log('Movendo componente aninhado:', childComponent.id, 'para posição:', newPosition);
  
  try {
    // Criar uma cópia profunda do componente pai para evitar mutações diretas
    const updatedParent = JSON.parse(JSON.stringify(parentComponent));
    
    // Encontrar o componente filho
    const childIndex = updatedParent.children.findIndex(c => c.id === childComponent.id);
    if (childIndex !== -1) {
      // Atualizar a posição do componente filho
      updatedParent.children[childIndex].position = {
        ...updatedParent.children[childIndex].position,
        x: newPosition.x,
        y: newPosition.y
      };
      
      // Atualizar o componente pai no array principal
      const parentIndex = pageComponents.value.findIndex(c => c.id === updatedParent.id);
      if (parentIndex !== -1) {
        // Criar uma cópia profunda dos componentes para evitar mutações diretas
        const updatedComponents = JSON.parse(JSON.stringify(pageComponents.value));
        
        // Atualizar o componente pai com as alterações
        updatedComponents[parentIndex] = updatedParent;
        
        // Atualizar o estado
        pageComponents.value = updatedComponents;
        
        // Forçar a atualização do v-model
        nextTick(() => {
          emit('update:modelValue', JSON.parse(JSON.stringify(updatedComponents)));
        });
        
        console.log('Componente aninhado movido com sucesso:', childComponent.id);
      }
    }
  } catch (error) {
    console.error('Erro ao mover componente aninhado:', error);
  }
}
</script>

<style scoped>
.page-builder {
  @apply min-h-screen bg-white dark:bg-gray-800 w-full;
}

.ghost {
  @apply opacity-50 bg-blue-100 dark:bg-gray-800;
}

/* Sobrescrevendo estilos do draggable */
:deep(.sortable-drag) {
  @apply bg-white dark:bg-gray-800 !important;
}

/* Estilos para aninhamento flexível */
.nest-highlight {
  @apply ring-2 ring-blue-500 ring-opacity-70 transition-all duration-200;
}

.nest-dropzone-active {
  @apply bg-blue-50 dark:bg-blue-900/20 transition-colors duration-200;
}

/* Destaque para a área de aninhamento durante o drag */
[id^="nest-container-"].drag-over {
  @apply border-blue-500 border-2 bg-blue-50 dark:bg-blue-900/20;
  box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.3);
  animation: pulse 1.5s infinite;
}

@keyframes pulse {
  0% {
    box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.5);
  }
  70% {
    box-shadow: 0 0 0 10px rgba(59, 130, 246, 0);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(59, 130, 246, 0);
  }
}

/* Alças de redimensionamento */
.resize-handle {
  @apply absolute w-3 h-3 bg-blue-500 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-200 z-50;
}

.resize-handle-se {
  @apply bottom-0 right-0 cursor-se-resize;
}

.resize-handle-sw {
  @apply bottom-0 left-0 cursor-sw-resize;
}

.resize-handle-ne {
  @apply top-0 right-0 cursor-ne-resize;
}

.resize-handle-nw {
  @apply top-0 left-0 cursor-nw-resize;
}

.resize-handle-n {
  @apply top-0 left-1/2 -translate-x-1/2 cursor-n-resize;
}

.resize-handle-e {
  @apply top-1/2 right-0 -translate-y-1/2 cursor-e-resize;
}

.resize-handle-s {
  @apply bottom-0 left-1/2 -translate-x-1/2 cursor-s-resize;
}

.resize-handle-w {
  @apply top-1/2 left-0 -translate-y-1/2 cursor-w-resize;
}

/* Estilo para componentes aninhados */
.nested-component {
  @apply relative border border-dashed border-blue-300 dark:border-blue-700 rounded-lg transition-all duration-200;
}

.nested-component:hover {
  @apply border-blue-500 dark:border-blue-500;
}

/* Indicador de hierarquia */
.hierarchy-indicator {
  @apply absolute top-0 left-0 w-full h-full pointer-events-none;
  background: repeating-linear-gradient(
    45deg,
    rgba(59, 130, 246, 0.05),
    rgba(59, 130, 246, 0.05) 10px,
    transparent 10px,
    transparent 20px
  );
}

/* Área de aninhamento */
.nest-container {
  min-height: 200px;
  position: relative;
  border: 2px dashed #e5e7eb;
  border-radius: 0.5rem;
  padding: 1rem;
  transition: all 0.3s ease;
}

.dark .nest-container {
  border-color: #4b5563;
}

.nest-container.drag-over {
  background-color: rgba(59, 130, 246, 0.1);
  border-color: #3b82f6;
}

.dark .nest-container.drag-over {
  background-color: rgba(59, 130, 246, 0.2);
  border-color: #60a5fa;
}

/* Estilos para componentes aninhados */
.nested-component {
  cursor: move;
  min-width: 100px;
  min-height: 50px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  transition: all 0.2s ease;
}

.nested-component:hover {
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  transform: translateY(-2px);
}
</style>