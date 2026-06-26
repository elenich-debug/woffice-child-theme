// Создаем новый скрипт для автоматического нажатия кнопки
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM загружен, ищем кнопку для автоматического нажатия...');
    
    // Функция для автоматического нажатия кнопки
    function autoClickOptimizeButton() {
        const buttons = document.querySelectorAll('.custom-update-btn');
        
        if (buttons.length > 0) {
            console.log('Кнопка найдена, имитируем клик через 2 секунды...');
            
            // Задержка перед кликом
            setTimeout(function() {
                console.log('Выполняем автоматический клик!');
                // Имитируем клик по первой найденной кнопке
                buttons[0].click();
            }, 2000); // Задержка 2 секунды
        } else {
            console.log('Кнопки оптимизации не найдены на странице');
        }
    }
    
    // Запускаем функцию автоматического клика
    autoClickOptimizeButton();
});