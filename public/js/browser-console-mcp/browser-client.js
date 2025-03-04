/**
 * Browser Console MCP - Cliente JavaScript
 * 
 * Este script captura logs do console, requisições de rede e outros eventos
 * do navegador e os envia para o servidor MCP.
 */

(function() {
  // Configuração
  const MCP_SERVER_URL = 'http://localhost:3333';
  const BATCH_SIZE = 10; // Número de logs para enviar em cada lote
  const SEND_INTERVAL = 2000; // Intervalo para enviar logs (ms)
  
  // Armazenamento de logs
  let consoleLogsBuffer = [];
  let networkRequestsBuffer = [];
  let isConnected = false;
  
  // Verificar conexão com o servidor
  function checkConnection() {
    fetch(`${MCP_SERVER_URL}/health`)
      .then(response => {
        if (response.ok) {
          isConnected = true;
          console.log('%c[Browser Console MCP] Conectado ao servidor', 'color: green');
        } else {
          isConnected = false;
          console.warn('%c[Browser Console MCP] Servidor respondeu com erro', 'color: orange');
        }
      })
      .catch(error => {
        isConnected = false;
        console.error('%c[Browser Console MCP] Não foi possível conectar ao servidor', 'color: red');
      });
  }
  
  // Enviar logs para o servidor
  function sendLogs(type, logs) {
    if (!isConnected || logs.length === 0) return;
    
    const endpoint = type === 'console' ? '/api/logs' : '/api/network';
    
    fetch(`${MCP_SERVER_URL}${endpoint}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(logs)
    })
    .then(response => {
      if (response.ok) {
        // Limpar buffer apenas se o envio for bem-sucedido
        if (type === 'console') {
          consoleLogsBuffer = [];
        } else {
          networkRequestsBuffer = [];
        }
      }
    })
    .catch(error => {
      console.error('%c[Browser Console MCP] Erro ao enviar logs:', 'color: red', error);
    });
  }
  
  // Interceptar logs do console
  function interceptConsoleLogs() {
    const originalConsole = {
      log: console.log,
      info: console.info,
      warn: console.warn,
      error: console.error,
      debug: console.debug
    };
    
    // Função para interceptar chamadas do console
    function intercept(method) {
      return function() {
        // Chamar a função original
        originalConsole[method].apply(console, arguments);
        
        // Capturar argumentos
        const args = Array.from(arguments).map(arg => {
          try {
            // Tentar converter objetos para string
            if (typeof arg === 'object') {
              return JSON.stringify(arg);
            }
            return String(arg);
          } catch (e) {
            return '[Objeto não serializável]';
          }
        });
        
        // Criar log
        const log = {
          type: method,
          message: args.join(' '),
          timestamp: new Date().toISOString(),
          source: window.location.href,
          stack: new Error().stack
        };
        
        // Adicionar ao buffer
        consoleLogsBuffer.push(log);
        
        // Enviar imediatamente se for erro
        if (method === 'error' && isConnected) {
          sendLogs('console', [log]);
        }
      };
    }
    
    // Substituir métodos do console
    console.log = intercept('log');
    console.info = intercept('info');
    console.warn = intercept('warn');
    console.error = intercept('error');
    console.debug = intercept('debug');
  }
  
  // Interceptar requisições de rede (fetch)
  function interceptFetch() {
    const originalFetch = window.fetch;
    
    window.fetch = function() {
      const startTime = Date.now();
      const url = arguments[0];
      const options = arguments[1] || {};
      
      // Criar objeto de requisição
      const request = {
        url: typeof url === 'string' ? url : url.url,
        method: options.method || 'GET',
        headers: options.headers || {},
        body: options.body,
        timestamp: new Date().toISOString(),
        startTime: startTime
      };
      
      // Chamar fetch original
      return originalFetch.apply(this, arguments)
        .then(response => {
          // Adicionar informações da resposta
          const responseTime = Date.now() - startTime;
          const networkRequest = {
            ...request,
            status: response.status,
            statusText: response.statusText,
            responseTime: responseTime,
            type: 'fetch'
          };
          
          // Adicionar ao buffer
          networkRequestsBuffer.push(networkRequest);
          
          return response;
        })
        .catch(error => {
          // Adicionar informações de erro
          const responseTime = Date.now() - startTime;
          const networkRequest = {
            ...request,
            error: error.message,
            responseTime: responseTime,
            type: 'fetch'
          };
          
          // Adicionar ao buffer
          networkRequestsBuffer.push(networkRequest);
          
          throw error;
        });
    };
  }
  
  // Interceptar XMLHttpRequest
  function interceptXHR() {
    const originalXHR = window.XMLHttpRequest;
    
    function CustomXHR() {
      const xhr = new originalXHR();
      const request = {
        type: 'xhr',
        timestamp: new Date().toISOString(),
        startTime: Date.now()
      };
      
      // Interceptar método open
      const originalOpen = xhr.open;
      xhr.open = function() {
        request.method = arguments[0];
        request.url = arguments[1];
        return originalOpen.apply(xhr, arguments);
      };
      
      // Interceptar método send
      const originalSend = xhr.send;
      xhr.send = function() {
        request.body = arguments[0];
        return originalSend.apply(xhr, arguments);
      };
      
      // Interceptar onreadystatechange
      const originalStateChange = xhr.onreadystatechange;
      xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
          // Requisição completa
          request.status = xhr.status;
          request.statusText = xhr.statusText;
          request.responseTime = Date.now() - request.startTime;
          request.responseHeaders = xhr.getAllResponseHeaders();
          
          // Adicionar ao buffer
          networkRequestsBuffer.push(request);
        }
        
        if (originalStateChange) {
          originalStateChange.apply(xhr, arguments);
        }
      };
      
      return xhr;
    }
    
    window.XMLHttpRequest = CustomXHR;
  }
  
  // Capturar erros não tratados
  function captureErrors() {
    window.addEventListener('error', function(event) {
      const error = {
        type: 'error',
        message: event.message,
        source: event.filename,
        lineno: event.lineno,
        colno: event.colno,
        stack: event.error ? event.error.stack : null,
        timestamp: new Date().toISOString()
      };
      
      consoleLogsBuffer.push(error);
      
      // Enviar imediatamente se conectado
      if (isConnected) {
        sendLogs('console', [error]);
      }
    });
    
    window.addEventListener('unhandledrejection', function(event) {
      const error = {
        type: 'error',
        message: `Promessa rejeitada: ${event.reason}`,
        stack: event.reason && event.reason.stack ? event.reason.stack : null,
        timestamp: new Date().toISOString(),
        source: window.location.href
      };
      
      consoleLogsBuffer.push(error);
      
      // Enviar imediatamente se conectado
      if (isConnected) {
        sendLogs('console', [error]);
      }
    });
  }
  
  // Configurar envio periódico de logs
  function setupPeriodicSending() {
    setInterval(() => {
      // Enviar logs do console
      if (consoleLogsBuffer.length > 0) {
        const batch = consoleLogsBuffer.slice(0, BATCH_SIZE);
        sendLogs('console', batch);
      }
      
      // Enviar requisições de rede
      if (networkRequestsBuffer.length > 0) {
        const batch = networkRequestsBuffer.slice(0, BATCH_SIZE);
        sendLogs('network', batch);
      }
    }, SEND_INTERVAL);
  }
  
  // Função para capturar screenshot
  function captureScreenshot() {
    return new Promise((resolve, reject) => {
      try {
        const canvas = document.createElement('canvas');
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
        const ctx = canvas.getContext('2d');
        
        // Criar uma imagem do HTML atual
        const data = `<svg xmlns="http://www.w3.org/2000/svg" width="${window.innerWidth}" height="${window.innerHeight}">
          <foreignObject width="100%" height="100%">
            <div xmlns="http://www.w3.org/1999/xhtml">
              ${document.documentElement.outerHTML}
            </div>
          </foreignObject>
        </svg>`;
        
        const img = new Image();
        const svg = new Blob([data], { type: 'image/svg+xml' });
        const url = URL.createObjectURL(svg);
        
        img.onload = function() {
          ctx.drawImage(img, 0, 0);
          URL.revokeObjectURL(url);
          
          // Converter para base64
          const screenshot = canvas.toDataURL('image/png');
          resolve(screenshot);
        };
        
        img.onerror = function() {
          URL.revokeObjectURL(url);
          reject(new Error('Falha ao capturar screenshot'));
        };
        
        img.src = url;
      } catch (error) {
        reject(error);
      }
    });
  }
  
  // Expor API para o MCP
  window.BrowserConsoleMCP = {
    // Limpar todos os logs armazenados
    clearLogs: function() {
      consoleLogsBuffer = [];
      networkRequestsBuffer = [];
      
      if (isConnected) {
        fetch(`${MCP_SERVER_URL}/api/clear`, {
          method: 'POST'
        }).catch(error => {
          console.error('%c[Browser Console MCP] Erro ao limpar logs:', 'color: red', error);
        });
      }
      
      return true;
    },
    
    // Executar código JavaScript
    executeCode: function(code) {
      try {
        return eval(code);
      } catch (error) {
        console.error('%c[Browser Console MCP] Erro ao executar código:', 'color: red', error);
        return { error: error.message };
      }
    },
    
    // Capturar screenshot
    takeScreenshot: function() {
      return captureScreenshot()
        .then(screenshot => {
          if (isConnected) {
            return fetch(`${MCP_SERVER_URL}/api/screenshot`, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json'
              },
              body: JSON.stringify({ screenshot })
            })
            .then(response => response.json());
          }
          return { screenshot };
        })
        .catch(error => {
          console.error('%c[Browser Console MCP] Erro ao capturar screenshot:', 'color: red', error);
          return { error: error.message };
        });
    }
  };
  
  // Inicializar
  function init() {
    console.log('%c[Browser Console MCP] Inicializando...', 'color: blue');
    
    // Verificar conexão com o servidor
    checkConnection();
    setInterval(checkConnection, 30000); // Verificar a cada 30 segundos
    
    // Interceptar logs e requisições
    interceptConsoleLogs();
    interceptFetch();
    interceptXHR();
    captureErrors();
    
    // Configurar envio periódico
    setupPeriodicSending();
    
    console.log('%c[Browser Console MCP] Inicializado com sucesso!', 'color: green');
  }
  
  // Iniciar quando o DOM estiver pronto
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})(); 