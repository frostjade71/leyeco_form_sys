/**
 * Homepage JavaScript
 * LEYECO III Forms Management System
 */

document.addEventListener('DOMContentLoaded', function () {

    // Enhanced card interactions
    const formCards = document.querySelectorAll('.form-card');

    formCards.forEach(card => {
        // Make entire card clickable
        card.addEventListener('click', function (e) {
            // Don't trigger if clicking the button directly
            if (!e.target.classList.contains('btn')) {
                const button = this.querySelector('.btn');
                if (button) {
                    window.location.href = button.getAttribute('href');
                }
            }
        });

        // Keyboard navigation support
        card.setAttribute('tabindex', '0');
        card.setAttribute('role', 'link');

        card.addEventListener('keypress', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                const button = this.querySelector('.btn');
                if (button) {
                    window.location.href = button.getAttribute('href');
                }
            }
        });

        // Add ARIA label for accessibility
        const cardTitle = card.querySelector('h3');
        if (cardTitle) {
            card.setAttribute('aria-label', 'Navigate to ' + cardTitle.textContent);
        }
    });

    // Animate stats on scroll (if stats section exists)
    const statsSection = document.querySelector('.stats-section');
    if (statsSection) {
        const statNumbers = document.querySelectorAll('.stat-number');

        const animateStats = () => {
            const sectionTop = statsSection.getBoundingClientRect().top;
            const windowHeight = window.innerHeight;

            if (sectionTop < windowHeight * 0.75) {
                statNumbers.forEach(stat => {
                    const finalValue = parseInt(stat.textContent);
                    animateValue(stat, 0, finalValue, 1500);
                });

                // Remove listener after animation
                window.removeEventListener('scroll', animateStats);
            }
        };

        window.addEventListener('scroll', animateStats);
        animateStats(); // Check on load
    }

    // Animate number counting
    function animateValue(element, start, end, duration) {
        if (element.dataset.animated) return;
        element.dataset.animated = 'true';

        const range = end - start;
        const increment = range / (duration / 16);
        let current = start;

        const timer = setInterval(() => {
            current += increment;
            if (current >= end) {
                element.textContent = end;
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(current);
            }
        }, 16);
    }

    // Add smooth reveal animation for cards on page load
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }, index * 100);
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Initially hide cards for animation
    formCards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        observer.observe(card);
    });
});
