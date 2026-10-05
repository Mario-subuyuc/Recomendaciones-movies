import Chart from 'chart.js/auto';

const canvas = document.getElementById('token-consumption-chart');
if (canvas) {
    const chart = new Chart(canvas, {
        type: 'bar',
        data: {
            labels: ['Películas', 'Videojuegos'],
            datasets: [{ label: 'Tokens académicos', data: [Number(canvas.dataset.peliculas), Number(canvas.dataset.videojuegos)], backgroundColor: ['#435ebe', '#20a080'], borderRadius: 6 }],
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } },
    });
    function theme() {
        const dark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
        for (const scale of ['x', 'y']) {
            chart.options.scales[scale].ticks.color = dark ? '#c8c8da' : '#495057';
            chart.options.scales[scale].grid = { color: dark ? '#343445' : '#e9ecef' };
        }
        chart.update();
    }
    theme();
    new MutationObserver(theme).observe(document.documentElement, { attributes: true, attributeFilter: ['data-bs-theme'] });
}
