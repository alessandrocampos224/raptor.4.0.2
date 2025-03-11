<template>
  <div class="space-y-6">
    <!-- Título e Descrição -->
    <div class="space-y-4">
      <h4 class="text-sm font-medium">Título e Descrição</h4>
      <div class="space-y-3">
        <div>
          <Label class="mb-1 block">Título da Seção</Label>
          <Input v-model="settings.title" placeholder="Digite o título da seção (opcional)" />
        </div>
        <div>
          <Label class="mb-1 block">Descrição</Label>
          <Textarea v-model="settings.description" rows="2" placeholder="Digite uma descrição para a seção (opcional)" />
        </div>
      </div>
    </div>

    <!-- Container -->
    <div class="space-y-4">
      <h4 class="text-sm font-medium">Container</h4>
      <div class="space-y-3">
        <div>
          <Label class="mb-1 block">Largura</Label>
          <Select v-model="settings.containerWidth">
            <SelectTrigger class="w-full">
              <SelectValue placeholder="Selecione a largura" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="default">Padrão</SelectItem>
              <SelectItem value="full">Largura Total</SelectItem>
              <SelectItem value="narrow">Estreito</SelectItem>
            </SelectContent>
          </Select>
        </div>
        <div>
          <Label class="mb-1 block">Alinhamento do Texto</Label>
          <Select v-model="settings.textAlignment">
            <SelectTrigger class="w-full">
              <SelectValue placeholder="Selecione o alinhamento" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="left">Esquerda</SelectItem>
              <SelectItem value="center">Centro</SelectItem>
              <SelectItem value="right">Direita</SelectItem>
            </SelectContent>
          </Select>
        </div>
      </div>
    </div>

    <!-- Background -->
    <div class="space-y-4">
      <h4 class="text-sm font-medium">Background</h4>
      <div class="space-y-3">
        <div>
          <Label class="mb-1 block">Cor de Fundo</Label>
          <Select v-model="settings.backgroundColor">
            <SelectTrigger class="w-full">
              <SelectValue placeholder="Selecione a cor de fundo" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="white">Branco</SelectItem>
              <SelectItem value="gray-50">Cinza Claro</SelectItem>
              <SelectItem value="gray-100">Cinza</SelectItem>
              <SelectItem value="tenant-primary">Cor Principal</SelectItem>
              <SelectItem value="tenant-secondary">Cor Secundária</SelectItem>
            </SelectContent>
          </Select>
        </div>
        <div>
          <Label class="mb-1 block">Imagem de Fundo</Label>
          <div class="flex gap-2">
            <Input v-model="settings.backgroundImage" type="text" placeholder="URL da imagem" class="flex-1" />
            <Button type="button" class="px-3 py-1 bg-gray-100 dark:bg-dark-card rounded-md text-sm">Escolher</Button>
          </div>
        </div>
      </div>
    </div>

    <!-- Bordas e Sombras -->
    <div class="space-y-4">
      <h4 class="text-sm font-medium">Bordas e Sombras</h4>
      <div class="space-y-3">
        <div>
          <Label class="mb-1 block">Sombra</Label>
          <Select v-model="settings.shadow">
            <SelectTrigger class="w-full">
              <SelectValue placeholder="Selecione a sombra" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="none">Nenhuma</SelectItem>
              <SelectItem value="sm">Pequena</SelectItem>
              <SelectItem value="md">Média</SelectItem>
              <SelectItem value="lg">Grande</SelectItem>
              <SelectItem value="xl">Extra Grande</SelectItem>
            </SelectContent>
          </Select>
        </div>
        <div>
          <Label class="mb-1 block">Estilo da Borda</Label>
          <Select v-model="settings.borderStyle">
            <SelectTrigger class="w-full">
              <SelectValue placeholder="Selecione o estilo da borda" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="none">Nenhuma</SelectItem>
              <SelectItem value="solid">Sólida</SelectItem>
              <SelectItem value="dashed">Tracejada</SelectItem>
              <SelectItem value="dotted">Pontilhada</SelectItem>
            </SelectContent>
          </Select>
        </div>
        <div v-if="settings.borderStyle !== 'none'">
          <Label class="mb-1 block">Cor da Borda</Label>
          <Select v-model="settings.borderColor">
            <SelectTrigger class="w-full">
              <SelectValue placeholder="Selecione a cor da borda" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="gray-200">Cinza Claro</SelectItem>
              <SelectItem value="gray-300">Cinza</SelectItem>
              <SelectItem value="tenant-primary">Cor Principal</SelectItem>
            </SelectContent>
          </Select>
        </div>
        <div>
          <Label class="mb-1 block">Arredondamento</Label>
          <Select v-model="settings.borderRadius">
            <SelectTrigger class="w-full">
              <SelectValue placeholder="Selecione o arredondamento" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="none">Nenhum</SelectItem>
              <SelectItem value="sm">Pequeno</SelectItem>
              <SelectItem value="md">Médio</SelectItem>
              <SelectItem value="lg">Grande</SelectItem>
              <SelectItem value="full">Total</SelectItem>
            </SelectContent>
          </Select>
        </div>
      </div>
    </div>

    <!-- Espaçamento -->
    <div class="space-y-4">
      <h4 class="text-sm font-medium">Espaçamento</h4>
      <div class="space-y-3">
        <!-- Espaçamento entre componentes -->
        <div>
          <Label class="mb-1 block">Espaçamento entre Componentes</Label>
          <Select v-model="settings.spacing">
            <SelectTrigger class="w-full">
              <SelectValue placeholder="Selecione o espaçamento" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="compact">Compacto</SelectItem>
              <SelectItem value="normal">Normal</SelectItem>
              <SelectItem value="relaxed">Relaxado</SelectItem>
            </SelectContent>
          </Select>
        </div>
        <!-- Padding Vertical -->
        <div>
          <Label class="mb-1 block">Padding Vertical</Label>
          <Select v-model="settings.paddingY">
            <SelectTrigger class="w-full">
              <SelectValue placeholder="Selecione o padding vertical" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="4">Pequeno (py-4)</SelectItem>
              <SelectItem value="8">Médio (py-8)</SelectItem>
              <SelectItem value="12">Normal (py-12)</SelectItem>
              <SelectItem value="16">Grande (py-16)</SelectItem>
              <SelectItem value="20">Extra Grande (py-20)</SelectItem>
            </SelectContent>
          </Select>
        </div>
        <!-- Padding Horizontal -->
        <div>
          <Label class="mb-1 block">Padding Horizontal</Label>
          <Select v-model="settings.paddingX">
            <SelectTrigger class="w-full">
              <SelectValue placeholder="Selecione o padding horizontal" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="4">Pequeno (px-4)</SelectItem>
              <SelectItem value="8">Médio (px-8)</SelectItem>
              <SelectItem value="12">Normal (px-12)</SelectItem>
              <SelectItem value="16">Grande (px-16)</SelectItem>
              <SelectItem value="20">Extra Grande (px-20)</SelectItem>
            </SelectContent>
          </Select>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, watch, onMounted } from 'vue'
