# Quantum Page Builder

Um construtor de páginas flexível e poderoso para Laravel, com suporte a aninhamento automático de componentes.

## Recursos

- Arrastar e soltar componentes para construir páginas
- Aninhamento automático de componentes (todos os componentes aceitam aninhamento)
- Posicionamento livre de componentes aninhados
- Redimensionamento de componentes
- Controle de ordem de empilhamento (z-index)
- Configurações avançadas para cada componente
- Suporte a temas claro e escuro
- Integração com Laravel e Inertia.js

## Aninhamento Flexível de Componentes

O Quantum Page Builder permite criar layouts complexos através do aninhamento flexível de componentes. Todos os componentes aceitam aninhamento por padrão, o que significa que você pode posicionar livremente componentes dentro de outros componentes, criando layouts personalizados e avançados.

### Como usar o aninhamento flexível

1. **Adicionar componentes aninhados**:
   - Arraste um componente da barra lateral para dentro da área "Componentes Aninhados" de qualquer componente
   - O componente será adicionado e poderá ser posicionado livremente dentro do componente pai

2. **Posicionar componentes aninhados**:
   - Clique e arraste um componente aninhado para posicioná-lo livremente dentro do componente pai
   - Use as alças de redimensionamento para ajustar o tamanho do componente

3. **Controlar a ordem de empilhamento**:
   - Use os botões "Trazer para frente" e "Enviar para trás" para controlar a ordem de empilhamento dos componentes aninhados

4. **Remover aninhamento**:
   - Use o botão "Desaninhar" para mover um componente aninhado para fora do componente pai

### Dicas para aninhamento eficiente

- Combine componentes de diferentes tipos para criar layouts personalizados
- Use o redimensionamento para ajustar o tamanho dos componentes aninhados
- Controle a ordem de empilhamento para criar efeitos de sobreposição
- Experimente diferentes combinações para encontrar o layout perfeito
- Crie múltiplos níveis de aninhamento para layouts complexos

## Componentes disponíveis

- **Cabeçalho**: Títulos e subtítulos com opções de estilo
- **Conteúdo**: Blocos de texto com formatação
- **Cards**: Grids de cards com diferentes layouts
- **Benefícios**: Destaque os benefícios do seu produto ou serviço
- **Simulador**: Formulários interativos para simulações
- **Canais de Atendimento**: Exiba seus canais de contato
- **Formulário**: Crie formulários personalizados

## Requisitos

- PHP 8.0+
- Laravel 9.0+
- Node.js 14+
- Tailwind CSS 3.0+
- Vue.js 3.0+
- Inertia.js

## Licença

Este projeto está licenciado sob a [MIT License](LICENSE).
