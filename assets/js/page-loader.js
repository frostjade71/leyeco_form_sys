/**
 * LEYECO III Forms Management System
 * Global Page Loader Script
 * 
 * This script handles the page loading state transitions
 * showing a loader when navigating between pages
 */

(function() {
    'use strict';

    // Hide loader when page is fully loaded
    window.addEventListener('load', function() {
        const loader = document.getElementById('page-loader');
        if (loader) {
            // Add a small delay for smooth transition
            setTimeout(function() {
                loader.classList.add('hidden');
            }, 300);
        }
    });

    // Show loader when navigating to another page
    document.addEventListener('DOMContentLoaded', function() {
        // Get all internal links
        const links = document.querySelectorAll('a[href]');
        
        links.forEach(function(link) {
            // Skip if it's an external link, anchor link, or has specific data attribute to skip
            const href = link.getAttribute('href');
            
            // Skip external links, anchors, javascript:, mailto:, tel:, etc.
            if (!href || 
                href.startsWith('#') || 
                href.startsWith('javascript:') || 
                href.startsWith('mailto:') || 
                href.startsWith('tel:') ||
                link.hasAttribute('data-no-loader') ||
                link.target === '_blank' ||
                href.startsWith('http') && !href.includes(window.location.hostname)) {
                return;
            }

            // Add click event to show loader
            link.addEventListener('click', function(e) {
                // Don't show loader if ctrl/cmd key is pressed (new tab)
                if (e.ctrlKey || e.metaKey) {
                    return;
                }

                const loader = document.getElementById('page-loader');
                if (loader) {
                    loader.classList.remove('hidden');
                }
            });
        });

        // Handle form submissions
        const forms = document.querySelectorAll('form');
        forms.forEach(function(form) {
            // Skip forms with data-no-loader attribute or AJAX forms
            if (form.hasAttribute('data-no-loader') || form.hasAttribute('data-ajax')) {
                return;
            }

            form.addEventListener('submit', function() {
                const loader = document.getElementById('page-loader');
                if (loader) {
                    loader.classList.remove('hidden');
                }
            });
        });

        // Handle browser back/forward buttons
        window.addEventListener('pageshow', function(event) {
            const loader = document.getElementById('page-loader');
            if (loader && event.persisted) {
                // Page was loaded from cache (back/forward button)
                loader.classList.add('hidden');
            }
        });
    });

    // Fallback: Hide loader if it's still visible after 10 seconds
    setTimeout(function() {
        const loader = document.getElementById('page-loader');
        if (loader && !loader.classList.contains('hidden')) {
            console.warn('Page loader timeout - forcing hide');
            loader.classList.add('hidden');
        }
    }, 10000);
})();
