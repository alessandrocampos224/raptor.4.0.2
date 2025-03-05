<template>
  <div class="space-y-2">
    <!-- Configurações comuns para todos os componentes -->
    <template v-for="type in ['header', 'content', 'benefits', 'simulator', 'contact-channels', 'cards', 'form']" :key="type">
      <template v-if="component?.type === type">
        <!-- Sistema de Colunas -->
        <div class="space-y-2">
          <h4 class="text-sm font-medium">Sistema de Colunas</h4>
          <div class="space-y-2">
            <div>
              <Label class="mb-1">Largura em Colunas</Label>
              <Select v-model="settings.columnSpan">
                <SelectTrigger class="w-full">
                  <SelectValue placeholder="Selecione o número de colunas" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem v-for="n in 12" :key="n" :value="n">{{ n }} {{ n === 1 ? 'coluna' : 'colunas' }}</SelectItem>
                </SelectContent>
              </Select>
            </div>
          </div>
        </div>

        <!-- Aninhamento de Componentes -->
        <div class="space-y-2">
          <h4 class="text-sm font-medium">Aninhamento de Componentes</h4>
          <div class="space-y-2">
            <div class="flex items-center space-x-2">
              <button
                type="button"
                role="switch"
                :aria-checked="settings.allowNesting"
                @click="toggleNesting"
                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-primary-600 focus:ring-offset-2"
                :class="[
                  settings.allowNesting ? 'bg-primary-600' : 'bg-gray-200',
                ]"
              >
                <span
                  class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                  :class="[
                    settings.allowNesting ? 'translate-x-6' : 'translate-x-1',
                  ]"
                />
              </button>
              <Label>Permitir componentes aninhados</Label>
            </div>
          </div>
        </div>

        <!-- Container -->
        <div class="space-y-2">
          <h4 class="text-sm font-medium">Container</h4>
          <div class="space-y-2">
            <div>
              <Label class="mb-1">Largura</Label>
              <Select v-model="settings.containerWidth">
                <SelectTrigger class="w-full">
                  <SelectValue placeholder="Selecione a largura" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="default">Padrão</SelectItem>
                  <SelectItem value="full">Largura Total</SelectItem>
                  <SelectItem value="contained">Contido</SelectItem>
                  <SelectItem value="narrow">Estreito</SelectItem>
                </SelectContent>
              </Select>
            </div>
          </div>
        </div>

        <!-- Opção de vincular conteúdo para header, content e cards -->
        <template v-if="type === 'header' || type === 'content' || type === 'cards'">
          <div class="space-y-4">
            <h4 class="text-sm font-medium">Vincular Conteúdo</h4>
            <div class="space-y-3">
              <div>
                <Label class="mb-1">Fonte de Dados</Label>
                <Select v-model="settings.dataSource">
                  <SelectTrigger>
                    <SelectValue placeholder="Selecione a fonte de dados" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="static">Conteúdo Estático</SelectItem>
                    <SelectItem value="dynamic">Conteúdo Dinâmico</SelectItem>
                  </SelectContent>
                </Select>
              </div>
              
              <div v-if="settings.dataSource === 'dynamic'">
                <div>
                  <Label class="mb-1">Tipo de Conteúdo</Label>
                  <Select v-model="settings.contentType">
                    <SelectTrigger>
                      <SelectValue placeholder="Selecione o tipo de conteúdo" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="single">Item Único</SelectItem>
                      <SelectItem value="category">Categoria</SelectItem>
                      <SelectItem value="posts">Posts</SelectItem>
                    </SelectContent>
                  </Select>
                </div>
                
                <div v-if="settings.contentType === 'single'">
                  <ContentSelector v-model="settings.contentLink" />
                </div>
                
                <div v-if="settings.contentType === 'category'">
                  <Label class="mb-1">Selecionar Categorias</Label>
                  <div class="space-y-2 mt-2">
                    <div v-if="isLoading" class="flex items-center justify-center py-4">
                      <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-primary-600"></div>
                    </div>
                    <div v-else class="grid grid-cols-1 gap-2">
                      <div v-for="category in availableCategories" :key="category.id" class="flex items-center">
                        <input 
                          type="checkbox" 
                          :id="`category-${category.id}`"
                          :checked="settings.selectedCategories?.includes(category.id)"
                          @change="(e) => {
                            if (!Array.isArray(settings.selectedCategories)) {
                              settings.selectedCategories = [];
                            }
                            if (e.target.checked) {
                              settings.selectedCategories.push(category.id);
                            } else {
                              settings.selectedCategories = settings.selectedCategories.filter(id => id !== category.id);
                            }
                          }"
                          class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                        />
                        <Label :for="`category-${category.id}`" class="ml-2 text-base uppercase">{{ category.name }}</Label>
                      </div>
                    </div>
                  </div>

                  <!-- Opções de Layout -->
                  <div class="mt-4">
                    <Label class="mb-1">Layout das Categorias</Label>
                    <Select v-model="settings.layout">
                      <SelectTrigger>
                        <SelectValue placeholder="Selecione o layout" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem value="list">Lista Simples</SelectItem>
                        <SelectItem value="grid">Grid com Cards</SelectItem>
                      </SelectContent>
                    </Select>
                  </div>

                  <!-- Preview das categorias selecionadas -->
                  <div class="mt-6 border-t pt-4">
                    <h4 class="text-sm font-medium mb-4">Preview</h4>
                    <div v-if="!settings.selectedCategories || settings.selectedCategories.length === 0" class="text-sm text-gray-500">
                      Selecione pelo menos uma categoria para visualizar
                    </div>
                    <div v-else class="space-y-2">
                      <div v-for="categoryId in settings.selectedCategories" :key="categoryId" class="text-base uppercase font-medium text-gray-800">
                        {{ availableCategories.find(c => c.id === categoryId)?.name }}
                      </div>
                    </div>
                  </div>
                </div>
                
                <div v-if="settings.contentType === 'posts'">
                  <div>
                    <Label class="mb-1">Filtrar por Categoria</Label>
                    <Select v-model="settings.filterCategory">
                      <SelectTrigger :disabled="isLoading">
                        <SelectValue placeholder="Selecione a categoria" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem value="all">Todas as categorias</SelectItem>
                        <SelectItem v-for="category in availableCategories" :key="category.id" :value="category.id">
                          {{ category.name }}
                        </SelectItem>
                      </SelectContent>
                    </Select>
                  </div>
                  <div class="mt-3">
                    <Label class="mb-1">Número de Posts</Label>
                    <Input v-model="settings.postsLimit" type="number" min="1" max="20" />
                  </div>
                  <div v-if="isLoading" class="flex items-center justify-center py-4">
                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-primary-600"></div>
                  </div>
                  <div v-else-if="error" class="p-2 text-sm text-red-500">
                    {{ error }}
                  </div>
                  <div v-else>
                    <div class="mt-4 space-y-2">
                      <div 
                        v-for="post in availablePosts" 
                        :key="post.id" 
                        class="flex items-start gap-3 p-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                      >
                        <input 
                          type="checkbox" 
                          :id="`post-${post.id}`"
                          :value="post.id"
                          :checked="settings.selectedPosts?.includes(post.id)"
                          @change="(e) => {
                            if (!Array.isArray(settings.selectedPosts)) {
                              settings.selectedPosts = [];
                            }
                            if (e.target.checked) {
                              if (settings.selectedPosts.length < settings.postsLimit) {
                                settings.selectedPosts.push(post.id);
                              }
                            } else {
                              settings.selectedPosts = settings.selectedPosts.filter(id => id !== post.id);
                            }
                          }"
                          class="mt-1 h-4 w-4 rounded border-gray-300 text-tenant-primary focus:ring-tenant-primary"
                        />
                        <div class="flex-1">
                          <Label :for="`post-${post.id}`" class="text-sm font-medium text-gray-900 dark:text-gray-100 cursor-pointer">
                            {{ post.name }}
                          </Label>
                          <p v-if="post.description" class="mt-1 text-xs text-gray-500 dark:text-gray-400 line-clamp-2">
                            {{ post.description }}
                          </p>
                          <div class="mt-2 flex items-center gap-2">
                            <span v-if="post.category" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-tenant-primary/10 text-tenant-primary">
                              {{ post.category.name }}
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="mt-4 text-sm text-gray-500 flex items-center justify-between">
                      <span>{{ settings.selectedPosts?.length || 0 }} / {{ settings.postsLimit }} posts selecionados</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </template>

        <!-- Configurações específicas do cabeçalho -->
        <template v-if="type === 'header'">
          <div class="space-y-4">
            <h4 class="text-sm font-medium">Cabeçalho</h4>
            <div class="space-y-3">
              <!-- Título -->
              <div v-if="settings.dataSource !== 'dynamic' || !settings.contentLink?.data">
                <Label class="mb-1">Título</Label>
                <Input v-model="settings.title" placeholder="Digite o título" />
              </div>
              <div v-else>
                <Label class="mb-1">Título (Dinâmico)</Label>
                <div class="p-2 bg-gray-50 dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700 text-sm">
                  {{ settings.contentLink.data.name }}
                </div>
              </div>
              <div>
                <Label class="mb-1">Tamanho do Título</Label>
                <Select v-model="settings.titleSize">
                  <SelectTrigger>
                    <SelectValue placeholder="Selecione o tamanho" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="xl">Pequeno</SelectItem>
                    <SelectItem value="2xl">Médio</SelectItem>
                    <SelectItem value="3xl">Grande</SelectItem>
                    <SelectItem value="5xl">Extra Grande</SelectItem>
                  </SelectContent>
                </Select>
              </div>
              <div>
                <Label class="mb-1">Peso do Título</Label>
                <Select v-model="settings.titleWeight">
                  <SelectTrigger>
                    <SelectValue placeholder="Selecione o peso" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="normal">Normal</SelectItem>
                    <SelectItem value="medium">Médio</SelectItem>
                    <SelectItem value="semibold">Semi-negrito</SelectItem>
                    <SelectItem value="bold">Negrito</SelectItem>
                  </SelectContent>
                </Select>
              </div>
              <div>
                <Label class="mb-1">Cor do Título</Label>
                <Select v-model="settings.titleColor">
                  <SelectTrigger>
                    <SelectValue placeholder="Selecione a cor" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="gray-900">Preto</SelectItem>
                    <SelectItem value="gray-700">Cinza Escuro</SelectItem>
                    <SelectItem value="tenant-primary">Cor Principal</SelectItem>
                  </SelectContent>
                </Select>
              </div>

              <!-- Subtítulo -->
              <div v-if="settings.dataSource !== 'dynamic' || !settings.contentLink?.data">
                <Label class="mb-1">Subtítulo</Label>
                <Input v-model="settings.subtitle" placeholder="Digite o subtítulo" />
              </div>
              <div v-else>
                <Label class="mb-1">Subtítulo (Dinâmico)</Label>
                <div class="p-2 bg-gray-50 dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700 text-sm">
                  {{ settings.contentLink.data.description || 'Sem descrição' }}
                </div>
              </div>
              <div>
                <Label class="mb-1">Tamanho do Subtítulo</Label>
                <Select v-model="settings.subtitleSize">
                  <SelectTrigger>
                    <SelectValue placeholder="Selecione o tamanho" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="lg">Pequeno</SelectItem>
                    <SelectItem value="xl">Médio</SelectItem>
                    <SelectItem value="2xl">Grande</SelectItem>
                  </SelectContent>
                </Select>
              </div>
              <div>
                <Label class="mb-1">Cor do Subtítulo</Label>
                <Select v-model="settings.subtitleColor">
                  <SelectTrigger>
                    <SelectValue placeholder="Selecione a cor" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="gray-600">Cinza</SelectItem>
                    <SelectItem value="gray-500">Cinza Claro</SelectItem>
                    <SelectItem value="tenant-primary">Cor Principal</SelectItem>
                  </SelectContent>
                </Select>
              </div>
            </div>
          </div>
        </template>

        <!-- Configurações específicas do bloco de conteúdo -->
        <template v-if="type === 'content'">
          <div class="space-y-4">
            <h4 class="text-sm font-medium">Conteúdo</h4>
            <div class="space-y-3">
              <div v-if="settings.dataSource !== 'dynamic' || !settings.contentLink?.data">
                <Label class="mb-1">Conteúdo</Label>
                <Textarea v-model="settings.content" placeholder="Digite o conteúdo" rows="6" />
              </div>
              <div v-else>
                <Label class="mb-1">Conteúdo (Dinâmico)</Label>
                <div class="p-2 bg-gray-50 dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700 text-sm max-h-32 overflow-y-auto">
                  {{ settings.contentLink.data.description || 'Sem conteúdo' }}
                </div>
              </div>
              <div>
                <Label class="mb-1">Tamanho do Texto</Label>
                <Select v-model="settings.textSize">
                  <SelectTrigger>
                    <SelectValue placeholder="Selecione o tamanho" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="sm">Pequeno</SelectItem>
                    <SelectItem value="base">Médio</SelectItem>
                    <SelectItem value="lg">Grande</SelectItem>
                  </SelectContent>
                </Select>
              </div>
              <div>
                <Label class="mb-1">Cor do Texto</Label>
                <Select v-model="settings.textColor">
                  <SelectTrigger>
                    <SelectValue placeholder="Selecione a cor" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="gray-900">Preto</SelectItem>
                    <SelectItem value="gray-700">Cinza Escuro</SelectItem>
                    <SelectItem value="gray-600">Cinza</SelectItem>
                  </SelectContent>
                </Select>
              </div>
            </div>
          </div>
        </template>

        <!-- Configurações específicas para cards -->
        <template v-if="type === 'cards'">
          <div class="space-y-2">
            <h4 class="text-sm font-medium">Cards</h4>
            <div class="space-y-2">
              <!-- Título da seção -->
              <div v-if="settings.dataSource !== 'dynamic' || !settings.contentLink?.data">
                <Label class="mb-1">Título da Seção</Label>
                <Input v-model="settings.title" placeholder="Digite o título da seção" />
              </div>
              
              <!-- Subtítulo da seção -->
              <div v-if="settings.dataSource !== 'dynamic' || !settings.contentLink?.data">
                <Label class="mb-1">Subtítulo da Seção</Label>
                <Input v-model="settings.subtitle" placeholder="Digite o subtítulo da seção" />
              </div>
              
              <!-- Configurações de layout -->
              <div>
                <Label class="mb-1">Layout dos Cards</Label>
                <Select v-model="settings.layout">
                  <SelectTrigger class="w-full">
                    <SelectValue placeholder="Selecione o layout" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="grid">Grid</SelectItem>
                    <SelectItem value="list">Lista</SelectItem>
                    <SelectItem value="masonry">Masonry</SelectItem>
                  </SelectContent>
                </Select>
              </div>

              <!-- Configurações específicas para Grid -->
              <div v-if="settings.layout === 'grid'" class="space-y-2">
                <div>
                  <Label class="mb-1">Número de Colunas</Label>
                  <Select v-model="settings.columns">
                    <SelectTrigger class="w-full">
                      <SelectValue placeholder="Selecione o número de colunas" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem :value="1">1 Coluna</SelectItem>
                      <SelectItem :value="2">2 Colunas</SelectItem>
                      <SelectItem :value="3">3 Colunas</SelectItem>
                      <SelectItem :value="4">4 Colunas</SelectItem>
                    </SelectContent>
                  </Select>
                </div>

                <!-- Espaçamento entre cards -->
                <div>
                  <Label class="mb-1">Espaçamento</Label>
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
              </div>
            </div>
          </div>
        </template>

        <!-- Background -->
        <div class="space-y-2">
          <h4 class="text-sm font-medium">Background</h4>
          <div class="space-y-2">
            <div>
              <Label class="mb-1">Cor de Fundo</Label>
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
          </div>
        </div>

        <!-- Espaçamento -->
        <div class="space-y-2">
          <h4 class="text-sm font-medium">Espaçamento</h4>
          <div class="space-y-2">
            <!-- Padding Vertical -->
            <div>
              <Label class="mb-1">Padding Vertical</Label>
              <Select v-model="settings.paddingY">
                <SelectTrigger class="w-full">
                  <SelectValue placeholder="Selecione o padding vertical" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="2">Muito Pequeno (py-2)</SelectItem>
                  <SelectItem value="4">Pequeno (py-4)</SelectItem>
                  <SelectItem value="6">Médio (py-6)</SelectItem>
                  <SelectItem value="8">Normal (py-8)</SelectItem>
                  <SelectItem value="12">Grande (py-12)</SelectItem>
                </SelectContent>
              </Select>
            </div>

            <!-- Padding Horizontal -->
            <div>
              <Label class="mb-1">Padding Horizontal</Label>
              <Select v-model="settings.paddingX">
                <SelectTrigger class="w-full">
                  <SelectValue placeholder="Selecione o padding horizontal" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="2">Muito Pequeno (px-2)</SelectItem>
                  <SelectItem value="4">Pequeno (px-4)</SelectItem>
                  <SelectItem value="6">Médio (px-6)</SelectItem>
                  <SelectItem value="8">Normal (px-8)</SelectItem>
                  <SelectItem value="12">Grande (px-12)</SelectItem>
                </SelectContent>
              </Select>
            </div>
          </div>
        </div>

        <!-- Conteúdo específico de cada componente -->
        <template v-if="type === 'benefits'">
          <!-- Conteúdo existente do componente benefits -->
          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <h4 class="text-sm font-medium">Benefícios</h4>
              <button type="button" @click="addBenefit" class="text-sm text-tenant-primary hover:text-tenant-primary-dark">
                Adicionar Benefício
              </button>
            </div>
            <div v-if="settings.items" class="space-y-4">
              <div v-for="(item, index) in settings.items" :key="index" class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <div class="flex justify-between items-start mb-3">
                  <h5 class="text-sm font-medium">Benefício {{ index + 1 }}</h5>
                  <button type="button" @click="removeBenefit(index)" class="text-red-500 hover:text-red-600">
                    <Trash class="w-4 h-4" />
                  </button>
                </div>
                <div class="space-y-3">
                  <div>
                    <Label class="mb-1">Título</Label>
                    <Input v-model="item.title" placeholder="Digite o título" />
                  </div>
                  <div>
                    <Label class="mb-1">Descrição</Label>
                    <Textarea v-model="item.description" rows="2" />
                  </div>
                  <div>
                    <Label class="mb-1">Link</Label>
                    <div class="space-y-2">
                      <Input v-model="item.link.text" placeholder="Texto do link" />
                      <Input v-model="item.link.url" placeholder="URL" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Layout -->
          <div class="space-y-4">
            <h4 class="text-sm font-medium">Layout</h4>
            <div class="space-y-3">
              <div>
                <Label class="mb-1">Estilo</Label>
                <Select v-model="settings.style">
                  <SelectTrigger>
                    <SelectValue placeholder="Selecione o estilo" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="default">Padrão</SelectItem>
                    <SelectItem value="cards">Cards</SelectItem>
                    <SelectItem value="minimal">Minimalista</SelectItem>
                  </SelectContent>
                </Select>
              </div>
              <div>
                <Label class="mb-1">Colunas</Label>
                <Select v-model.number="settings.columns">
                  <SelectTrigger>
                    <SelectValue placeholder="Selecione o número de colunas" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="2">2 Colunas</SelectItem>
                    <SelectItem value="3">3 Colunas</SelectItem>
                    <SelectItem value="4">4 Colunas</SelectItem>
                  </SelectContent>
                </Select>
              </div>
            </div>
          </div>
        </template>

        <template v-if="type === 'simulator'">
          <!-- Conteúdo existente do componente simulator -->
          <div class="space-y-4">
            <h4 class="text-sm font-medium">Cabeçalho</h4>
            <div class="space-y-3">
              <div>
                <Label class="mb-1">Título</Label>
                <Input v-model="settings.title" placeholder="Digite o título" />
              </div>
              <div>
                <Label class="mb-1">Descrição</Label>
                <Textarea v-model="settings.description" rows="2" />
              </div>
            </div>
          </div>

          <!-- Campos -->
          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <h4 class="text-sm font-medium">Campos do Formulário</h4>
              <button type="button" @click="addField" class="text-sm text-tenant-primary hover:text-tenant-primary-dark">
                Adicionar Campo
              </button>
            </div>
            <div v-if="settings.fields" class="space-y-4">
              <div v-for="(field, index) in settings.fields" :key="index" class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <div class="flex justify-between items-start mb-3">
                  <h5 class="text-sm font-medium">Campo {{ index + 1 }}</h5>
                  <button type="button" @click="removeField(index)" class="text-red-500 hover:text-red-600">
                    <Trash class="w-4 h-4" />
                  </button>
                </div>
                <div class="space-y-3">
                  <div>
                    <Label class="mb-1">Tipo</Label>
                    <Select v-model="field.type">
                      <SelectTrigger>
                        <SelectValue placeholder="Selecione o tipo" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem value="text">Texto</SelectItem>
                        <SelectItem value="number">Número</SelectItem>
                        <SelectItem value="select">Seleção</SelectItem>
                        <SelectItem value="date">Data</SelectItem>
                        <SelectItem value="currency">Moeda</SelectItem>
                      </SelectContent>
                    </Select>
                  </div>
                  <div>
                    <Label class="mb-1">Nome do Campo</Label>
                    <Input v-model="field.name" placeholder="Digite o nome do campo" />
                  </div>
                  <div>
                    <Label class="mb-1">Rótulo</Label>
                    <Input v-model="field.label" placeholder="Digite o rótulo" />
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Botão -->
          <div class="space-y-4">
            <h4 class="text-sm font-medium">Botão de Envio</h4>
            <div class="space-y-3">
              <div>
                <Label class="mb-1">Texto do Botão</Label>
                <Input v-model="settings.submitText" placeholder="Digite o texto do botão" />
              </div>
            </div>
          </div>
        </template>

        <template v-if="type === 'contact-channels'">
          <!-- Conteúdo existente do componente contact-channels -->
          <div class="space-y-4">
            <h4 class="text-sm font-medium">Título</h4>
            <div class="space-y-3">
              <div>
                <Label class="mb-1">Texto</Label>
                <Input v-model="settings.title" placeholder="Digite o título" />
              </div>
            </div>
          </div>

          <!-- Canais -->
          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <h4 class="text-sm font-medium">Canais</h4>
              <button type="button" @click="addChannel" class="text-sm text-tenant-primary hover:text-tenant-primary-dark">
                Adicionar Canal
              </button>
            </div>
            <div v-if="settings.channels" class="space-y-4">
              <div v-for="(channel, index) in settings.channels" :key="index" class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <div class="flex justify-between items-start mb-3">
                  <h5 class="text-sm font-medium">Canal {{ index + 1 }}</h5>
                  <button type="button" @click="removeChannel(index)" class="text-red-500 hover:text-red-600">
                    <Trash class="w-4 h-4" />
                  </button>
                </div>
                <div class="space-y-3">
                  <div>
                    <Label class="mb-1">Título</Label>
                    <Input v-model="channel.title" placeholder="Digite o título" />
                  </div>
                  <div>
                    <Label class="mb-1">Descrição</Label>
                    <Textarea v-model="channel.description" rows="2" />
                  </div>
                  <div>
                    <Label class="mb-1">Ícone</Label>
                    <Select v-model="channel.icon">
                      <SelectTrigger>
                        <SelectValue placeholder="Selecione o ícone" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem value="Users">Atendimento Presencial</SelectItem>
                        <SelectItem value="Mail">E-mail</SelectItem>
                        <SelectItem value="Phone">Telefone</SelectItem>
                        <SelectItem value="Calendar">Agendamento</SelectItem>
                        <SelectItem value="Clock">Horário</SelectItem>
                        <SelectItem value="MapPin">Localização</SelectItem>
                        <SelectItem value="Globe">Website</SelectItem>
                        <SelectItem value="MessageSquare">Chat</SelectItem>
                      </SelectContent>
                    </Select>
                  </div>
                  <!-- Detalhes do Canal -->
                  <div class="space-y-2">
                    <div class="flex items-center justify-between">
                      <label class="text-xs font-medium">Detalhes</label>
                      <button type="button" @click="addChannelDetail(channel)" class="text-xs text-tenant-primary hover:text-tenant-primary-dark">
                        Adicionar Detalhe
                      </button>
                    </div>
                    <div v-for="(detail, detailIndex) in channel.details" :key="detailIndex" class="flex items-center space-x-2">
                      <Select v-model="detail.type">
                        <SelectTrigger>
                          <SelectValue placeholder="Selecione o tipo" />
                        </SelectTrigger>
                        <SelectContent>
                          <SelectItem value="address">Endereço</SelectItem>
                          <SelectItem value="phone">Telefone</SelectItem>
                          <SelectItem value="email">E-mail</SelectItem>
                          <SelectItem value="schedule">Horário</SelectItem>
                          <SelectItem value="time">Tempo</SelectItem>
                        </SelectContent>
                      </Select>
                      <Input v-model="detail.value" placeholder="Digite o valor" />
                      <button type="button" @click="removeChannelDetail(channel, detailIndex)" class="text-red-500 hover:text-red-600">
                        <Trash class="w-4 h-4" />
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Layout -->
          <div class="space-y-4">
            <h4 class="text-sm font-medium">Layout</h4>
            <div class="space-y-3">
              <div>
                <Label class="mb-1">Estilo</Label>
                <Select v-model="settings.style">
                  <SelectTrigger>
                    <SelectValue placeholder="Selecione o estilo" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="default">Padrão</SelectItem>
                    <SelectItem value="cards">Cards</SelectItem>
                    <SelectItem value="minimal">Minimalista</SelectItem>
                  </SelectContent>
                </Select>
              </div>
              <div>
                <Label class="mb-1">Colunas</Label>
                <Select v-model.number="settings.columns">
                  <SelectTrigger>
                    <SelectValue placeholder="Selecione o número de colunas" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="2">2 Colunas</SelectItem>
                    <SelectItem value="3">3 Colunas</SelectItem>
                    <SelectItem value="4">4 Colunas</SelectItem>
                  </SelectContent>
                </Select>
              </div>
            </div>
          </div>
        </template>

        <template v-if="type === 'form'">
          <!-- Conteúdo existente do componente form -->
          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <h4 class="text-sm font-medium">Campos do Formulário</h4>
              <button type="button" @click="addFormField" class="text-sm text-tenant-primary hover:text-tenant-primary-dark">
                Adicionar Campo
              </button>
            </div>
            <div v-if="settings.fields" class="space-y-4">
              <div v-for="(field, index) in settings.fields" :key="index" class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <div class="flex justify-between items-start mb-3">
                  <h5 class="text-sm font-medium">Campo {{ index + 1 }}</h5>
                  <button type="button" @click="removeFormField(index)" class="text-red-500 hover:text-red-600">
                    <Trash class="w-4 h-4" />
                  </button>
                </div>
                <div class="space-y-3">
                  <div>
                    <Label class="mb-1">Tipo</Label>
                    <Select v-model="field.type">
                      <SelectTrigger>
                        <SelectValue placeholder="Selecione o tipo" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem value="text">Texto</SelectItem>
                        <SelectItem value="number">Número</SelectItem>
                        <SelectItem value="select">Seleção</SelectItem>
                        <SelectItem value="date">Data</SelectItem>
                        <SelectItem value="currency">Moeda</SelectItem>
                      </SelectContent>
                    </Select>
                  </div>
                  <div>
                    <Label class="mb-1">Nome do Campo</Label>
                    <Input v-model="field.name" placeholder="Digite o nome do campo" />
                  </div>
                  <div>
                    <Label class="mb-1">Rótulo</Label>
                    <Input v-model="field.label" placeholder="Digite o rótulo" />
                  </div>
                  <div>
                    <Label class="mb-1">Placeholder</Label>
                    <Input v-model="field.placeholder" placeholder="Digite o placeholder" />
                  </div>
                  <div>
                    <Label class="mb-1">Obrigatório</Label>
                    <Input type="checkbox" v-model="field.required" />
                  </div>
                  <div>
                    <Label class="mb-1">Opções</Label>
                    <div class="space-y-2">
                      <div v-for="(option, optionIndex) in field.options" :key="optionIndex" class="flex items-center space-x-2">
                        <Input v-model="option.label" placeholder="Digite a opção" />
                        <button type="button" @click="removeFieldOption(field, optionIndex)" class="text-red-500 hover:text-red-600">
                          <Trash class="w-4 h-4" />
                        </button>
                      </div>
                      <button type="button" @click="addFieldOption(field)" class="text-xs text-tenant-primary hover:text-tenant-primary-dark">
                        Adicionar Opção
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </template>
      </template>
    </template>
  </div>
