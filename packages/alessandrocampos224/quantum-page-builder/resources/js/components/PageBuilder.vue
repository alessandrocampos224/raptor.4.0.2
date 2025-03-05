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
                    <component :is="element.icon" class="w-5 h-5 mr-2 text-primary-500" />
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
                  `col-span-${element.props?.columnSpan || 12}`
                ]"
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
                    <component :is="element.icon" class="w-5 h-5 text-primary-500" />
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

                <!-- Área para componentes aninhados -->
                <div v-if="element.props?.allowNesting === true" class="p-4 border-t border-gray-200 dark:border-gray-700">
                  <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">Componentes Aninhados</h4>
                  
                  <draggable
                    v-model="element.children"
                    v-bind="nestedDropOptions"
                    item-key="id"
                    @add="(event) => handleNestedDragAdd(element, event)"
                    class="min-h-[100px] border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-lg p-4 grid grid-cols-12 gap-4"
                  >
                    <template #header v-if="!element.children || !element.children.length">
                      <div class="flex flex-col items-center justify-center min-h-[100px] col-span-12">
                        <p class="text-sm text-gray-500 dark:text-gray-400 text-center">Arraste componentes para esta área</p>
                      </div>
                    </template>
                    
                    <template #item="{ element: childElement }">
                      <div 
                        :class="[
                          'relative group bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all duration-200',
                          `col-span-${childElement.props?.columnSpan || 12}`
                        ]"
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
                      </div>
                    </template>
                  </draggable>
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
        
        <!-- Conteúdo do painel com scroll -->
        <div class="flex-1 overflow-y-auto p-4 bg-white dark:bg-gray-800 relative">
          <component-settings
            v-if="selectedComponent"
            :component="selectedComponent"
            @update="updateComponentSettings"
            class="relative z-[52]"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, nextTick, onMounted, onUnmounted } from 'vue'
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
  Clock
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
})

