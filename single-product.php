<?php
/**
 * Single Product Template
 * 
 * @package CT_Storefront
 */

get_header(); ?>

<main id="main" class="site-main">
    <div class="container">
        <?php while (have_posts()) : the_post(); ?>
            
            <article id="post-<?php the_ID(); ?>" <?php post_class('product-single'); ?>>
                
                <nav class="breadcrumb">
                    <a href="<?php echo esc_url(home_url('/')); ?>">Home</a>
                    <span>/</span>
                    <a href="#products">Products</a>
                    <span>/</span>
                    <span><?php the_title(); ?></span>
                </nav>

                <div class="product-content-wrapper">
                    
                    <div class="product-images">
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="main-image">
                                <?php the_post_thumbnail('product-large'); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="product-details">
                        
                        <header class="product-header">
                            <h1 class="product-title"><?php the_title(); ?></h1>
                            
                            <?php 
                            $category = get_post_meta(get_the_ID(), '_product_category', true);
                            if ($category) : ?>
                                <p class="product-category"><?php echo esc_html(ucfirst($category)); ?></p>
                            <?php endif; ?>
                            
                            <?php 
                            $rating = get_post_meta(get_the_ID(), '_product_rating', true);
                            if ($rating) : ?>
                                <div class="product-rating">
                                    <div class="stars">
                                        <?php
                                        for ($i = 1; $i <= 5; $i++) {
                                            if ($i <= $rating) {
                                                echo '★';
                                            } else {
                                                echo '☆';
                                            }
                                        }
                                        ?>
                                    </div>
                                    <span class="rating-text">(<?php echo number_format($rating, 1); ?>)</span>
                                </div>
                            <?php endif; ?>
                        </header>

                        <div class="product-price-section">
                            <?php 
                            $price = get_post_meta(get_the_ID(), '_product_price', true);
                            if ($price) : ?>
                                <div class="product-price">
                                    <span class="current-price">$<?php echo number_format($price, 2); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php 
                            $badge = get_post_meta(get_the_ID(), '_product_badge', true);
                            if ($badge) : ?>
                                <span class="product-badge <?php echo esc_attr($badge); ?>">
                                    <?php echo esc_html(ucfirst($badge)); ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="product-description">
                            <?php the_content(); ?>
                        </div>

                        <div class="product-actions">
                            <button class="add-to-cart-large" data-product-id="<?php echo get_the_ID(); ?>">
                                Add to Cart
                            </button>
                        </div>

                    </div>
                </div>

            </article>

        <?php endwhile; ?>
    </div>
</main>

<?php get_footer(); ?>
