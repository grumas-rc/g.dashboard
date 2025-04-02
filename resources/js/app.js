import 'bootstrap';
import Chart from 'chart.js/auto';
import 'chartjs-adapter-date-fns';

window.Chart = Chart;
import './bootstrap';

const llmItems = document.querySelectorAll('.llm-item');
if (llmItems.length > 0) {
    const showMoreBtn = document.getElementById('showMoreBtn');
    const hideMoreBtn = document.getElementById('hideMoreBtn');

// Показываем первые 5 элементов по умолчанию
    llmItems.forEach((item, index) => {
        if (index < 5) {
            item.style.display = 'flex';
        }
    });

// Обработчик клика на кнопку "Показать всё"
    showMoreBtn.addEventListener('click', function () {
        llmItems.forEach(item => {
            item.style.display = 'flex'; // Показываем все элементы
        });
        showMoreBtn.style.display = 'none'; // Скрываем кнопку "Показать всё"
        hideMoreBtn.style.display = 'inline'; // Показываем кнопку "Скрыть"
    });

// Обработчик клика на кнопку "Скрыть"
    hideMoreBtn.addEventListener('click', function () {
        llmItems.forEach((item, index) => {
            if (index >= 5) {
                item.style.display = 'none'; // Скрываем элементы, начиная с 5-го
            }
        });
        hideMoreBtn.style.display = 'none'; // Скрываем кнопку "Скрыть"
        showMoreBtn.style.display = 'inline'; // Показываем кнопку "Показать всё"
    });
}
