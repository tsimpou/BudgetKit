import './bootstrap';
import * as Turbo from '@hotwired/turbo';
import Alpine from 'alpinejs';
import './stores';

window.Alpine = Alpine;
window.Turbo = Turbo;

Alpine.start();

const NAV_TAB_URLS = ['/', '/budget', '/transactions', '/stats'];

function initPageComponents() {
    if (document.querySelector('#mapOne')) {
        import('./components/map').then(module => module.initMap());
    }

    if (document.querySelector('#chartOne')) {
        import('./components/chart/chart-1').then(module => module.initChartOne());
    }
    if (document.querySelector('#chartTwo')) {
        import('./components/chart/chart-2').then(module => module.initChartTwo());
    }
    if (document.querySelector('#chartThree')) {
        import('./components/chart/chart-3').then(module => module.initChartThree());
    }
    if (document.querySelector('#chartSix')) {
        import('./components/chart/chart-6').then(module => module.initChartSix());
    }
    if (document.querySelector('#chartEight')) {
        import('./components/chart/chart-8').then(module => module.initChartEight());
    }
    if (document.querySelector('#chartThirteen')) {
        import('./components/chart/chart-13').then(module => module.initChartThirteen());
    }

    if (document.querySelector('#calendar')) {
        import('./components/calendar-init').then(module => module.calendarInit());
    }

    if (document.querySelector('#donutChart') || document.querySelector('#trendChart')) {
        import('./components/stats-charts').then(module => module.initStatsCharts());
    }
}

function updateMobileNav() {
    const path = window.location.pathname;

    document.querySelectorAll('[data-mobile-nav]').forEach((link) => {
        const href = link.getAttribute('href') ?? '';
        const linkPath = new URL(href, window.location.origin).pathname;
        const prefix = link.dataset.mobileNavPrefix;
        const active = prefix ? path.startsWith(prefix) : path === linkPath;

        link.classList.toggle('text-[#667eea]', active);
        link.classList.toggle('text-gray-400', !active);
    });
}

function showTurboLoading() {
    document.getElementById('turbo-loading')?.classList.remove('hidden');
}

function hideTurboLoading() {
    document.getElementById('turbo-loading')?.classList.add('hidden');
}

function prefetchNavTabs() {
    const current = window.location.pathname;

    NAV_TAB_URLS.forEach((url) => {
        if (url !== current) {
            Turbo.visit(url, { action: 'prefetch' });
        }
    });
}

document.addEventListener('turbo:load', () => {
    Alpine.initTree(document.body);
    initPageComponents();
    updateMobileNav();
    hideTurboLoading();

    if (document.getElementById('mobile-bottom-nav')) {
        setTimeout(prefetchNavTabs, 300);
    }
});

document.addEventListener('DOMContentLoaded', () => {
    initPageComponents();
    updateMobileNav();
});

document.addEventListener('turbo:before-fetch-request', () => {
    if (document.getElementById('mobile-bottom-nav')) {
        showTurboLoading();
    }
});

document.addEventListener('turbo:frame-render', hideTurboLoading);
document.addEventListener('turbo:fetch-request-error', hideTurboLoading);
document.addEventListener('turbo:visit', (event) => {
    if (event.detail?.action === 'prefetch') {
        return;
    }
    if (document.getElementById('mobile-bottom-nav')) {
        showTurboLoading();
    }
});

document.addEventListener('touchstart', (event) => {
    const link = event.target.closest('a[data-turbo-prefetch]');
    if (link?.href) {
        Turbo.visit(link.href, { action: 'prefetch' });
    }
}, { passive: true });