</template>

<script setup>
import { ref, reactive, watch, onMounted } from 'vue'
import { Trash, Plus } from 'lucide-vue-next'
import { Input } from './ui/input'
import { Label } from './ui/label'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from './ui/select'
import { Textarea } from './ui/textarea'
import { Button } from './ui/button'
import { Slider } from './ui/slider'
import { Checkbox } from './ui/checkbox'
import ContentSelector from './ContentSelector.vue'
import axios from 'axios'
import SelectedCategoriesList from './categories/SelectedCategoriesList.vue'

// Estado
const availableCategories = ref([])
const availablePosts = ref([])
const isLoading = ref(false)
const error = ref(null)
const isLoadingPosts = ref(false)

// Props
const props = defineProps({
  component: {
    type: Object,
    required: true,
    default: () => ({
      type: '',
      props: {}
    })
  }
})

const emit = defineEmits(['update'])

// Inicializa as configurações com valores padrão baseados no tipo do componente
const initializeSettings = () => {
  const defaultSettings = {
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
    style: 'default',
    dataSource: 'static',
    contentLink: null,
    columnSpan: 6,
    allowNesting: false,
    contentType: 'single',
    selectedCategories: [],
    selectedPosts: [],
    filterCategory: 'all',
    postsLimit: 6,
    layout: 'grid'
  }

  if (props.component?.type === 'header') {
    return {
      ...defaultSettings,
      title: '',
      subtitle: '',
      titleSize: '4xl',
      titleWeight: 'bold',
      titleColor: 'gray-900',
      titleFont: 'font-sans',
      subtitleSize: 'xl',
      subtitleWeight: 'normal',
      subtitleColor: 'gray-600',
      subtitleFont: 'font-sans',
      actions: []
    }
  } else if (props.component?.type === 'content') {
    return {
      ...defaultSettings,
      content: '',
      textSize: 'base',
      textColor: 'gray-900',
      textFont: 'font-sans'
    }
  } else if (props.component?.type === 'benefits') {
    return {
      ...defaultSettings,
      items: [],
      columns: 3
    }
  } else if (props.component?.type === 'simulator') {
    return {
      ...defaultSettings,
      title: '',
      description: '',
      fields: [],
      submitText: 'Calcular'
    }
  } else if (props.component?.type === 'contact-channels') {
    return {
      ...defaultSettings,
      title: '',
      channels: [],
      columns: 3,
      style: 'default'
    }
  } else if (props.component?.type === 'cards') {
    return {
      ...defaultSettings,
      title: '',
      subtitle: '',
      cards: [{
        title: 'Novo Card',
        description: 'Descrição do card',
        icon: 'document',
        link: {
          text: 'Ver mais',
          url: '#'
        }
      }],
      columns: 3,
      spacingVertical: 'default',
      spacingHorizontal: 'default'
    }
  } else if (props.component?.type === 'form') {
    return {
      ...defaultSettings,
      title: '',
      description: '',
      fields: [],
      submitText: 'Enviar'
    }
  } else {
    return defaultSettings
  }
}

