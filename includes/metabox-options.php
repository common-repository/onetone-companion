<?php

/**
 * Calls the class on the post edit screen.
 */
function onetoneCompanion_call_metaboxClass() {
    new OnetoneCompanion_metaboxClass();
}

if ( is_admin() ) {
    add_action( 'load-post.php', 'onetoneCompanion_call_metaboxClass' );
    add_action( 'load-post-new.php', 'onetoneCompanion_call_metaboxClass' );
}

/** 
 * The Class.
 */
class OnetoneCompanion_metaboxClass {

	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'onetone_add_meta_box' ) );
		add_action( 'save_post', array( $this, 'onetone_save' ) );
	}

	/**
	 * Adds the meta box container.
	 */
	public function onetone_add_meta_box( $post_type ) {
            $post_types = array( 'page');     //limit meta box to certain post types
            if ( in_array( $post_type, $post_types )) {
		add_meta_box(
			'onetone_companion_page_meta_box'
			,__( 'OneTone Metabox Options', 'onetone-companion' )
			,array( $this, 'onetone_render_meta_box_content' )
			,$post_type
			,'advanced'
			,'high'
		);
            }
	}
// get onetone sliders from plugin magee shrotcodes

 public static function onetone_sliders_meta(){
	 $onetone_sliders[] = array(
            'label'       => __( 'Select a slider', 'onetone-companion' ),
            'value'       => ''
          );
	$onetone_custom_slider = new WP_Query( array( 'post_type' => 'magee_slider', 'post_status'=>'publish', 'posts_per_page' => -1 ) );
	while ( $onetone_custom_slider->have_posts() ) {
		$onetone_custom_slider->the_post();

		$onetone_sliders[] = array(
            'label'       => get_the_title(),
            'value'       => get_the_ID()
          );
	}
	wp_reset_postdata();
	return $onetone_sliders;
	 }
  
	/**
	 * Save the meta when the post is saved.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	public function onetone_save( $post_id ) {
	
		/*
		 * We need to verify this came from the our screen and with proper authorization,
		 * because save_post can be triggered at other times.
		 */

		// Check if our nonce is set.
		if ( ! isset( $_POST['onetone_inner_custom_box_nonce'] ) )
			return $post_id;

		$nonce = $_POST['onetone_inner_custom_box_nonce'];

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'onetone_inner_custom_box' ) )
			return $post_id;
			
			
		// If this is an autosave, our form has not been submitted,
                //     so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return $post_id;

		// Check the user's permissions.
		if ( 'page' == $_POST['post_type'] ) {

			if ( ! current_user_can( 'edit_page', $post_id ) )
				return $post_id;
	
		} else {

			if ( ! current_user_can( 'edit_post', $post_id ) )
				return $post_id;
		}


		/* OK, its safe for us to save the data now. */

		// Sanitize the user input.
		//$show_breadcrumb        = sanitize_text_field( $_POST['onetone_show_breadcrumb'] );
		//$onetone_layout          = sanitize_text_field( $_POST['onetone_layout'] );

		if( isset($_POST) && $_POST ){
			
		$post_metas                      = array();		
		$post_metas['header_overlay']    =  isset($_POST['header_overlay'])?sanitize_text_field($_POST['header_overlay']):'no';
		$post_metas['nav_menu']          =  isset($_POST['nav_menu'])?sanitize_text_field($_POST['nav_menu']):'';
		$post_metas['full_width']        =  isset($_POST['full_width'])?sanitize_text_field($_POST['full_width']):'no';
		$post_metas['padding_top']       =  isset($_POST['padding_top'])?sanitize_text_field($_POST['padding_top']):'';
		$post_metas['padding_bottom']    =  isset($_POST['padding_bottom'])?sanitize_text_field($_POST['padding_bottom']):'';
		$post_metas['display_breadcrumb'] =  isset($_POST['display_breadcrumb'])?sanitize_text_field($_POST['display_breadcrumb']):'';
		$post_metas['nav_menu']          =  isset($_POST['nav_menu'])?sanitize_text_field($_POST['nav_menu']):'';
		$post_metas['page_layout']       =  isset($_POST['page_layout'])?sanitize_text_field($_POST['page_layout']):'none';
		$post_metas['left_sidebar']      =  isset($_POST['left_sidebar'])?sanitize_text_field($_POST['left_sidebar']):'';
		$post_metas['right_sidebar']     =  isset($_POST['right_sidebar'])?sanitize_text_field($_POST['right_sidebar']):'';
		$post_metas['slider_banner']     =  isset($_POST['slider_banner'])?sanitize_text_field($_POST['slider_banner']):'0';
		$post_metas['banner_position']   =  isset($_POST['banner_position'])?sanitize_text_field($_POST['banner_position']):'1';
		$post_metas['magee_slider']      =  isset($_POST['magee_slider'])?sanitize_text_field($_POST['magee_slider']):'';
		$post_metas['display_title']     =  isset($_POST['display_title'])?sanitize_text_field($_POST['display_title']):'yes';
		$post_metas['display_title_bar'] =  isset($_POST['display_title_bar'])?sanitize_text_field($_POST['display_title_bar']):'0';
		
		$post_metas['titlebar_background_color'] =  isset($_POST['titlebar_background_color'])?sanitize_hex_color($_POST['titlebar_background_color']):'';
		$post_metas['titlebar_background_image'] =  isset($_POST['titlebar_background_image'])?sanitize_text_field($_POST['titlebar_background_image']):'0';
		$post_metas['titlebar_font_color'] =  isset($_POST['titlebar_font_color'])?sanitize_hex_color($_POST['titlebar_font_color']):'';
		$post_metas['titlebar_padding_top']       =  isset($_POST['titlebar_padding_top'])?sanitize_text_field($_POST['titlebar_padding_top']):'';
		$post_metas['titlebar_padding_bottom']    =  isset($_POST['titlebar_padding_bottom'])?sanitize_text_field($_POST['titlebar_padding_bottom']):'';
		
		$onetone_post_meta = json_encode( $post_metas );
		// Update the meta field.
		update_post_meta( $post_id, '_onetone_post_meta', $onetone_post_meta );
		}

	}

	/**
	 * Render Meta Box content.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function onetone_render_meta_box_content( $post ) {
	
	   global $wp_registered_sidebars;
	   
	 //  $magee_sliders = self::onetone_sliders_meta();
	   
		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'onetone_inner_custom_box', 'onetone_inner_custom_box_nonce' );

		// Use get_post_meta to retrieve an existing value from the database.
	    $page_meta  = get_post_meta( $post->ID ,'_onetone_post_meta',true);
		$page_metas = json_decode( $page_meta,true );
		if( $page_metas )
	    extract( $page_metas );
		
	
		/************ get nav menus*************/
		
		$menus 	= get_terms( 'nav_menu', array( 'hide_empty' => true ) );

		$nav_menus[] = array(
            'label'       => __( 'Default', 'onetone-companion' ),
            'value'       => ''
          );
		  
		foreach ( $menus as $menu ) {
		$nav_menus[] = array(
					'label'       => $menu->name,
					'value'       => $menu->term_id
				  );
			
		}
		
		/* sidebars */

	  $onetone_sidebars[] = array(
				  'label'       => __( 'None', 'onetone-companion' ),
				  'value'       => ''
				);
	  
	  foreach( $wp_registered_sidebars as $key => $value){
		  
		  $onetone_sidebars[] = array(
				  'label'       => $value['name'],
				  'value'       => $value['id'],
				);
		  }
		  
		// Display the form, using the current value.
		$header_overlay     = isset( $header_overlay )? $header_overlay:'no'; 
		$nav_menu           = isset( $nav_menu )? $nav_menu:''; 
		$full_width         = isset( $full_width )? $full_width:'no'; 
		$page_layout        = isset( $page_layout )? $page_layout:'none'; 
		$left_sidebar       = isset( $left_sidebar )? $left_sidebar:''; 
		$right_sidebar      = isset( $right_sidebar )? $right_sidebar:''; 
		$display_breadcrumb = isset( $display_breadcrumb )? $display_breadcrumb:''; 
		$display_title      = isset( $display_title )? $display_title:'yes'; 
		
		$padding_top         = isset( $padding_top )? $padding_top:'50px';
		$padding_bottom      = isset( $padding_bottom )? $padding_bottom:'50px';
		
		// page title bar
		$titlebar_padding_top         = isset( $titlebar_padding_top )? $titlebar_padding_top:'40px';
		$titlebar_padding_bottom      = isset( $titlebar_padding_bottom )? $titlebar_padding_bottom:'40px';
		$titlebar_font_color      = isset( $titlebar_font_color )? sanitize_hex_color($titlebar_font_color):'#555555';
		$titlebar_background_color      = isset( $titlebar_background_color )? sanitize_hex_color($titlebar_background_color):'#f5f5f5';
		$titlebar_background_image      = isset( $titlebar_background_image )? esc_url($titlebar_background_image):'';
		
		$display_title_bar  = isset( $display_title_bar )? $display_title_bar:''; 
		
		echo '<p class="meta-options"><label for="header_overlay"  style="display: inline-block;width: 150px;">';
		_e( 'Header Overlay', 'onetone-companion' );
		echo '</label> ';
		echo '<select name="header_overlay" id="overlay_header">
		<option '.selected($header_overlay,'no',false).' value="no">'.__("No","onetone-companion").'</option>
		<option '.selected($header_overlay,'yes',false).' value="yes">'.__("Yes","onetone-companion").'</option>
		</select></p>';
		
		echo '<p class="meta-options"><label for="left_sidebar"  style="display: inline-block;width: 150px;">';
		_e( 'Custom Menu', 'onetone-companion' );
		echo '</label> ';
		echo '<select name="nav_menu" id="nav_menu">';
		foreach( $nav_menus as $nav){
		echo '<option '.selected($nav_menu,$nav['value'],false).' value="'.esc_attr($nav['value']).'">'.esc_attr($nav['label']).'</option>';
		}
		echo '</select></p>';
		
		
		echo '<p class="meta-options"><label for="titlebar_background_color"  style="display: inline-block;width: 150px;">';
		_e( 'Page Title Bar Background Color', 'onetone-companion' );
		echo '</label> ';
		echo '<input class="oc-color" type="text" name="titlebar_background_color" value="'.$titlebar_background_color.'"/>';
		echo '</p>';

		
		echo '<p class="meta-options"><label for="titlebar_background_image"  style="display: inline-block;width: 150px;">';
		_e( 'Page Title Bar Background Image', 'onetone-companion' );
		echo '</label>';
		echo '<span class="form-group oc-uploader"><input type="text" name="titlebar_background_image" value="'.$titlebar_background_image.'"></span>';
		echo '</p>';
		
		echo '<p class="meta-options"><label for="titlebar_font_color"  style="display: inline-block;width: 150px;">';
		_e( 'Page Title Bar Font Color', 'onetone-companion' );
		echo '</label> ';
		echo '<input class="oc-color" type="text" name="titlebar_font_color" value="'.$titlebar_font_color.'"/>';
		echo '</p>';
		
		echo '<p class="meta-options"><label for="titlebar_padding_top"  style="display: inline-block;width: 150px;">';
		_e( 'Title Bar Padding Top', 'onetone-companion' );
		echo '</label> ';
		echo '<input name="titlebar_padding_top" type="text" value="'.esc_attr($titlebar_padding_top).'" />';
		echo '</p>';
		
		echo '<p class="meta-options"><label for="titlebar_padding_bottom"  style="display: inline-block;width: 150px;">';
		_e( 'Title Bar Padding Bottom', 'onetone-companion' );
		echo '</label> ';
		echo '<input name="titlebar_padding_bottom" type="text" value="'.esc_attr($titlebar_padding_bottom).'" />';
		echo '</p>';
		
		
		
		echo '<p class="meta-options"><label for="full_width"  style="display: inline-block;width: 150px;">';
		_e( 'Content Full Width', 'onetone-companion' );
		echo '</label> ';
		echo '<select name="full_width" id="full_width">
		<option '.selected($full_width,'no',false).' value="no">'.__("No","onetone-companion").'</option>
		<option '.selected($full_width,'yes',false).' value="yes">'.__("Yes","onetone-companion").'</option>
		</select></p>';

		echo '<p class="meta-options"><label for="display_title_bar"  style="display: inline-block;width: 150px;">';
		_e( 'Display Title Bar', 'onetone-companion' );
		echo '</label> ';
		echo '<select name="display_title_bar" id="display_title_bar">
		<option '.selected( $display_title_bar,'',false ).' value="" selected>'.__("Default","onetone-companion").'</option>
		<option '.selected( $display_title_bar,'yes',false ).' value="yes">'.__("Yes","onetone-companion").'</option>
		<option '.selected( $display_title_bar,'no',false ).' value="no">'.__("No","onetone-companion").'</option>
		</select></p>';
		
		echo '<p class="meta-options"><label for="display_breadcrumb"  style="display: inline-block;width: 150px;">';
		_e( 'Display Breadcrumb', 'onetone-companion' );
		echo '</label> ';
		echo '<select name="display_breadcrumb" id="display_breadcrumb">
		<option '.selected( $display_breadcrumb,'',false ).' value="">'.__("Default","onetone-companion").'</option>
		<option '.selected( $display_breadcrumb,'yes',false ).' value="yes">'.__("Yes","onetone-companion").'</option>
		<option '.selected( $display_breadcrumb,'no',false ).' value="no">'.__("No","onetone-companion").'</option>
		</select></p>';
		
		
		echo '<p class="meta-options"><label for="page_layout"  style="display: inline-block;width: 150px;">';
		_e( 'Page Layout', 'onetone-companion' );
		echo '</label> ';
		echo '<select name="page_layout" id="page_layout">
		<option '.selected($page_layout,'none',false).' value="none">'.__("No Sidebar","onetone-companion").'</option>
		<option '.selected($page_layout,'left',false).' value="left">'.__("Left Sidebar","onetone-companion").'</option>
		<option '.selected($page_layout,'right',false).' value="right">'.__("Right Sidebar","onetone-companion").'</option>
		<option '.selected($page_layout,'both',false).' value="both">'.__("Both Sidebar","onetone-companion").'</option>
		</select></p>';
		
		echo '<p class="meta-options"><label for="padding_top"  style="display: inline-block;width: 150px;">';
		_e( 'Content Padding Top', 'onetone-companion' );
		echo '</label> ';
		echo '<input name="padding_top" type="text" value="'.esc_attr($padding_top).'" />';
		echo '</p>';
		
		echo '<p class="meta-options"><label for="padding_bottom"  style="display: inline-block;width: 150px;">';
		_e( 'Content Padding Bottom', 'onetone-companion' );
		echo '</label> ';
		echo '<input name="padding_bottom" type="text" value="'.esc_attr($padding_bottom).'" />';
		echo '</p>';
		
		echo '<p class="meta-options"><label for="left_sidebar"  style="display: inline-block;width: 150px;">';
		_e( 'Select Left Sidebar', 'onetone-companion' );
		echo '</label> ';
		echo '<select name="left_sidebar" id="left_sidebar">';
		foreach( $onetone_sidebars as $sidebar ){
		echo '<option '.selected($left_sidebar,$sidebar['value'],false).' value="'.esc_attr($sidebar['value']).'">'.esc_attr($sidebar['label']).'</option>';
		}
		echo '</select></p>';
		
		echo '<p class="meta-options"><label for="right_sidebar"  style="display: inline-block;width: 150px;">';
		_e( 'Select Right Sidebar', 'onetone-companion' );
		echo '</label> ';
		echo '<select name="right_sidebar" id="right_sidebar">';
		foreach( $onetone_sidebars as $sidebar ){
		echo '<option '.selected($right_sidebar,$sidebar['value'],false).' value="'.esc_attr($sidebar['value']).'">'.esc_attr($sidebar['label']).'</option>';
		}
		echo '</select></p>';
		
		
		
	}
}