<template>
  <div class="min-h-screen flex flex-col items-center justify-center bg-gray-100 dark:bg-gray-900">
    <div class="text-center p-8 bg-white dark:bg-gray-800 rounded-lg shadow-md max-w-md w-full">
      <h1 class="text-6xl font-bold text-red-500 mb-4">500</h1>
      <h2 class="text-2xl font-semibold text-gray-800 dark:text-white mb-4">Erro interno do servidor</h2>
      <p class="text-gray-600 dark:text-gray-300 mb-6">
        {{ message || 'Ocorreu um erro ao processar sua solicitação. Por favor, tente novamente mais tarde.' }}
      </p>
      <a 
        href="/" 
        class="inline-block px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
      >
        Voltar para a página inicial
      </a>
    </div>
  </div>
</template>

<script setup>
defineProps({
  message: {
    type: String,
    default: ''
  }
})
</script> 