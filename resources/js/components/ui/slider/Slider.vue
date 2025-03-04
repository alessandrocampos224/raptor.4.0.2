<template>
  <div class="relative flex w-full touch-none select-none items-center">
    <div ref="track" class="relative h-2 w-full grow overflow-hidden rounded-full bg-secondary">
      <div
        :style="{ transform: `translateX(-${100 - (modelValue / max) * 100}%)` }"
        class="absolute h-full w-full bg-primary"
      ></div>
    </div>
    <input
      :value="modelValue"
      @input="updateValue"
      type="range"
      :min="min"
      :max="max"
      :step="step"
      class="absolute h-2 w-full cursor-pointer appearance-none bg-transparent"
    />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  modelValue: {
    type: Number,
    required: true
  },
  min: {
    type: Number,
    default: 0
  },
  max: {
    type: Number,
    default: 100
  },
  step: {
    type: Number,
    default: 1
  }
})

const emit = defineEmits(['update:modelValue'])

const track = ref(null)

const updateValue = (event) => {
  emit('update:modelValue', parseFloat(event.target.value))
}
</script>

<style scoped>
input[type="range"] {
  -webkit-appearance: none;
  appearance: none;
  background: transparent;
}

input[type="range"]::-webkit-slider-thumb {
  -webkit-appearance: none;
  appearance: none;
  width: 16px;
  height: 16px;
  border-radius: 50%;
  background: hsl(var(--primary));
  cursor: pointer;
  border: none;
}

input[type="range"]::-moz-range-thumb {
  width: 16px;
  height: 16px;
  border-radius: 50%;
  background: hsl(var(--primary));
  cursor: pointer;
  border: none;
}

input[type="range"]:focus {
  outline: none;
}
</style> 