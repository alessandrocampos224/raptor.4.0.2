declare module '@quantum/page-builder/components/PageBuilder.vue' {
  import { DefineComponent } from 'vue';
  
  // Definindo o tipo para as props do PageBuilder
  interface PageBuilderProps {
    modelValue?: any[];
    globalStylesValue?: {
      theme?: string;
      spacing?: string;
      contentWidth?: string;
    };
    // Adicionando as props que estão sendo passadas nos componentes
    breadcrumbs?: any[];
    sections?: any[];
    record?: any;
    config?: any;
    actions?: any[];
  }
  
  const component: DefineComponent<PageBuilderProps>;
  export default component;
} 