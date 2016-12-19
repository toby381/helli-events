<?php
/**
 * The template for displaying helli-post
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */

get_header(); ?>

<div class="wrap">
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<?php
				/* Start the Loop */
				while ( have_posts() ) : the_post();
                    ?>
										
                    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                        <header class="entry-header">

                            <!-- Display featured image in right-aligned floating div -->
                            <div style="float: right; margin: 10px">
                                <?php the_post_thumbnail( array( 100, 100 ) ); ?>
                            </div>

                            <!-- Display Title and Author Name -->
                            <strong>Title: </strong><?php the_title(); ?><br />
                            <strong>Sted: </strong>
                            <?php echo esc_html( get_post_meta( get_the_ID(), 'event_sted', true ) ); ?>
                            <br />

                            
                    </header>

                    <!-- Display contents -->
                    <div class="entry-content"><?php the_content(); ?></div>
                </article>
                
            <?php
                if(get_post_meta( get_the_ID(), 'event_booking_status', true ) == "on") {  ?>
                    
                    <form method = "post" action = ""> 
                            <input type="hidden" name="event_id" value="<?php the_ID(); ?>">
                            <input type="hidden" name="user_id" value="<?php echo get_current_user_id(); ?>">
                  <h3>Meld deg på</h3>
                  <p> 
                     <label for="firstName">Fornavn:</label> 
                     <input type="text" name="firstName"/>
                  </p>
                  <p> 
                     <label for="lastName">Etternavn:</label> 
                     <input type="text" name="lastName"/>
                  </p>
                    <p> 
                     <label for="epost">Epost:</label> 
                     <input type="text" name="epost"/>
                  </p>
                  <hr>  
                  <input type="submit" value="Submit" name="booking_submit"/>  
            </form>
                <?php
                }
                ?>
 	

				<?php endwhile; // End of the loop.
			?>

		</main><!-- #main -->
	</div><!-- #primary -->
	<?php get_sidebar(); ?>
</div><!-- .wrap -->

<?php get_footer();
