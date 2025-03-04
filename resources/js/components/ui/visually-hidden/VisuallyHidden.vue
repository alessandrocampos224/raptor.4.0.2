<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
  inheritAttrs: false,
});

const props = withDefaults(
  defineProps<{
    asChild?: boolean;
    as?: any;
  }>(),
  {
    asChild: false,
    as: 'span',
  }
);

const Component = props.as;
</script>

<template>
  <Component
    v-bind="$attrs"
    class="absolute w-px h-px p-0 -m-[1px] overflow-hidden whitespace-nowrap border-0"
    :style="{
      clip: 'rect(0, 0, 0, 0)',
    }"
  >
    <slot />
  </Component>
</template>