onUnmounted(() => {
  window.removeEventListener('keydown', (e) => {
    if (e.key === 'Escape' && drawerOpen.value) {
      closeDrawer()
    }
  })
})

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
  
  // Cria uma cópia profunda dos defaultProps
  const props = JSON.parse(JSON.stringify(component.defaultProps));
  
  // Garante que as propriedades básicas existam
  props.style = props.style || 'default';
  props.layout = props.layout || 'default';
  props.spacingVertical = props.spacingVertical || 'default';
  props.spacingHorizontal = props.spacingHorizontal || 'default';
  
  // Define o columnSpan padrão com base se é um componente aninhado ou não
  props.columnSpan = isNested ? 6 : 6;
  props.allowNesting = props.allowNesting === true;
  
  console.log('PageBuilder - Props do componente clonado com allowNesting:', props.allowNesting);
  
  // Retorna um novo componente com os props mapeados
  const newComponent = {
      id: uuidv4(),
      type: component.type,
      props: props,
      children: []
  };
  
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
  // Atualiza o componente original com as alterações antes de fechar
  if (selectedComponent.value) {
    console.log('PageBuilder - Fechando drawer, componente selecionado:', selectedComponent.value)
    
    // Garante que allowNesting seja um booleano
    const updatedComponent = ensureAllowNestingIsBoolean(selectedComponent.value);
    
    // Encontra o componente no array principal
    const index = pageComponents.value.findIndex(c => c.id === updatedComponent.id)
    if (index !== -1) {
      console.log('PageBuilder - Atualizando componente antes de fechar:', updatedComponent)
      
      // Atualiza o componente no array
      pageComponents.value[index] = updatedComponent
      
      // Força a emissão da atualização
      nextTick(() => {
        emit('update:modelValue', JSON.parse(JSON.stringify(pageComponents.value)))
      })
    } else if (parentComponent.value) {
      // Verifica se é um componente aninhado
      const parentIndex = pageComponents.value.findIndex(c => c.id === parentComponent.value.id)
      if (parentIndex !== -1 && pageComponents.value[parentIndex].children) {
        const childIndex = pageComponents.value[parentIndex].children.findIndex(c => c.id === updatedComponent.id)
        if (childIndex !== -1) {
          console.log('PageBuilder - Atualizando componente aninhado antes de fechar:', updatedComponent)
          
          // Atualiza o componente aninhado no array
          pageComponents.value[parentIndex].children[childIndex] = updatedComponent
          
          // Força a emissão da atualização
          nextTick(() => {
            emit('update:modelValue', JSON.parse(JSON.stringify(pageComponents.value)))
          })
        }
      }
    }
  }
  
  drawerOpen.value = false
  selectedComponent.value = null
  parentComponent.value = null
  
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
  
  // Se tiver um componente pai, atualiza o componente aninhado
  if (parentComponentId) {
    const parentComponent = pageComponents.value.find(c => c.id === parentComponentId)
    if (parentComponent && parentComponent.children) {
      const childIndex = parentComponent.children.findIndex(c => c.id === componentId)
      if (childIndex !== -1) {
        const childComponent = parentComponent.children[childIndex]
        console.log('PageBuilder - Componente aninhado encontrado:', childComponent)
        
        // Atualiza as props mantendo o allowNesting
        childComponent.props = {
          ...childComponent.props,
          ...updatedSettings
        }
        
        // Garante que o componente tenha allowNesting como booleano
        const updatedChild = ensureAllowNestingIsBoolean(childComponent);
        parentComponent.children[childIndex] = updatedChild;
        
        console.log('PageBuilder - Componente aninhado atualizado com allowNesting:', updatedChild.props.allowNesting)
        
        // Força a atualização do v-model
        nextTick(() => {
          emit('update:modelValue', JSON.parse(JSON.stringify(pageComponents.value)))
        })
      }
    }
  } else {
    // Atualiza o componente principal
    const componentIndex = pageComponents.value.findIndex(c => c.id === componentId)
    if (componentIndex !== -1) {
      const component = pageComponents.value[componentIndex]
      console.log('PageBuilder - Componente encontrado:', component)
      
      // Atualiza as props mantendo o allowNesting
      component.props = {
        ...component.props,
        ...updatedSettings
      }
      
      // Garante que o componente tenha allowNesting como booleano
      const updatedComponent = ensureAllowNestingIsBoolean(component);
      pageComponents.value[componentIndex] = updatedComponent;
      
      console.log('PageBuilder - Componente atualizado com allowNesting:', updatedComponent.props.allowNesting)
      
      // Força a atualização do v-model
      nextTick(() => {
        emit('update:modelValue', JSON.parse(JSON.stringify(pageComponents.value)))
      })
    }
  }
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
  if (!parent.children) return
  
  const index = parent.children.findIndex(c => c.id === child.id)
  if (index !== -1) {
    parent.children.splice(index, 1)
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
    addedComponent.props.allowNesting = addedComponent.props.allowNesting === true;
    
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
  
  // Garantir que o componente pai tenha a propriedade children
  if (!parentComponent.children) {
    parentComponent.children = [];
  }
  
  // Garantir que o componente aninhado tenha as propriedades necessárias
  const addedComponent = parentComponent.children[event.newIndex];
  if (addedComponent) {
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
    
    // Garante que allowNesting seja um booleano usando nossa função
    const updatedComponent = ensureAllowNestingIsBoolean(addedComponent);
    parentComponent.children[event.newIndex] = updatedComponent;
    
    // Força a atualização do v-model
    nextTick(() => {
      emit('update:modelValue', JSON.parse(JSON.stringify(pageComponents.value)));
    });
    
    // Log para debug
    console.log('Componente aninhado após inicialização:', updatedComponent);
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
  
  // Garante que allowNesting seja um booleano
  if (componentCopy.props) {
    // Força o valor para true ou false explicitamente
    componentCopy.props.allowNesting = componentCopy.props.allowNesting === true;
    console.log(`Garantindo que allowNesting seja booleano para componente ${componentCopy.id}:`, componentCopy.props.allowNesting);
  }
  
  // Também processa componentes filhos
  if (componentCopy.children && Array.isArray(componentCopy.children)) {
    componentCopy.children = componentCopy.children.map(child => ensureAllowNestingIsBoolean(child));
  }
  
  return componentCopy;
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

:deep(.sortable-ghost) {
  @apply bg-white dark:bg-gray-800 opacity-50 !important;
}

/* Remove as barras diagonais do grid */
:deep(.grid) {
  background-image: none !important;
}

:deep(.grid > *) {
  background-image: none !important;
}

:deep(.grid::before),
:deep(.grid::after) {
  display: none !important;
}

/* Ajuste da área principal */
.page-builder .flex-1 {
  @apply transition-all duration-300;
  flex: 1 1 auto;
  min-width: 0;
}

/* Ajustes para os selects no painel de configuração */
:deep(select) {
  z-index: 60 !important;
  position: relative;
}

:deep(.select-dropdown) {
  z-index: 60 !important;
}

:deep(.select-options) {
  z-index: 60 !important;
}
</style>