import { Input } from './ui/input'
import { Label } from './ui/label'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from './ui/select'
import { Textarea } from './ui/textarea'
import { Button } from './ui/button'

// Props
const props = defineProps({
  section: {
    type: Object,
    required: true,
    default: () => ({
      id: '',
      props: {}
    })
  }
})

const emit = defineEmits(['update'])

// Inicializa as configurações com valores padrão
const initializeSettings = () => {
  return {
    title: '',
    description: '',
    containerWidth: 'default',
    textAlignment: 'left',
    backgroundColor: 'white',
    backgroundImage: '',
    overlayOpacity: 0,
    shadow: 'none',
    borderStyle: 'none',
    borderWidth: '0',
    borderColor: 'gray-200',
    borderRadius: 'none',
    paddingY: '12',
    paddingX: '4',
    spacing: 'normal',
    titleSize: '3xl',
    titleWeight: 'bold',
    titleColor: 'gray-900',
    descriptionSize: 'lg',
    descriptionColor: 'gray-600'
  }
}

// Inicializa as configurações com os valores da seção ou valores padrão
const settings = reactive(initializeSettings())

// Carregar dados iniciais
onMounted(() => {
  // Mesclar as configurações existentes com as novas
  Object.assign(settings, props.section?.props || {})
})

// Watch para emitir atualizações
let updateTimeout = null
watch(settings, (newSettings) => {
  // Limpa o timeout anterior para evitar múltiplas atualizações
  if (updateTimeout) {
    clearTimeout(updateTimeout)
  }
  
  // Usa um timeout para debounce das atualizações
  updateTimeout = setTimeout(() => {
    // Cria uma cópia profunda das configurações para evitar mutações
    const updatedSettings = JSON.parse(JSON.stringify(newSettings))
    
    console.log('SectionSettings - Emitindo atualização:', {
      sectionId: props.section.id,
      settings: updatedSettings
    })
    
    // Emitir o evento com o ID da seção e as configurações atualizadas
    emit('update', props.section.id, updatedSettings)
  }, 300) // 300ms de debounce
}, { deep: true })

// Watch para atualizar quando a seção mudar
watch(() => props.section, (newSection) => {
  if (newSection) {
    console.log('SectionSettings - Nova seção recebida:', newSection)
    
    // Limpa o timeout para evitar que atualizações pendentes sejam aplicadas à nova seção
    if (updateTimeout) {
      clearTimeout(updateTimeout)
      updateTimeout = null
    }
    
    // Cria uma cópia profunda das configurações iniciais
    const initialSettings = JSON.parse(JSON.stringify(initializeSettings()))
    
    // Cria uma cópia profunda das props da seção
    const sectionProps = newSection.props ? JSON.parse(JSON.stringify(newSection.props)) : {}
    
    // Limpa as configurações atuais para evitar mesclar com valores antigos
    Object.keys(settings).forEach(key => {
      delete settings[key]
    })
    
    // Mescla as configurações
    Object.assign(settings, {
      ...initialSettings,
      ...sectionProps
    })
    
    console.log('SectionSettings - Settings após atualização:', settings)
  }
}, { deep: true, immediate: true })
</script> 