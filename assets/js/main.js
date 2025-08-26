/**
 * CT Storefront Starter - Main JavaScript
 * Handles AJAX product filtering, mobile menu, and accessibility features
 */

(function($) {
    'use strict';

    // Performance: Debounce function
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Accessibility: Focus management
    function trapFocus(element) {
        const focusableElements = element.querySelectorAll(
            'a[href], button, textarea, input[type="text"], input[type="radio"], input[type="checkbox"], select'
        );
        const firstFocusableElement = focusableElements[0];
        const lastFocusableElement = focusableElements[focusableElements.length - 1];

        element.addEventListener('keydown', function(e) {
            if (e.key === 'Tab') {
                if (e.shiftKey) {
                    if (document.activeElement === firstFocusableElement) {
                        e.preventDefault();
                        lastFocusableElement.focus();
                    }
                } else {
                    if (document.activeElement === lastFocusableElement) {
                        e.preventDefault();
                        firstFocusableElement.focus();
                    }
                }
            }
        });
    }

    // Initialize when DOM is ready
    $(document).ready(function() {
        initMobileMenu();
        initProductFilters();
        initAccessibility();
        initPerformanceOptimizations();
        
        // Load initial products
        loadInitialProducts();
    });

    /**
     * Mobile Menu Functionality
     */
    function initMobileMenu() {
        const $mobileToggle = $('#mobile-menu-toggle');
        const $navigation = $('#site-navigation');
        const $body = $('body');

        if (!$mobileToggle.length) return;

        $mobileToggle.on('click', function(e) {
            e.preventDefault();
            const isExpanded = $mobileToggle.attr('aria-expanded') === 'true';
            
            $mobileToggle.attr('aria-expanded', !isExpanded);
            $navigation.toggleClass('mobile-active');
            $body.toggleClass('menu-open');
            
            // Accessibility: Announce menu state
            const announcement = isExpanded ? 'Menu closed' : 'Menu opened';
            announceToScreenReader(announcement);
            
            // Focus management
            if (!isExpanded) {
                $navigation.find('a').first().focus();
                trapFocus($navigation[0]);
            }
        });

        // Close menu on escape key
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && $navigation.hasClass('mobile-active')) {
                $mobileToggle.click();
            }
        });

        // Close menu when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#site-navigation, #mobile-menu-toggle').length) {
                if ($navigation.hasClass('mobile-active')) {
                    $mobileToggle.click();
                }
            }
        });
    }

    /**
     * Product Filtering with AJAX
     */
    function initProductFilters() {
        const $filters = $('#category-filter, #price-min, #price-max, #rating-filter, #sort-filter');
        const $clearFilters = $('#clear-filters');
        const $productsGrid = $('#products-grid');
        const $loading = $('#loading');
        const $noResults = $('#no-results');
        
        let filterTimeout;
        let currentRequest = null;

        // Debounced filter function for performance
        const debouncedFilter = debounce(filterProducts, 300);

        $filters.on('change keyup', function() {
            debouncedFilter();
        });

        $clearFilters.on('click', function(e) {
            e.preventDefault();
            $filters.val('');
            filterProducts();
            
            // Accessibility: Announce filter cleared
            announceToScreenReader('All filters cleared');
        });

        function filterProducts() {
            // Cancel previous request if still pending
            if (currentRequest) {
                currentRequest.abort();
            }

            const filterData = {
                action: 'ct_filter_products',
                category: $('#category-filter').val(),
                price_min: $('#price-min').val(),
                price_max: $('#price-max').val(),
                rating: $('#rating-filter').val(),
                sort: $('#sort-filter').val(),
                nonce: ct_ajax.nonce
            };

            // Show loading state
            $productsGrid.hide();
            $loading.show();
            $noResults.hide();

            // Track CTA click for analytics
            trackCTA('filter_applied', filterData);

            currentRequest = $.ajax({
                url: ct_ajax.ajax_url,
                type: 'POST',
                data: filterData,
                timeout: 10000, // 10 second timeout
                success: function(response) {
                    $loading.hide();
                    
                    if (response.success && response.data) {
                        const data = response.data;
                        
                        if (data.count > 0) {
                            $productsGrid.html(data.html).fadeIn();
                            
                            // Add fade-in animation to new products
                            $productsGrid.find('.product-card').addClass('fade-in');
                            
                            // Accessibility: Announce results
                            announceToScreenReader(`${data.count} products found`);
                            
                            // Update URL for bookmarking (without page reload)
                            updateURL(filterData);
                        } else {
                            $productsGrid.hide();
                            $noResults.show();
                            announceToScreenReader('No products found');
                        }
                    } else {
                        handleError('Invalid response from server');
                    }
                },
                error: function(xhr, status, error) {
                    $loading.hide();
                    
                    if (status !== 'abort') {
                        handleError('Failed to load products. Please try again.');
                        console.error('AJAX Error:', error);
                    }
                }
            });
        }

        // Handle form submission for non-JS fallback
        $('#filters-form').on('submit', function(e) {
            if (!window.fetch) {
                // Allow form submission for older browsers
                return true;
            }
            e.preventDefault();
            filterProducts();
        });
    }

    /**
     * Load Initial Products
     */
    function loadInitialProducts() {
        const $productsGrid = $('#products-grid');
        
        if ($productsGrid.length && $productsGrid.is(':empty')) {
            // Load initial products on page load
            setTimeout(function() {
                $('#category-filter').trigger('change');
            }, 100);
        }
    }

    /**
     * Add to Cart Functionality
     */
    $(document).on('click', '.add-to-cart, .add-to-cart-large', function(e) {
        e.preventDefault();
        
        const $button = $(this);
        const productId = $button.data('product-id');
        const productName = $button.closest('.product-card, .product-single').find('.product-title').text();
        
        // Visual feedback
        $button.addClass('added').text('Added!');
        
        // Track CTA click
        trackCTA('add_to_cart', {
            product_id: productId,
            product_name: productName
        });
        
        // Accessibility: Announce action
        announceToScreenReader(`${productName} added to cart`);
        
        // Reset button after delay
        setTimeout(function() {
            $button.removeClass('added').text('Add to Cart');
        }, 2000);
    });

    /**
     * Accessibility Features
     */
    function initAccessibility() {
        // Add focus-visible polyfill
        if (!window.focusVisible) {
            document.body.classList.add('js-focus-visible');
        }

        // Skip link functionality
        $('.skip-link').on('click', function(e) {
            e.preventDefault();
            const target = $($(this).attr('href'));
            if (target.length) {
                target.attr('tabindex', '-1').focus();
                announceToScreenReader('Skipped to main content');
            }
        });

        // Announce dynamic content changes
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                    // Check for new products loaded
                    const newProducts = $(mutation.addedNodes).find('.product-card');
                    if (newProducts.length > 0) {
                        announceToScreenReader(`${newProducts.length} new products loaded`);
                    }
                }
            });
        });

        // Observe products grid for changes
        const productsGrid = document.getElementById('products-grid');
        if (productsGrid) {
            observer.observe(productsGrid, { childList: true, subtree: true });
        }
    }

    /**
     * Performance Optimizations
     */
    function initPerformanceOptimizations() {
        // Lazy load images
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });

            document.querySelectorAll('img[data-src]').forEach(function(img) {
                imageObserver.observe(img);
            });
        }

        // Preload critical resources
        const criticalResources = [
            ct_ajax.ajax_url,
            window.location.origin + '/wp-content/themes/ct-storefront/assets/js/main.js'
        ];

        criticalResources.forEach(function(url) {
            const link = document.createElement('link');
            link.rel = 'preload';
            link.href = url;
            link.as = 'script';
            document.head.appendChild(link);
        });
    }

    /**
     * Utility Functions
     */
    function announceToScreenReader(message) {
        const announcement = document.createElement('div');
        announcement.setAttribute('aria-live', 'polite');
        announcement.setAttribute('aria-atomic', 'true');
        announcement.className = 'sr-only';
        announcement.textContent = message;
        
        document.body.appendChild(announcement);
        
        setTimeout(function() {
            document.body.removeChild(announcement);
        }, 1000);
    }

    function handleError(message) {
        const $noResults = $('#no-results');
        $noResults.html(`<h3>Error</h3><p>${message}</p>`).show();
        announceToScreenReader(message);
    }

    function updateURL(params) {
        if (window.history && window.history.pushState) {
            const url = new URL(window.location);
            Object.keys(params).forEach(key => {
                if (params[key]) {
                    url.searchParams.set(key, params[key]);
                } else {
                    url.searchParams.delete(key);
                }
            });
            window.history.pushState({}, '', url);
        }
    }

    function trackCTA(action, data) {
        // Send analytics data to WordPress
        if (typeof ct_ajax !== 'undefined' && ct_ajax.track_analytics) {
            $.post(ct_ajax.ajax_url, {
                action: 'ct_track_analytics',
                cta_action: action,
                cta_data: data,
                nonce: ct_ajax.nonce
            });
        }
    }

    // Handle browser back/forward buttons
    window.addEventListener('popstate', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const category = urlParams.get('category') || '';
        const priceMin = urlParams.get('price_min') || '';
        const priceMax = urlParams.get('price_max') || '';
        const rating = urlParams.get('rating') || '';
        const sort = urlParams.get('sort') || '';
        
        $('#category-filter').val(category);
        $('#price-min').val(priceMin);
        $('#price-max').val(priceMax);
        $('#rating-filter').val(rating);
        $('#sort-filter').val(sort);
        
        // Trigger filter if any parameters exist
        if (category || priceMin || priceMax || rating || sort) {
            filterProducts();
        }
    });

    // Graceful degradation for older browsers
    if (!window.fetch) {
        console.warn('Fetch API not supported, using fallback methods');
    }

    if (!window.IntersectionObserver) {
        console.warn('IntersectionObserver not supported, lazy loading disabled');
    }

})(jQuery);
