import './bootstrap';
import Typed from 'typed.js';

document.querySelector('[data-theme-toggle]')?.addEventListener('click', () => {
    const isDark = document.documentElement.classList.toggle('dark');
    localStorage.theme = isDark ? 'dark' : 'light';
});

const target = document.querySelector('[data-typed]');
const sizer = document.querySelector('[data-typed-sizer]');

if (target && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    if (sizer) {
        sizer.style.visibility = 'hidden';
    }

    new Typed(target, {
        strings: [
            'Når stenger ølsalget og Vinmonopolet før røde dager?',
            'Når stenger ølsalget før påske?',
            'Når stenger Vinmonopolet før 17. mai?',
            'Når stenger ølsalget før jul?',
        ],
        typeSpeed: 22,
        backSpeed: 12,
        backDelay: 2000,
        startDelay: 300,
        loop: true,
        smartBackspace: true,
        showCursor: false,
    });
}
