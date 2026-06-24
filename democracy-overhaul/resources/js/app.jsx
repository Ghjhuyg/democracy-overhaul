import React from 'react';
import ReactDOM from 'react-dom/client';
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import WebSocketListener from './components/WebSocketListener';

// 1. Инициализация Alpine.js с плагином collapse
Alpine.plugin(collapse);
window.Alpine = Alpine;
Alpine.start();

// 2. React-приложение (если есть контейнер)
console.log('React app starting');
const container = document.getElementById('react-root');
if (container) {
    const root = ReactDOM.createRoot(container);
    root.render(<WebSocketListener />);
}