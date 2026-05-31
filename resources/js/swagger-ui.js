import 'swagger-ui-dist/swagger-ui.css';
import SwaggerUIBundle from 'swagger-ui-dist/swagger-ui-bundle.js';
import SwaggerUIStandalonePreset from 'swagger-ui-dist/swagger-ui-standalone-preset.js';

SwaggerUIBundle({
    url: '/api/documentation/openapi.yaml',
    dom_id: '#swagger-ui',
    deepLinking: true,
    presets: [
        SwaggerUIBundle.presets.apis,
        SwaggerUIStandalonePreset,
    ],
    layout: 'BaseLayout',
    defaultModelRendering: 'model',
    docExpansion: 'list',
    filter: true,
    showExtensions: true,
    showCommonExtensions: true,
    tryItOutEnabled: false,
});