// Inicializa as configurações com os valores do componente ou valores padrão
const settings = reactive(initializeSettings())

// Garantir que selectedCategories seja sempre um array
if (!Array.isArray(settings.selectedCategories)) {
  settings.selectedCategories = []
}

// Função para carregar categorias
const loadCategories = async () => {
  try {
    isLoading.value = true
    error.value = null
    const response = await axios.get('/api/page-builder/categories')
    if (response.data.success) {
      availableCategories.value = response.data.data
      console.log('Categorias carregadas:', availableCategories.value)
    } else {
      error.value = response.data.message || 'Erro ao carregar categorias'
      console.error('Erro ao carregar categorias:', response.data.message)
    }
  } catch (error) {
    if (error.response && error.response.status === 401) {
      error.value = 'Você precisa estar autenticado para acessar as categorias'
    } else {
      error.value = 'Erro ao carregar categorias. Tente novamente mais tarde.'
    }
    console.error('Erro ao carregar categorias:', error)
  } finally {
    isLoading.value = false
  }
}

// Função para carregar posts
const loadPosts = async (categoryId = null) => {
  try {
    isLoading.value = true
    error.value = null
    const url = categoryId 
      ? `/api/page-builder/categories/${categoryId}/posts`
      : '/api/page-builder/posts'
    const response = await axios.get(url)
    if (response.data.success) {
      availablePosts.value = response.data.data
    } else {
      error.value = response.data.message || 'Erro ao carregar posts'
      console.error('Erro ao carregar posts:', response.data.message)
    }
  } catch (error) {
    if (error.response && error.response.status === 401) {
      error.value = 'Você precisa estar autenticado para acessar os posts'
    } else {
      error.value = 'Erro ao carregar posts. Tente novamente mais tarde.'
    }
    console.error('Erro ao carregar posts:', error)
  } finally {
    isLoading.value = false
  }
}

