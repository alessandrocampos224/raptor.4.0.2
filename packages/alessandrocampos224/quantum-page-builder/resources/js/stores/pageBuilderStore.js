import { defineStore } from 'pinia'
import { v4 as uuidv4 } from 'uuid'
import axios from 'axios'

export const usePageBuilderStore = defineStore('pageBuilder', {
  state: () => ({
    sections: [],
    globalStyles: {
      theme: 'light',
      spacing: 'normal',
      contentWidth: 'contained'
    },
    selectedComponent: null,
    drawerOpen: false,
    columnSelectorOpen: false,
    pendingComponent: null,
    pendingSectionId: null,
    isSaving: false,
    saveSuccess: false
  }),

  getters: {
    getSectionById: (state) => (id) => {
      return state.sections.find(section => section.id === id)
    },
    getComponentById: (state) => (id) => {
      for (const section of state.sections) {
        const component = section.components.find(comp => comp.id === id)
        if (component) return component
      }
      return null
    }
  },

  actions: {
    // Ações para seções
    addSection() {
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
      this.sections.push(newSection)
    },

    updateSection(updatedSection, index) {
      this.sections[index] = updatedSection
    },

    moveSection(index, direction) {
      if (direction === 'up' && index > 0) {
        const temp = this.sections[index - 1]
        this.sections[index - 1] = this.sections[index]
        this.sections[index] = temp
      } else if (direction === 'down' && index < this.sections.length - 1) {
        const temp = this.sections[index + 1]
        this.sections[index + 1] = this.sections[index]
        this.sections[index] = temp
      }
    },

    duplicateSection(index) {
      const sectionToDuplicate = this.sections[index]
      const newSection = JSON.parse(JSON.stringify(sectionToDuplicate))
      newSection.id = uuidv4()
      newSection.components = newSection.components.map(component => ({
        ...component,
        id: uuidv4()
      }))
      this.sections.splice(index + 1, 0, newSection)
    },

    removeSection(index) {
      this.sections.splice(index, 1)
    },

    // Ações para componentes
    addComponent(component, sectionId) {
      this.pendingComponent = component
      this.pendingSectionId = sectionId
      this.columnSelectorOpen = true
    },

    confirmComponentColumns(columns) {
      if (this.pendingComponent && this.pendingSectionId) {
        const sectionIndex = this.sections.findIndex(s => s.id === this.pendingSectionId)
        if (sectionIndex !== -1) {
          this.pendingComponent.props.columnSpan = columns
          this.sections[sectionIndex].components.push(this.pendingComponent)
        }
      }
      this.closeColumnSelector()
    },

    closeColumnSelector() {
      this.columnSelectorOpen = false
      this.pendingComponent = null
      this.pendingSectionId = null
    },

    editComponent(component) {
      this.selectedComponent = component
      this.drawerOpen = true
    },

    closeDrawer() {
      this.drawerOpen = false
      this.selectedComponent = null
    },

    updateComponentSettings(componentId, newSettings, sectionId) {
      const sectionIndex = this.sections.findIndex(s => s.id === sectionId)
      if (sectionIndex !== -1) {
        const componentIndex = this.sections[sectionIndex].components.findIndex(c => c.id === componentId)
        if (componentIndex !== -1) {
          this.sections[sectionIndex].components[componentIndex].props = newSettings
        }
      }
    },

    moveComponent(component, direction, sectionId) {
      const sectionIndex = this.sections.findIndex(s => s.id === sectionId)
      if (sectionIndex !== -1) {
        const components = this.sections[sectionIndex].components
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
      }
    },

    duplicateComponent(component, sectionId) {
      const sectionIndex = this.sections.findIndex(s => s.id === sectionId)
      if (sectionIndex !== -1) {
        const newComponent = JSON.parse(JSON.stringify(component))
        newComponent.id = uuidv4()
        const componentIndex = this.sections[sectionIndex].components.findIndex(c => c.id === component.id)
        this.sections[sectionIndex].components.splice(componentIndex + 1, 0, newComponent)
      }
    },

    removeComponent(component, sectionId) {
      const sectionIndex = this.sections.findIndex(s => s.id === sectionId)
      if (sectionIndex !== -1) {
        const componentIndex = this.sections[sectionIndex].components.findIndex(c => c.id === component.id)
        if (componentIndex !== -1) {
          this.sections[sectionIndex].components.splice(componentIndex, 1)
        }
      }
    },

    // Ações para salvar e carregar
    async savePageStructure() {
      try {
        this.isSaving = true
        const structure = {
          sections: this.sections,
          globalStyles: this.globalStyles
        }
        
        await axios.post('/api/page-builder/save', structure)
        
        this.saveSuccess = true
        setTimeout(() => {
          this.saveSuccess = false
        }, 2000)
      } catch (error) {
        console.error('Erro ao salvar a estrutura da página:', error)
      } finally {
        this.isSaving = false
      }
    },

    async loadPageStructure() {
      try {
        const response = await axios.get('/api/page-builder/load')
        if (response.data.success) {
          this.sections = response.data.data.sections || []
          this.globalStyles = response.data.data.globalStyles || {
            theme: 'light',
            spacing: 'normal',
            contentWidth: 'contained'
          }
        }
      } catch (error) {
        console.error('Erro ao carregar a estrutura da página:', error)
      }
    },

    // Ações para estilos globais
    updateGlobalStyles(styles) {
      this.globalStyles = {
        ...this.globalStyles,
        ...styles
      }
    }
  }
}) 