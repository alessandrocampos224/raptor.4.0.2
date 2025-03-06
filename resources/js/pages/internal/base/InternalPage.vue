<template>
  <div class="internal-page">
    <div v-if="pageComponents.length === 0" class="p-8 text-center">
      <h2 class="text-2xl font-bold mb-4">Carregando componentes da página...</h2>
      <p>Se esta mensagem persistir, pode haver um problema com os dados da página.</p>
    </div>
    <!-- Renderização dos componentes da página -->
    <div v-else :class="[
      'grid grid-cols-12 gap-4',
      pageGlobalStyles.theme === 'dark' ? 'dark-theme' : '',
      `gap-y-${pageGlobalStyles.spacing === 'compact' ? '12' : pageGlobalStyles.spacing === 'normal' ? '20' : '28'}`,
      pageGlobalStyles.contentWidth === 'contained' ? 'max-w-7xl mx-auto' : 
      pageGlobalStyles.contentWidth === 'narrow' ? 'max-w-4xl mx-auto' : 'w-full'
    ]">
      <template v-for="(component, index) in pageComponents" :key="index">
        <div :class="[
          `col-span-${component.props?.columnSpan || 12}`,
          'w-full'
        ]">
          <component
            :is="getComponentByType(component.type)"
            v-bind="{ ...component.props || {}, globalTheme: pageGlobalStyles.theme }"
            class="w-full"
          >
            <!-- Renderização de componentes aninhados -->
            <template v-if="component.children && component.children.length > 0">
              <div class="nested-components">
                <template v-for="(childComponent, childIndex) in component.children" :key="`${index}-${childIndex}`">
                  <div :class="`col-span-${childComponent.props?.columnSpan || 12}`">
                    <component
                      :is="getComponentByType(childComponent.type)"
                      v-bind="{ ...childComponent.props || {}, globalTheme: pageGlobalStyles.theme }"
                      class="w-full mb-4"
                    />
                  </div>
                </template>
              </div>
            </template>
          </component>
        </div>
      </template>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { usePage } from '@inertiajs/vue3'

// Importar os componentes de blocos
import HeaderBlock from '@quantum/page-builder/components/Blocks/HeaderBlock.vue'
import ContentBlock from '@quantum/page-builder/components/Blocks/ContentBlock.vue'
import CardBlock from '@quantum/page-builder/components/Blocks/CardBlock.vue'
import FormBlock from '@quantum/page-builder/components/Blocks/FormBlock.vue'
import BenefitsBlock from '@quantum/page-builder/components/Blocks/BenefitsBlock.vue'
import SimulatorBlock from '@quantum/page-builder/components/Blocks/SimulatorBlock.vue'
import ContactChannelsBlock from '@quantum/page-builder/components/Blocks/ContactChannelsBlock.vue'

// Props
const props = defineProps({
  pageData: {
    type: Object,
    default: () => ({})
  }
})

// Estado da página
const pageComponents = ref([])
const pageGlobalStyles = ref({
  theme: 'light',
  spacing: 'normal',
  contentWidth: 'contained'
})

// Obter dados da página atual
const page = usePage()
const pageSlug = computed(() => {
  return page.props.path || ''
})