// Observar mudanças na categoria selecionada para filtrar posts
watch(() => settings.filterCategory, async (newValue) => {
  if (settings.contentType === 'posts') {
    if (newValue && newValue !== 'all') {
      await loadPosts(newValue)
    } else {
      await loadPosts()
    }
  }
})

// Observar mudanças no tipo de conteúdo
watch(() => settings.contentType, async (newValue) => {
  if (newValue === 'posts') {
    await loadPosts(settings.filterCategory !== 'all' ? settings.filterCategory : null)
  }
})

// Carregar dados iniciais
onMounted(async () => {
  // Mesclar as configurações existentes com as novas
  Object.assign(settings, props.component?.props || {})
  
  // Garantir que allowNesting seja sempre um booleano
  settings.allowNesting = settings.allowNesting === true
  console.log('ComponentSettings - allowNesting inicializado como:', settings.allowNesting)
  
  // Garantir que selectedCategories seja sempre um array
  if (!Array.isArray(settings.selectedCategories)) {
    console.log('ComponentSettings - selectedCategories não é um array, inicializando:', settings.selectedCategories)
    settings.selectedCategories = []
  } else {
    console.log('ComponentSettings - selectedCategories inicializado como:', settings.selectedCategories)
  }
  
  await loadCategories()
  if (settings.contentType === 'posts') {
    await loadPosts(settings.filterCategory !== 'all' ? settings.filterCategory : null)
  }
})

