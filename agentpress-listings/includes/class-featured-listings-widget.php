<?php
/**
 * This widget presents loop content, based on your input, specifically for the homepage.
 *
 * @package AgentPress
 * @since 2.0
 * @author Nathan Rice
 */
class AgentPress_Featured_Listings_Widget extends WP_Widget {

	function AgentPress_Featured_Listings_Widget() {
		$widget_ops = array( 'classname' => 'featured-listings', 'description' => __( 'Display grid-style featured listings', 'apl' ) );
		$control_ops = array( 'width' => 300, 'height' => 350 );
		$this->WP_Widget( 'featured-listings', __( 'AgentPress - Featured Listings', 'apl' ), $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {
	
		/** defaults */
		$instance = wp_parse_args( $instance, array(
			'title' 			=> '',
			'posts_per_page'	=> 10
		) );
	
		extract( $args );
		
		echo $before_widget;
		
			if ( ! empty( $instance['title'] ) ) {
				echo $before_title . apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) . $after_title;
			}
			
			$toggle = ''; /** for left/right class */
			
			$query_args = array(
				'post_type'			=> 'listing',
				'posts_per_page'	=> $instance['posts_per_page'],
				'paged'				=> get_query_var('paged') ? get_query_var('paged') : 1
			);
			
			query_posts( $query_args );
			if ( have_posts() ) : while ( have_posts() ) : the_post();

				$loop = ''; /** initialze the $loop variable */

				$loop .= sprintf( '<a href="%s">%s</a>', get_permalink(), genesis_get_image( array( 'size' => 'properties' ) ) );

				$loop .= sprintf( '<span class="listing-price">%s</span>', genesis_get_custom_field('_listing_price') );
				$custom_text = genesis_get_custom_field( '_listing_text' );
				if( strlen( $custom_text ) )
					$loop .= sprintf( '<span class="listing-text">%s</span>', esc_html( $custom_text ) );
				$loop .= sprintf( '<span class="listing-address">%s</span>', genesis_get_custom_field('_listing_address') );
				$loop .= sprintf( '<span class="listing-city-state-zip">%s, %s %s</span>', genesis_get_custom_field('_listing_city'), genesis_get_custom_field('_listing_state'), genesis_get_custom_field('_listing_zip') );

				$loop .= sprintf( '<a href="%s" class="more-link">%s</a>', get_permalink(), __( 'View Listing', 'apl' ) );

				$toggle = $toggle == 'left' ? 'right' : 'left';

				/** wrap in post class div, and output **/
				printf( '<div class="%s"><div class="widget-wrap"><div class="listing-wrap">%s</div></div></div>', join( ' ', get_post_class( $toggle ) ), apply_filters( 'agentpress_featured_listings_widget_loop', $loop ) );

			endwhile; endif;
			wp_reset_query();
		
		echo $after_widget;
		
	}

	function update( $new_instance, $old_instance ) {
		return $new_instance;
	}

	function form( $instance ) {
		
		$instance = wp_parse_args( $instance, array(
			'title'				=> '',
			'posts_per_page'	=> 10
		) );
			
		printf( '<p><label for="%s">%s</label><input type="text" id="%s" name="%s" value="%s" style="%s" /></p>', $this->get_field_id('title'), __( 'Title:', 'apl' ), $this->get_field_id('title'), $this->get_field_name('title'), esc_attr( $instance['title'] ), 'width: 95%;' );
		
		printf( '<p>%s <input type="text" name="%s" value="%s" size="3" /></p>', __( 'How many results should be returned?', 'apl' ), $this->get_field_name('posts_per_page'), esc_attr( $instance['posts_per_page'] ) );
		
	}
}