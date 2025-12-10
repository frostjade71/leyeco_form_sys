/**
 * LEYECO III Forms Management System
 * Common JavaScript Functions
 */

document.addEventListener('DOMContentLoaded', function () {
    // Mobile Menu Toggle
    const navbarToggle = document.querySelector('.navbar-toggle');
    const navbarMenu = document.querySelector('.navbar-menu');
    const mobileOverlay = document.querySelector('.mobile-menu-overlay');

    if (navbarToggle) {
        navbarToggle.addEventListener('click', function () {
            navbarToggle.classList.toggle('active');
            navbarMenu.classList.toggle('active');

            // Toggle overlay
            if (mobileOverlay) {
                mobileOverlay.classList.toggle('active');
            }
        });
    }

    // Close mobile menu when clicking overlay
    if (mobileOverlay) {
        mobileOverlay.addEventListener('click', function () {
            navbarToggle.classList.remove('active');
            navbarMenu.classList.remove('active');
            mobileOverlay.classList.remove('active');
        });
    }

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href !== '#' && href !== '#!') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });

    // Close mobile menu when clicking nav links
    const navLinks = document.querySelectorAll('.navbar-nav a');
    navLinks.forEach(link => {
        link.addEventListener('click', function () {
            if (window.innerWidth <= 992) {
                navbarToggle.classList.remove('active');
                navbarMenu.classList.remove('active');
                if (mobileOverlay) {
                    mobileOverlay.classList.remove('active');
                }
            }
        });
    });
});
