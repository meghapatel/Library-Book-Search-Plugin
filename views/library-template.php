<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}
/**
 * The template for displaying all custom post data
 */

get_header(); ?>

<div class="wrap">
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<?php
			/* Start the Loop */
			while ( have_posts() ) : the_post();

				?>
				<header class="entry-header">
				<?php
				
				if ( is_single() ) {
					the_title( '<h1 class="entry-title"><b>Book Name :</b> ', '</h1>' );
				} elseif ( is_front_page() && is_home() ) {
					the_title( '<h3 class="entry-title"><b>Book Name :</b> <a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h3>' );
				} else {
					the_title( '<h2 class="entry-title"><b>Book Name :</b> <a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
				}
				?>
				</header>
				<div class="entry-content">
				<?php
				 $terms = get_the_terms( $post->ID , 'author' );
				 $terms_pub = get_the_terms( $post->ID , 'publisher' );
				 // Loop over each item since it's an array
				 if ( $terms != null ){
				 foreach( $terms as $term ) {
				 // Print the name method from $term which is an OBJECT
				 ?>
				 <p><b>Author :</b> 
				 <a href="<?php echo get_term_link($term->slug, 'author'); ?>"><?php echo $term->name; ?></a>
				 <?php
				 // Get rid of the other data stored in the object, since it's not needed
				 unset($term);
				 } } 
				 // Loop over each item since $term is an array
				 if ( $terms_pub != null ){
				 foreach( $terms_pub as $terms_pub1 ) {
				 // Print the name method from $term which is an OBJECT
				 ?>
				 </p>
				 <p>
				 <b>Publisher :</b> 
				 <a href="<?php echo get_term_link($terms_pub1->slug, 'publisher'); ?>"><?php echo $terms_pub1->name; ?></a>
				 <?php
				 // Get rid of the other data stored in the object, since it's not needed
				 unset($terms_pub1);
				 } } 
				?>
				 </p>

				<p><b>Book Description : </b>
				<?php
				/* translators: %s: Name of current post */
				the_content();
				?>
				</p>
				<p> <b>Rating :</b>
					    <?php $start_rating = get_post_meta( get_the_ID() , 'rating_meta_box_select', true ); ?>
						<span style="display:none;" id="star_rating"><?php echo $start_rating; ?></span>
						<span class="fa fa-star <?php echo ( $start_rating >= 1 ) ? "checked" : ""; ?>"></span>
						<span class="fa fa-star <?php echo ( $start_rating >= 2 ) ? "checked" : ""; ?>"></span>
						<span class="fa fa-star <?php echo ( $start_rating >= 3 ) ? "checked" : ""; ?>"></span>
						<span class="fa fa-star <?php echo ( $start_rating >= 4 ) ? "checked" : ""; ?>"></span>
						<span class="fa fa-star <?php echo ( $start_rating >= 5 ) ? "checked" : ""; ?>"></span>
				</p>
				<p> <b>Price : </b>
				<?php echo "$ ".get_post_meta( get_the_ID() , 'price_meta_box_text', true ); ?>
				</p>
				</div>
				<?php

				the_post_navigation( array(
					'prev_text' => '<span class="screen-reader-text">' . __( 'Previous Post', 'twentyseventeen' ) . '</span><span aria-hidden="true" class="nav-subtitle">' . __( 'Previous', 'twentyseventeen' ) . '</span> <span class="nav-title"><span class="nav-title-icon-wrapper">' . twentyseventeen_get_svg( array( 'icon' => 'arrow-left' ) ) . '</span>%title</span>',
					'next_text' => '<span class="screen-reader-text">' . __( 'Next Post', 'twentyseventeen' ) . '</span><span aria-hidden="true" class="nav-subtitle">' . __( 'Next', 'twentyseventeen' ) . '</span> <span class="nav-title">%title<span class="nav-title-icon-wrapper">' . twentyseventeen_get_svg( array( 'icon' => 'arrow-right' ) ) . '</span></span>',
				) );

			endwhile; // End of the loop.
			?>

		</main><!-- #main -->
	</div><!-- #primary -->
	<?php get_sidebar(); ?>
</div><!-- .wrap -->

<?php get_footer();
