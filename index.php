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
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <h1>Discover Amazing Products</h1>
                <p>Explore our curated collection of premium products with advanced filtering and seamless shopping experience.</p>
                <div class="hero-buttons">
                    <a href="#products" class="btn">Shop Now</a>
                    <a href="#about" class="btn btn-secondary">Learn More</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section id="products" class="products-section">
        <div class="container">
            <div class="section-title">
                <h2>Our Products</h2>
                <p>Find exactly what you're looking for with our advanced filtering system</p>
            </div>

            <!-- Product Filters -->
            <div class="filters-container">
                <div class="filters-row">
                    <div class="filter-group">
                        <label for="category-filter">Category</label>
                        <select id="category-filter" name="category">
                            <option value="">All Categories</option>
                            <option value="electronics">Electronics</option>
                            <option value="clothing">Clothing</option>
                            <option value="home">Home & Garden</option>
                            <option value="sports">Sports & Outdoors</option>
                            <option value="books">Books & Media</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="price-min">Price Range</label>
                        <div class="price-range">
                            <input type="number" id="price-min" name="price_min" placeholder="Min" min="0">
                            <span>-</span>
                            <input type="number" id="price-max" name="price_max" placeholder="Max" min="0">
                        </div>
                    </div>

                    <div class="filter-group">
                        <label for="rating-filter">Rating</label>
                        <select id="rating-filter" name="rating">
                            <option value="">Any Rating</option>
                            <option value="5">5 Stars</option>
                            <option value="4">4+ Stars</option>
                            <option value="3">3+ Stars</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="sort-filter">Sort By</label>
                        <select id="sort-filter" name="sort">
                            <option value="newest">Newest First</option>
                            <option value="price-low">Price: Low to High</option>
                            <option value="price-high">Price: High to Low</option>
                            <option value="rating">Highest Rated</option>
                            <option value="popular">Most Popular</option>
                        </select>
                    </div>

                    <button type="button" class="clear-filters" id="clear-filters">Clear Filters</button>
                </div>
            </div>

            <!-- Products Grid -->
            <div id="products-container">
                <div class="products-grid" id="products-grid">
                    <!-- Products will be loaded here via AJAX -->
                </div>
                
                <!-- Loading State -->
                <div class="loading hidden" id="loading">
                    <div class="spinner"></div>
                    <p>Loading products...</p>
                </div>
                
                <!-- No Results State -->
                <div class="no-results hidden" id="no-results">
                    <h3>No products found</h3>
                    <p>Try adjusting your filters or search criteria.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="feature-card">
                        <div class="feature-icon">🚚</div>
                        <h3>Free Shipping</h3>
                        <p>Free shipping on orders over $50</p>
                    </div>
                </div>
                <div class="col">
                    <div class="feature-card">
                        <div class="feature-icon">🔄</div>
                        <h3>Easy Returns</h3>
                        <p>30-day return policy for all items</p>
                    </div>
                </div>
                <div class="col">
                    <div class="feature-card">
                        <div class="feature-icon">🔒</div>
                        <h3>Secure Payment</h3>
                        <p>100% secure payment processing</p>
                    </div>
                </div>
                <div class="col">
                    <div class="feature-card">
                        <div class="feature-icon">📞</div>
                        <h3>24/7 Support</h3>
                        <p>Round-the-clock customer support</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

<?php get_footer(); ?>