// Carregar dados da página
const loadPageData = async () => {
  try {
    // Se os dados da página foram passados como prop, use-os
    if (props.pageData) {
      console.log('Dados da página recebidos via props:', props.pageData)
      
      // Se não houver componentes definidos, criar um componente de conteúdo padrão
      if (!props.pageData.components || props.pageData.components.length === 0) {
        console.log('Criando componente de conteúdo padrão')
        pageComponents.value = [{
          type: 'content',
          props: {
            title: props.pageData.title || 'Título da Página',
            content: 'Esta página ainda não possui conteúdo configurado. Use o construtor de páginas para adicionar componentes.',
            columnSpan: 12
          }
        }]
      } else {
        pageComponents.value = props.pageData.components
      }
      
      pageGlobalStyles.value = props.pageData.globalStyles || {
        theme: 'light',
        spacing: 'normal',
        contentWidth: 'contained'
      }
      return
    }
    
    // Caso contrário, carregue os dados da página pelo slug
    console.log('Carregando dados da página pelo slug:', pageSlug.value)
    const response = await axios.get(`/api/pages/${pageSlug.value}`)
    console.log('Resposta da API:', response.data)
    if (response.data.success) {
      const data = response.data.data
      
      // Se não houver componentes definidos, criar um componente de conteúdo padrão
      if (!data.components || data.components.length === 0) {
        console.log('Criando componente de conteúdo padrão a partir da API')
        pageComponents.value = [{
          type: 'content',
          props: {
            title: data.title || 'Título da Página',
            content: 'Esta página ainda não possui conteúdo configurado. Use o construtor de páginas para adicionar componentes.',
            columnSpan: 12
          }
        }]
      } else {
        pageComponents.value = data.components
      }
      
      pageGlobalStyles.value = data.globalStyles || {
        theme: 'light',
        spacing: 'normal',
        contentWidth: 'contained'
      }
    }
  } catch (error) {
    console.error('Erro ao carregar dados da página:', error)
    
    // Em caso de erro, criar um componente de erro
    pageComponents.value = [{
      type: 'content',
      props: {
        title: 'Erro ao carregar a página',
        content: 'Ocorreu um erro ao carregar os dados da página. Por favor, tente novamente mais tarde.',
        columnSpan: 12
      }
    }]
  }
}

// Mapeamento de tipos de componentes para componentes Vue
const getComponentByType = (type) => {
  const components = {
    'header': HeaderBlock,
    'content': ContentBlock,
    'cards': CardBlock,
    'form': FormBlock,
    'benefits': BenefitsBlock,
    'simulator': SimulatorBlock,
    'contact-channels': ContactChannelsBlock
  }
  console.log('Tipo de componente solicitado:', type, 'Componente encontrado:', components[type] ? 'Sim' : 'Não')
  return components[type] || 'div'
}

// Carregar dados da página ao montar o componente
onMounted(() => {
  console.log('Componente InternalPage montado')
  loadPageData()
})
</script>

<style>
/* Tema escuro */
.dark-theme {
  background-color: #1a202c;
  color: #f7fafc;
}

.dark-theme h1, 
.dark-theme h2, 
.dark-theme h3, 
.dark-theme h4, 
.dark-theme h5, 
.dark-theme h6 {
  color: #f7fafc;
}

.dark-theme p {
  color: #e2e8f0;
}

.dark-theme a {
  color: #90cdf4;
}

.dark-theme a:hover {
  color: #63b3ed;
}

/* Estilos específicos para componentes no tema escuro */
.dark-theme .header-block {
  background-color: #2d3748;
  border-color: #4a5568;
}

.dark-theme .content-block {
  background-color: #2d3748;
  border-color: #4a5568;
}

.dark-theme .card-block {
  background-color: #2d3748;
  border-color: #4a5568;
}

.dark-theme .benefits-block {
  background-color: #2d3748;
  border-color: #4a5568;
}

.dark-theme .simulator-block {
  background-color: #2d3748;
  border-color: #4a5568;
}

.dark-theme .form-block {
  background-color: #2d3748;
  border-color: #4a5568;
}

.dark-theme .contact-channels-block {
  background-color: #2d3748;
  border-color: #4a5568;
}

/* Espaçamento entre componentes */
.gap-y-12 {
  margin-bottom: 3rem;
}

.gap-y-20 {
  margin-bottom: 5rem;
}

.gap-y-28 {
  margin-bottom: 7rem;
}

/* Largura do conteúdo */
.max-w-7xl {
  max-width: 80rem;
}

.max-w-4xl {
  max-width: 56rem;
}

.internal-page {
  @apply min-h-screen;
}

:deep(.nested-components) {
  @apply mt-8 grid grid-cols-12 gap-8;
}
</style>

