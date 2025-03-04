<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @routes
        @vite(['resources/js/app.ts'])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
        
        <!-- Browser Console MCP -->
        <script src="http://localhost:3333/browser-client.js"></script>
        
        <!-- Script de teste para o MCP -->
        <script>
            // Gerar alguns logs para teste
            console.log('Teste de log do MCP - ' + new Date().toISOString());
            console.info('Teste de info do MCP - ' + new Date().toISOString());
            console.warn('Teste de aviso do MCP - ' + new Date().toISOString());
            console.error('Teste de erro do MCP - ' + new Date().toISOString());
            
            // Verificar se o MCP está carregado
            if (window.BrowserConsoleMCP) {
                console.log('MCP carregado com sucesso!');
            } else {
                console.error('MCP não foi carregado corretamente!');
            }
        </script>
    </body>
</html>