// Métodos para gerenciar botões de ação
const addAction = () => {
  if (!settings.actions) {
    settings.actions = []
  }
  settings.actions.push({
    text: 'Novo Botão',
    url: '#',
    primary: false
  })
}

const removeAction = (index) => {
  settings.actions.splice(index, 1)
}

// Métodos para gerenciar benefícios
const addBenefit = () => {
  if (!settings.items) {
    settings.items = []
  }
  settings.items.push({
    icon: 'Clock',
    title: 'Novo Benefício',
    description: 'Descrição do benefício',
    link: {
      text: 'Ver mais',
      url: '#'
    }
  })
}

const removeBenefit = (index) => {
  settings.items.splice(index, 1)
}

// Métodos para gerenciar campos do simulador
const addField = () => {
  if (!settings.fields) {
    settings.fields = []
  }
  settings.fields.push({
    type: 'text',
    name: 'novo_campo',
    label: 'Novo Campo'
  })
}

const removeField = (index) => {
  settings.fields.splice(index, 1)
}

// Métodos para gerenciar canais de atendimento
const addChannel = () => {
  if (!settings.channels) {
    settings.channels = []
  }
  settings.channels.push({
    icon: 'Users',
    title: 'Novo Canal',
    description: 'Descrição do canal',
    details: []
  })
}

