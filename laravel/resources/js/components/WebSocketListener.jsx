import React, { useEffect, useState } from 'react';

const WebSocketListener = () => {
    const [billsHtml, setBillsHtml] = useState('');

    // Функция обновления списка (заменяет содержимое #bills-list)
    const refreshBills = () => {
        // Сохраняем текущие параметры URL (фильтры, пагинация)
        const url = window.location.pathname + window.location.search;
        fetch(url)
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newList = doc.getElementById('bills-list');
                if (newList) {
                    setBillsHtml(newList.innerHTML);
                } else {
                    console.warn('bills-list not found in response, reloading page');
                    window.location.reload();
                }
            })
            .catch(error => console.error('Failed to refresh bills:', error));
    };

    // Подключение WebSocket при монтировании
    useEffect(() => {
        const ws = new WebSocket('wss://api.democracy-overhaul.bagaev.ai-info.ru/ws');

        ws.onopen = () => {
            console.log('WebSocket connected (React)');
            // При первом подключении можно сразу обновить список (на случай, если были изменения)
            refreshBills();
        };

        ws.onmessage = (event) => {
            try {
                const data = JSON.parse(event.data);
                console.log('WebSocket message (React):', data);
                refreshBills();
            } catch (e) {
                console.error('Failed to parse WebSocket message:', e);
            }
        };

        ws.onclose = () => {
            console.log('WebSocket disconnected (React)');
            // Попытка переподключения через 3 секунды (как в методичке)
            setTimeout(() => {
                console.log('Reconnecting WebSocket...');
                // Здесь можно вызвать повторное создание ws, но проще перезагрузить страницу или использовать библиотеку-реконнект
                // Мы используем простой подход: при закрытии пробуем переподключиться снова (рекурсивно)
                // Но для простоты оставим перезагрузку страницы
                window.location.reload();
            }, 3000);
        };

        // Очистка при размонтировании
        return () => {
            if (ws.readyState === WebSocket.OPEN) {
                ws.close();
            }
        };
    }, []); // пустой массив – эффект сработает один раз

    // Первоначальная загрузка списка (при рендере)
    useEffect(() => {
        refreshBills();
    }, []);

    return (
        <div id="react-bills-list">
            <div dangerouslySetInnerHTML={{ __html: billsHtml }} />
        </div>
    );
};

export default WebSocketListener;