<?php
/**
 * CT Storefront Starter - Main Index Template
 * 
 * This is the main template file for the CT Storefront Starter theme.
 * It includes the hero section, product filters, and product grid.
 * 
 * @package CT_Storefront
 * @version 1.0.0
 */

get_header(); ?>

<main id="main" class="site-main">
    <!-- Hero Section -->
    <section class="hero-section" aria-labelledby="hero-title">
        <div class="container">
            <div class="hero-content">
                <h1 id="hero-title">Discover Amazing Products</h1>
                <p>Explore our curated collection of premium products with advanced filtering and seamless shopping experience.</p>
                <div class="hero-buttons">
                    <a href="#products" class="btn btn-primary">Shop Now</a>
                    <a href="#about" class="btn btn-secondary">Learn More</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section id="products" class="products-section" aria-labelledby="products-title">
        <div class="container">
            <div class="section-header">
                <h2 id="products-title">Our Products</h2>
                <p>Filter and discover products that match your preferences</p>
            </div>

            <!-- Filters Section -->
            <div class="filters-section">
                <div class="container">
                    <form id="filters-form" method="get" action="<?php echo esc_url(home_url('/')); ?>#products" novalidate>
                        <div class="filters-container">
                            <div class="filters-row">
                                <div class="filter-group">
                                    <label for="category-filter">Category</label>
                                    <select id="category-filter" name="category" aria-describedby="category-help">
                                        <option value="">All Categories</option>
                                        <option value="electronics">Electronics</option>
                                        <option value="clothing">Clothing</option>
                                        <option value="home">Home & Garden</option>
                                        <option value="sports">Sports & Outdoors</option>
                                        <option value="books">Books & Media</option>
                                    </select>
                                    <div id="category-help" class="sr-only">Select a product category to filter results</div>
                                </div>

                                <div class="filter-group">
                                    <label for="price-min">Min Price</label>
                                    <input type="number" id="price-min" name="price_min" min="0" step="0.01" placeholder="0.00" aria-describedby="price-help">
                                    <div id="price-help" class="sr-only">Enter minimum price in dollars</div>
                                </div>

                                <div class="filter-group">
                                    <label for="price-max">Max Price</label>
                                    <input type="number" id="price-max" name="price_max" min="0" step="0.01" placeholder="1000.00" aria-describedby="price-help">
                                </div>

                                <div class="filter-group">
                                    <label for="rating-filter">Min Rating</label>
                                    <select id="rating-filter" name="rating" aria-describedby="rating-help">
                                        <option value="">Any Rating</option>
                                        <option value="3">3+ Stars</option>
                                        <option value="4">4+ Stars</option>
                                        <option value="5">5 Stars</option>
                                    </select>
                                    <div id="rating-help" class="sr-only">Select minimum star rating</div>
                                </div>

                                <div class="filter-group">
                                    <label for="sort-filter">Sort By</label>
                                    <select id="sort-filter" name="sort" aria-describedby="sort-help">
                                        <option value="newest">Newest First</option>
                                        <option value="price-low">Price: Low to High</option>
                                        <option value="price-high">Price: High to Low</option>
                                        <option value="rating">Highest Rated</option>
                                        <option value="popular">Most Popular</option>
                                    </select>
                                    <div id="sort-help" class="sr-only">Choose how to sort the products</div>
                                </div>

                                <div class="filter-group">
                                    <button type="button" class="clear-filters" id="clear-filters" aria-describedby="clear-help">
                                        Clear Filters
                                    </button>
                                    <div id="clear-help" class="sr-only">Clear all applied filters</div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Products Grid -->
            <div id="products-grid" class="products-grid" aria-live="polite" aria-atomic="true">
                <!-- Products will be loaded here via AJAX -->
            </div>

            <!-- Loading State -->
            <div id="loading" class="loading" style="display: none;" aria-hidden="true">
                <div class="spinner" role="status" aria-label="Loading products"></div>
                <p>Loading products...</p>
            </div>

            <!-- No Results State -->
            <div id="no-results" class="no-results" style="display: none;">
                <h3>No products found</h3>
                <p>Try adjusting your filters or browse all products.</p>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="about" class="features-section" aria-labelledby="features-title">
        <div class="container">
            <div class="section-header">
                <h2 id="features-title">Why Choose Us</h2>
                <p>We provide the best shopping experience with premium features</p>
            </div>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon" aria-hidden="true">🚚</div>
                    <h3>Free Shipping</h3>
                    <p>Free shipping on orders over $50. Fast and reliable delivery to your doorstep.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon" aria-hidden="true">🔄</div>
                    <h3>Easy Returns</h3>
                    <p>30-day return policy for all items. Hassle-free returns and exchanges.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon" aria-hidden="true">🔒</div>
                    <h3>Secure Payment</h3>
                    <p>100% secure payment processing. Your data is protected with industry-standard encryption.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon" aria-hidden="true">📞</div>
                    <h3>24/7 Support</h3>
                    <p>Round-the-clock customer support. We're here to help whenever you need us.</p>
                </div>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>
