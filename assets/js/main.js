/**
 * CT Storefront Starter - Main JavaScript
 * Handles AJAX product filtering and interactive features
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        initProductFilters();
        initMobileMenu();
        loadInitialProducts();
    });

    function initProductFilters() {
        const $filters = $('#category-filter, #price-min, #price-max, #rating-filter, #sort-filter');
        const $clearFilters = $('#clear-filters');
        let filterTimeout;

        $filters.on('change keyup', function() {
            clearTimeout(filterTimeout);
            filterTimeout = setTimeout(filterProducts, 300);
        });

        $clearFilters.on('click', function() {
            $filters.val('');
            filterProducts();
        });

        function filterProducts() {
            const filterData = {
                action: 'ct_filter_products',
                nonce: ct_ajax.nonce,
                category: $('#category-filter').val(),
                price_min: $('#price-min').val(),
                price_max: $('#price-max').val(),
                rating: $('#rating-filter').val(),
                sort: $('#sort-filter').val()
            };

            $('#products-grid').hide();
            $('#loading').show();
            $('#no-results').hide();

            $.ajax({
                url: ct_ajax.ajax_url,
                type: 'POST',
                data: filterData,
                success: function(response) {
                    $('#loading').hide();
                    
                    if (response.success && response.data) {
                        const data = response.data;
                        
                        if (data.count > 0) {
                            $('#products-grid').html(data.html).fadeIn();
                            $('#products-grid .product-card').addClass('fade-in');
                        } else {
                            $('#products-grid').hide();
                            $('#no-results').show();
                        }
                    }
                },
                error: function() {
                    $('#loading').hide();
                    $('#no-results').show();
                }
            });
        }
    }

    function loadInitialProducts() {
        const $productsGrid = $('#products-grid');
        
        if ($productsGrid.length && $productsGrid.is(':empty')) {
            const filterData = {
                action: 'ct_filter_products',
                nonce: ct_ajax.nonce,
                category: '',
                price_min: '',
                price_max: '',
                rating: '',
                sort: 'newest'
            };

            $.ajax({
                url: ct_ajax.ajax_url,
                type: 'POST',
                data: filterData,
                success: function(response) {
                    if (response.success && response.data) {
                        const data = response.data;
                        if (data.count > 0) {
                            $productsGrid.html(data.html).fadeIn();
                        } else {
                            $productsGrid.html('<div class="no-results"><h3>No products available</h3><p>Please add some products to get started.</p></div>');
                        }
                    }
                }
            });
        }
    }

    function initMobileMenu() {
        $('#mobile-menu-toggle').on('click', function() {
            $('#site-navigation').toggleClass('mobile-active');
            $(this).toggleClass('active');
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('#site-navigation, #mobile-menu-toggle').length) {
                $('#site-navigation').removeClass('mobile-active');
                $('#mobile-menu-toggle').removeClass('active');
            }
        });
    }

    $(document).on('click', '.add-to-cart', function(e) {
        e.preventDefault();
        const $button = $(this);
        const originalText = $button.text();
        
        $button.prop('disabled', true).text('Adding...');
        
        setTimeout(function() {
            $button.text('Added!').addClass('added');
            setTimeout(function() {
                $button.prop('disabled', false).text(originalText).removeClass('added');
            }, 2000);
        }, 1000);
    });

    $('a[href^="#"]').on('click', function(e) {
        const target = $(this.getAttribute('href'));
        if (target.length) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: target.offset().top - 80
            }, 800);
        }
    });

})(jQuery);