const removeChannel = (index) => {
  settings.channels.splice(index, 1)
}

const addChannelDetail = (channel) => {
  if (!channel.details) {
    channel.details = []
  }
  channel.details.push({
    type: 'address',
    value: ''
  })
}

const removeChannelDetail = (channel, detailIndex) => {
  channel.details.splice(detailIndex, 1)
}

// Métodos para gerenciar cards
const addCard = () => {
  if (!Array.isArray(settings.cards)) {
    settings.cards = []
  }
  settings.cards.push({
    title: 'Novo Card',
    description: 'Descrição do card',
    icon: 'document',
    link: {
      text: 'Ver mais',
      url: '#'
    }
  })
}

const removeCard = (index) => {
  if (Array.isArray(settings.cards)) {
    settings.cards.splice(index, 1)
  }
}

// Métodos para gerenciar campos do formulário
const addFormField = () => {
  if (!settings.fields) {
    settings.fields = []
  }
  settings.fields.push({
    type: 'text',
    name: 'novo_campo',
    label: 'Novo Campo',
    placeholder: '',
    required: false,
    options: []
  })
}

const removeFormField = (index) => {
  if (settings.fields) {
    settings.fields.splice(index, 1)
  }
}

const addFieldOption = (field) => {
  if (!field.options) {
    field.options = []
  }
  field.options.push({
    label: 'Nova Opção',
    value: field.options.length + 1
  })
}

