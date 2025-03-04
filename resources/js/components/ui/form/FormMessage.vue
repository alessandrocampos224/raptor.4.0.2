<script lang="ts" setup>
defineProps<{
  name: string;
  errors: any;
}>();
</script>

<template>
  <p class="text-[0.8rem] font-medium text-destructive" v-if="errors[name]">
    {{ errors[name] }} 
  </p>
</template>
