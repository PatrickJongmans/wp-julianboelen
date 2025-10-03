/**
 * Main JavaScript file for TF Theme
 * 
 * @package JulianboelenTheme
 * @version 1.0.0
 */

(function($) {
    'use strict';

    // Document ready
    $(document).ready(function() {
        
        // Mobile menu toggle
        $('.mobile-menu-toggle').on('click', function() {
            $('.mobile-navigation').toggleClass('hidden');
            
            // Toggle aria-expanded attribute
            const isExpanded = $(this).attr('aria-expanded') === 'true';
            $(this).attr('aria-expanded', !isExpanded);
            
            // Toggle menu icon
            const icon = $(this).find('svg');
            if ($('.mobile-navigation').hasClass('hidden')) {
                icon.html('<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>');
            } else {
                icon.html('<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>');
            }
        });

        // Close mobile menu when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.mobile-menu-toggle, .mobile-navigation').length) {
                $('.mobile-navigation').addClass('hidden');
                $('.mobile-menu-toggle').attr('aria-expanded', 'false');
                $('.mobile-menu-toggle svg').html('<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>');
            }
        });

        // Smooth scrolling for anchor links
        $('a[href*="#"]:not([href="#"])').on('click', function() {
            if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
                var target = $(this.hash);
                target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
                if (target.length) {
                    $('html, body').animate({
                        scrollTop: target.offset().top - 80
                    }, 1000);
                    return false;
                }
            }
        });

        // Add loaded class to body when everything is loaded
        $(window).on('load', function() {
            $('body').addClass('loaded');
        });

        // Responsive tables
        $('.entry-content table').wrap('<div class="table-responsive"></div>');

        // Skip link focus fix
        $('.skip-link').on('click', function() {
            var target = $(this.hash);
            if (target.length) {
                target.attr('tabindex', '-1').focus();
            }
        });

    });

    // Window resize handler
    $(window).on('resize', function() {
        // Close mobile menu on resize
        if ($(window).width() >= 1024) {
            $('.mobile-navigation').addClass('hidden');
            $('.mobile-menu-toggle').attr('aria-expanded', 'false');
        }
    });

    // Utility functions
    window.JulianboelenTheme = {
        
        // Initialize theme features
        init: function() {
            this.initLazyLoading();
            this.initAccessibility();
        },

        // Lazy loading for images
        initLazyLoading: function() {
            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            img.src = img.dataset.src;
                            img.classList.remove('lazy');
                            imageObserver.unobserve(img);
                        }
                    });
                });

                document.querySelectorAll('img[data-src]').forEach(img => {
                    imageObserver.observe(img);
                });
            }
        },

        // Accessibility enhancements
        initAccessibility: function() {
            // Add aria-label to menu items with children
            $('.menu-item-has-children > a').each(function() {
                $(this).attr('aria-haspopup', 'true');
                $(this).attr('aria-expanded', 'false');
            });

            // Handle submenu keyboard navigation
            $('.menu-item-has-children > a').on('keydown', function(e) {
                if (e.which === 13 || e.which === 32) { // Enter or Space
                    e.preventDefault();
                    $(this).next('.sub-menu').toggleClass('show');
                    const isExpanded = $(this).attr('aria-expanded') === 'true';
                    $(this).attr('aria-expanded', !isExpanded);
                }
            });
        }
    };

    // Initialize theme on document ready
    $(document).ready(function() {
        JulianboelenTheme.init();
    });

})(jQuery);