const removeFieldOption = (field, optionIndex) => {
  if (field.options) {
    field.options.splice(optionIndex, 1)
  }
}

const openMediaLibrary = () => {
  // Implementar integração com biblioteca de mídia
  console.log('Abrir biblioteca de mídia')
}

// Watch para emitir atualizações
watch(settings, (newSettings) => {
  console.log('ComponentSettings - Settings atualizados:', {
    ...newSettings,
    allowNesting: newSettings.allowNesting === true,
  })
  
  // Garantir que allowNesting seja incluído e convertido para booleano
  const updatedSettings = {
    ...newSettings,
    allowNesting: newSettings.allowNesting === true
  }
  
  // Emitir o evento com o ID do componente e as configurações atualizadas
  emit('update', props.component.id, updatedSettings)
}, { deep: true })

// Watch para atualizar quando o componente mudar
watch(() => props.component, (newComponent) => {
  if (newComponent) {
    console.log('ComponentSettings - Novo componente recebido:', newComponent)
    // Manter o estado atual do allowNesting ao mesclar as configurações
    const currentAllowNesting = settings.allowNesting
    Object.assign(settings, {
      ...initializeSettings(),
      ...(newComponent.props || {}),
      allowNesting: newComponent.props?.allowNesting === true
    })
    console.log('ComponentSettings - Settings após atualização com allowNesting:', settings.allowNesting)
  }
}, { deep: true })

// Função para alternar o estado de aninhamento
const toggleNesting = () => {
  console.log('Toggle nesting - Estado anterior:', settings.allowNesting)
  settings.allowNesting = settings.allowNesting !== true
  console.log('Toggle nesting - Novo estado:', settings.allowNesting)
  
  // Emitir atualização imediatamente após a mudança
  emit('update', props.component.id, {
    ...settings,
    allowNesting: settings.allowNesting === true
  })
}
</script>