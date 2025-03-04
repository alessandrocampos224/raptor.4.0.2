<!-- GalleryBlock.vue -->
<template>
  <div
    :class="[
      'gallery-block',
      `style-${style}`,
      `layout-${layout}`,
      `spacing-v-${spacingVertical}`,
      `spacing-h-${spacingHorizontal}`
    ]"
  >
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div v-if="title" class="text-center mb-8">
        <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
          {{ title }}
        </h2>
        <p v-if="subtitle" class="mt-3 max-w-2xl mx-auto text-xl text-gray-500 dark:text-gray-400 sm:mt-4">
          {{ subtitle }}
        </p>
      </div>

      <div :class="gridClass">
        <div
          v-for="(image, index) in images"
          :key="index"
          class="group relative"
        >
          <!-- Overlay de hover -->
          <div class="relative w-full h-80 bg-white dark:bg-gray-800 rounded-lg overflow-hidden group-hover:opacity-75 transition-opacity">
            <img
              :src="image.url"
              :alt="image.caption"
              class="w-full h-full object-center object-cover"
            />
          </div>
          <div class="mt-4 flex items-center justify-between">
            <div>
              <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                <a :href="image.url" target="_blank" class="hover:underline">
                  {{ image.caption }}
                </a>
              </h3>
              <p v-if="image.description" class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ image.description }}
              </p>
            </div>
            <button
              v-if="isEditable"
              @click="removeImage(index)"
              class="p-1 bg-white dark:bg-gray-800 rounded-full shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600"
            >
              <Trash class="h-5 w-5 text-gray-400 dark:text-gray-300" />
            </button>
          </div>
        </div>

        <!-- Botão de adicionar imagem quando editável -->
        <div
          v-if="isEditable"
          class="relative w-full h-80 bg-gray-100 dark:bg-gray-800 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 p-12 text-center hover:border-gray-400 dark:hover:border-gray-500 transition-colors cursor-pointer"
          @click="triggerImageUpload"
        >
          <div class="space-y-1">
            <svg
              class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500"
              stroke="currentColor"
              fill="none"
              viewBox="0 0 48 48"
              aria-hidden="true"
            >
              <path
                d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              />
            </svg>
            <div class="flex text-sm text-gray-600 dark:text-gray-400">
              <label
                class="relative cursor-pointer rounded-md font-medium text-primary-600 dark:text-primary-400 hover:text-primary-500 dark:hover:text-primary-300 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary-500"
              >
                <span>Adicionar imagem</span>
                <input
                  ref="fileInput"
                  type="file"
                  class="sr-only"
                  accept="image/*"
                  @change="handleImageUpload"
                  multiple
                />
              </label>
              <p class="pl-1">ou arraste e solte</p>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400">
              PNG, JPG, GIF até 10MB
            </p>
          </div>
        </div>
      </div>

      <!-- Área para componentes aninhados -->
      <div v-if="allowNesting" class="nested-components-container mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
        <slot></slot>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Trash } from 'lucide-vue-next'

const props = defineProps({
  images: {
    type: Array,
    default: () => []
  },
  title: {
    type: String,
    default: ''
  },
  subtitle: {
    type: String,
    default: ''
  },
  style: {
    type: [String, Object],
    default: () => ({}),
  },
  layout: {
    type: String,
    default: 'default'
  },
  spacingVertical: {
    type: String,
    default: 'default'
  },
  spacingHorizontal: {
    type: String,
    default: 'default'
  },
  columns: {
    type: Number,
    default: 3
  },
  isEditable: {
    type: Boolean,
    default: false
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

const emit = defineEmits(['update:images'])

const fileInput = ref(null)

const gridClass = computed(() => {
  const baseClass = 'mt-8 grid gap-8'
  switch (props.columns) {
    case 2:
      return `${baseClass} grid-cols-1 sm:grid-cols-2`
    case 3:
      return `${baseClass} grid-cols-1 sm:grid-cols-2 lg:grid-cols-3`
    case 4:
      return `${baseClass} grid-cols-1 sm:grid-cols-2 lg:grid-cols-4`
    default:
      return `${baseClass} grid-cols-1 sm:grid-cols-2 lg:grid-cols-3`
  }
})

const triggerImageUpload = () => {
  fileInput.value?.click()
}

const handleImageUpload = async (event) => {
  const files = Array.from(event.target.files)
  const newImages = []

  for (const file of files) {
    if (file.size > 10 * 1024 * 1024) { // 10MB limit
      alert('Arquivo muito grande. O limite é 10MB.')
      continue
    }

    try {
      // Aqui você implementará a lógica de upload
      // Por enquanto, vamos apenas criar uma URL local
      const url = URL.createObjectURL(file)
      newImages.push({
        url,
        caption: file.name,
        description: ''
      })
    } catch (error) {
      console.error('Erro ao processar imagem:', error)
    }
  }

  emit('update:images', [...props.images, ...newImages])
}

const removeImage = (index) => {
  const newImages = [...props.images]
  newImages.splice(index, 1)
  emit('update:images', newImages)
}
</script>

<style scoped>
.gallery-block {
  @apply py-12;
}

/* Estilos */
.style-default {
  @apply bg-white dark:bg-gray-900;
}

.style-highlighted {
  @apply bg-gray-50 dark:bg-gray-800;
}

.style-bordered {
  @apply border border-gray-200 dark:border-gray-700 rounded-lg;
}

/* Layouts */
.layout-default {
  @apply w-full;
}

.layout-centered {
  @apply text-center;
}

.layout-narrow {
  @apply max-w-4xl mx-auto;
}

.layout-wide {
  @apply max-w-7xl mx-auto;
}

/* Espaçamentos Verticais */
.spacing-v-default {
  @apply py-12;
}

.spacing-v-compact {
  @apply py-8;
}

.spacing-v-comfortable {
  @apply py-16;
}

.spacing-v-spacious {
  @apply py-20;
}

/* Espaçamentos Horizontais */
.spacing-h-default {
  @apply px-4 sm:px-6 lg:px-8;
}

.spacing-h-compact {
  @apply px-4;
}

.spacing-h-comfortable {
  @apply px-6 sm:px-8 lg:px-10;
}

.spacing-h-spacious {
  @apply px-8 sm:px-10 lg:px-12;
}
</style> 