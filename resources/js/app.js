import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// ── Scroll Reveal ──────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(el => {
            if (el.isIntersecting) {
                el.target.classList.add('visible');
                observer.unobserve(el.target);
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
});

// ── Smooth Scroll untuk anchor links ──────────────────────────────────────
document.addEventListener('click', (e) => {
    const link = e.target.closest('a[href^="#"]');
    if (!link) return;
    const target = document.querySelector(link.getAttribute('href'));
    if (!target) return;
    e.preventDefault();
    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
});

// ── Page Transitions (View Transitions API) ────────────────────────────────
document.addEventListener('click', (e) => {
    const link = e.target.closest('a[href]');
    if (!link) return;

    const href = link.getAttribute('href');
    // Skip: external, hash, javascript, download, target blank
    if (!href || href.startsWith('#') || href.startsWith('javascript') ||
        href.startsWith('http') || link.hasAttribute('download') ||
        link.getAttribute('target') === '_blank') return;

    // Skip form-related links
    if (link.closest('form')) return;

    if (!document.startViewTransition) return; // fallback: browser tidak support

    e.preventDefault();
    document.startViewTransition(() => {
        window.location.href = href;
    });
});
