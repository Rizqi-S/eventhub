import Chart from 'chart.js/auto';
window.Chart = Chart;

document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('sidebar');
    document.querySelector('[data-sidebar-toggle]')?.addEventListener('click', (e) => {
        e.stopPropagation();
        sidebar?.classList.toggle('open');
    });
    document.addEventListener('click', (event) => {
        if (window.innerWidth <= 900 && sidebar?.classList.contains('open') && !sidebar.contains(event.target) && !event.target.closest('[data-sidebar-toggle]')) {
            sidebar.classList.remove('open');
        }
    });
    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm(form.dataset.confirm || 'Lanjutkan tindakan ini?')) {
                event.preventDefault();
            }
        });
    });
});