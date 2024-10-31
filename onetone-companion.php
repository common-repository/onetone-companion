<?php
/*
	Plugin Name: OneTone Companion
	Description: Theme options and templates importer.
	Author: MageeWP
	Author URI: https://www.mageewp.com/
	Version: 1.1.1
	Text Domain: onetone-companion
	Domain Path: /languages
	License: GPL v2 or later
*/

if ( !defined('ABSPATH') ) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

require_once "includes/metabox-options.php";
require_once 'includes/elementor-widgets/elementor-widgets.php';
require_once 'includes/templates-importer/templates-importer.php';
require_once 'includes/templates-importer/class-site-options-import.php';
require_once 'includes/widget-recent-posts.php';
define( 'ONETONE_COMPANION_VER', '1.1.1' );

if(!class_exists('OnetoneCompanion')){
	
	class OnetoneCompanion{
	
		public function __construct() {

			add_action( 'plugins_loaded', array( $this, 'init' ) );
			add_action('admin_menu', array(&$this,'create_menu'));
			add_action( 'wp_enqueue_scripts',  array(&$this , 'front_scripts' ));
			add_action( 'admin_enqueue_scripts', array($this,'admin_scripts' ));
			add_shortcode( 'oc_contact',  array(&$this, 'contact_form') );
			
			add_action('wp_ajax_onetone_contact', array(&$this,'onetone_contact'));
			add_action('wp_ajax_nopriv_onetone_contact', array(&$this,'onetone_contact'));
			
			add_action( 'do_meta_boxes', array(&$this,'remove_theme_metaboxes') );
			add_action('init', array(&$this,'html_tags_code') );
			add_action('init', array(&$this,'update_companion_options') );
			
		}
		
	  function remove_theme_metaboxes(){
			  remove_meta_box( 'onetone_page_meta_box', 'page', 'advanced' );
	  }
		
		public static function init() {
			load_plugin_textdomain( 'onetone-companion', false,  basename( dirname( __FILE__ ) ) . '/languages' );
		}
		
	  function admin_scripts() {
		  
		  wp_enqueue_style( 'wp-color-picker' );
		  wp_enqueue_style('thickbox');
		  wp_enqueue_script('thickbox');
		  wp_enqueue_script( 'media-upload'); 

		  wp_enqueue_script( 'onetone-companion-admin', plugins_url('assets/js/admin.js', __FILE__),array( 'jquery', 'wp-color-picker' ),ONETONE_COMPANION_VER,true);
		  wp_enqueue_style( 'onetone-companion-admin-css',  plugins_url( 'assets/css/admin.css',__FILE__ ), '',ONETONE_COMPANION_VER, false );
		  
		  if(isset($_GET['page']) && ( $_GET['page'] == 'onetone-companion' || $_GET['page'] == 'onetone-license' ) )

		  if(isset($_GET['page']) && ( $_GET['page']== 'onetone-templates' || $_GET['page']== 'onetone-companion') ){
			  wp_enqueue_script( 'onetone-companion-templater', plugins_url('assets/js/templater.js', __FILE__),array( 'jquery', 'wp-util', 'updates' ),ONETONE_COMPANION_VER,true);
			  wp_enqueue_style( 'onetone-companion-templater',  plugins_url( 'assets/css/templater.css',__FILE__ ), '',ONETONE_COMPANION_VER, false );
  
			  wp_localize_script( 'onetone-companion-templater', 'onetone_companion_admin',
			  array(
				  'ajaxurl' => admin_url('admin-ajax.php'),
				  'nonce' => wp_create_nonce( 'wp_rest' ),
				  'i18n' =>array('t1'=> __( 'Install and Import', 'onetone-companion' ),'t2'=> __( 'Import', 'onetone-companion' ),'t3'=> __( 'Install and Import Site', 'onetone-companion' ),'t4'=> __( 'Import Site', 'onetone-companion' ) ),
			  ) );
			  
		  }
			  
	  }
	  
	function update_companion_options(){
		
		$companion_options = get_option( 'onetone_companion_options' );
		
		if( is_array($companion_options) && isset($companion_options['onetone_homepage_sections']) ){
			foreach($companion_options as $k=>$v){
				
				if( in_array( $k, array(
				'onetone_homepage_sections',
				'onetone_homepage_options',
				'onetone_slideshow',
				'onetone_general_option',
				'onetone_header',
				'onetone_page_title_bar',
				'onetone_styling',
				'onetone_sidebar',
				'onetone_footer'

			)) ){
				$companion_options[$k] = '';					
					}
				
				}

			update_option('onetone_companion_options',$companion_options );
			
			}
		}
		
	function front_scripts() {
		
			global $post;
			
			wp_enqueue_script( 'onetone-companion-front', plugins_url('assets/js/main.js', __FILE__),array('jquery'),ONETONE_COMPANION_VER,true);
			wp_enqueue_style( 'onetone-companion-front',  plugins_url( 'assets/css/front.css',__FILE__ ), '',ONETONE_COMPANION_VER, false );
			$i18n = array(
			'i1'=> __('Please fill out all required fields.','onetone-companion' ),
			'i2'=> __('Please enter valid email.','onetone-companion' ),
			'i3'=> __('Please enter your name.','onetone-companion' ),
			'i4'=> __('Message is required.','onetone-companion' ),
			);
			
			if(is_page()){
				
				if( isset($post->ID ) ){
					$onetone_page_meta = get_post_meta( $post->ID ,'_onetone_post_meta');
				}				
				if( isset($onetone_page_meta[0]) && $onetone_page_meta[0]!='' )
					$onetone_page_meta = json_decode( $onetone_page_meta[0],true );
					
					$padding_top     = isset($onetone_page_meta['padding_top'])?$onetone_page_meta['padding_top']:'';
					$padding_bottom  = isset($onetone_page_meta['padding_bottom'])?$onetone_page_meta['padding_bottom']:'';

					$titlebar_background_color = isset($onetone_page_meta['titlebar_background_color'])?$onetone_page_meta['titlebar_background_color']:'';
					$titlebar_background_image = isset($onetone_page_meta['titlebar_background_image'])?$onetone_page_meta['titlebar_background_image']:'';
					$titlebar_font_color = isset($onetone_page_meta['titlebar_font_color'])?$onetone_page_meta['titlebar_font_color']:'';
					$titlebar_padding_top     = isset($onetone_page_meta['titlebar_padding_top'])?$onetone_page_meta['titlebar_padding_top']:'';
					$titlebar_padding_bottom  = isset($onetone_page_meta['titlebar_padding_bottom'])?$onetone_page_meta['titlebar_padding_bottom']:'';
					
					$css = '';
					$container_css = '';
					if( $padding_top )
						$container_css .= 'padding-top:'.esc_attr($padding_top).';';
					if( $padding_bottom )
						$container_css .= 'padding-bottom:'.esc_attr($padding_bottom).';';
					
					$titlebar_css = '';
					if( $titlebar_background_color )
						$titlebar_css .= 'background-color:'.sanitize_hex_color($titlebar_background_color).';';
					if( $titlebar_background_image )
						$titlebar_css .= 'background-image: url('.esc_url($titlebar_background_image).');';
					if( $titlebar_padding_top )
						$titlebar_css .= 'padding-top:'.esc_attr($titlebar_padding_top).';';
					if( $titlebar_padding_bottom )
						$titlebar_css .= 'padding-bottom:'.esc_attr($titlebar_padding_bottom).';';
						
					$css .= '#post-'.$post->ID.' .post-inner{'.$container_css.'}';
					$css .= '#post-'.$post->ID.' .page-title-bar{'.$titlebar_css.'}';
					
					if( $titlebar_font_color )
						$css .= '#post-'.$post->ID.' .page-title-bar,#post-'.$post->ID.' .page-title-bar h1, #post-'.$post->ID.' .page-title-bar a, #post-'.$post->ID.' .page-title-bar span, #post-'.$post->ID.' .page-title-bar i{color:'.sanitize_hex_color($titlebar_font_color).';}';
					
					$css = wp_filter_nohtml_kses($css);
					
					wp_add_inline_style( 'onetone-companion-front', $css );

				
				}
			
			wp_localize_script( 'onetone-companion-front', 'oc_params', array(
				'ajaxurl' => admin_url('admin-ajax.php'),
				'plugins_url' => plugins_url('', __FILE__),
				'i18n' => $i18n,
			));
			
			
	}
	
	function create_menu() {
	
		$theme = self::get_theme_textdomain();
		//create new top-level menu
		add_menu_page( __('OneTone Companion','onetone-companion'), __('OneTone Companion','onetone-companion'), 'manage_options', 'onetone-companion', array('MageewpTemplater','render_admin_page'),'dashicons-admin-generic');
		
	if( $theme == 'onetone-pro' || $theme == 'onetone_pro' ){
		add_submenu_page(
			'onetone-companion', __( 'License Key', 'onetone-companion' ), __( 'License Key', 'onetone-companion' ), 'manage_options', 'onetone-license',
			array(&$this, 'settings_page' )
		);
	}

		//call register settings function
		add_action( 'admin_init', array(&$this,'register_mysettings') );
	}
	
	// hide license key
	public static function replaceStar($str, $start, $length = 0){
	
	  $i = 0;
	  $star = '';
	  if($start >= 0) {
	   if($length > 0) {
		$str_len = strlen($str);
		$count = $length;
		if($start >= $str_len) {
		 $count = 0;
		}
	   }elseif($length < 0){
		$str_len = strlen($str);
		$count = abs($length);
		if($start >= $str_len) {
		 $start = $str_len - 1;
		}
		$offset = $start - $count + 1;
		$count = $offset >= 0 ? abs($length) : ($start + 1);
		$start = $offset >= 0 ? $offset : 0;
	   }else {
		$str_len = strlen($str);
		$count = $str_len - $start;
	   }
	  }else {
	   if($length > 0) {
		$offset = abs($start);
		$count = $offset >= $length ? $length : $offset;
	   }elseif($length < 0){
		$str_len = strlen($str);
		$end = $str_len + $start;
		$offset = abs($start + $length) - 1;
		$start = $str_len - $offset;
		$start = $start >= 0 ? $start : 0;
		$count = $end - $start + 1;
	   }else {
		$str_len = strlen($str);
		$count = $str_len + $start + 1;
		$start = 0;
	   }
	  }
	 
	  while ($i < $count) {
	   $star .= '*';
	   $i++;
	  }
	 
	  return substr_replace($str, $star, $start, $count);
	}
		
	static function license( $onetone_companion_options ){
		
	
		?>
          <div class="license">
          <p><?php esc_html_e( 'You can import the Pro version of the OneTone templates after activating the license key.', 'onetone-companion' );?></p>
          
          <?php if ( $onetone_companion_options['license_key'] == '' ):?>
          
		<p><?php _e( 'License Key', 'onetone-companion' );?>: <input size="50" name="onetone_companion_options[license_key]" value="<?php echo $onetone_companion_options['license_key'];?>" type="text" /></p>
		<p></p>
        <?php
		
		else:
		$license_key_hide = OnetoneCompanion::replaceStar($onetone_companion_options['license_key'],10,8);
		
		$license_key = '';

		?>
      
        <p><?php _e( 'License Key', 'onetone-companion' );?>: 
        <input size="50" disabled="disabled" name="onetone_companion_options[license_key_hide]" value="<?php echo $license_key_hide ;?>" type="text" />
        <input size="50" type="hidden" name="onetone_companion_options[license_key]" value="<?php echo $license_key;?>" type="text" /></p>
		<p></p>
        
        <?php endif;?>
		 
		   </div>
			<p class="submit">
            <?php if($onetone_companion_options['license_key'] == '' ):?>
			<input type="submit" class="button-primary" value="<?php _e('Active','onetone-companion');?>" />
            <?php	else:?>
            <input type="submit" class="button-primary" value="<?php _e('Deactivate','onetone-companion');?>" />
		 <?php endif;?>
			</p>
		
	<?php	}
	
	static function check_onetone_version(){
		
		// customizer sections version
		$onetone_customizer_section = '';
		if( function_exists('onetone_option_saved') ){
			for( $s=0; $s<10;$s++){
				
				$have_section_title = onetone_option_saved('section_title_'.$s);
				$have_menu_title = onetone_option_saved('menu_title_'.$s);
				
				if( $have_section_title != '' || $have_menu_title != '' ){
					$onetone_customizer_section = 1;
					break;
				}
			}
		}

		return $onetone_customizer_section;
		
		}
	
	// Get theme text domain
	function get_theme_textdomain(){
		$theme = wp_get_theme();
			
		$textdomain = $theme->get( 'Template' );
		if( $textdomain == '' )
			$textdomain = $theme->get( 'TextDomain' );
		return $textdomain;	
	}
	
	// Default options
	public static function default_options(){

			$return = array(
				
				'license_key' => '',

			);
			
			return $return;
			
			}
			
	function text_validate($input){
			
			//$onetone_customizer_section = self::check_onetone_version();
			
			$default_options = array(
				
				'license_key' => '',

			);
			
			$input = wp_parse_args($input,$default_options);
			
			
			$input['license_key'] = sanitize_text_field($input['license_key']);
			
			return $input;
		}
		
		function register_mysettings() {
			//register settings
			register_setting( 'onetone-settings-group', 'onetone_companion_options', array(&$this,'text_validate') );
		}
		
		
		function settings_page( ) {
			
			$onetone_customizer_section = self::check_onetone_version();
			
			$theme_textdomain = self::get_theme_textdomain();

			$tabs = array( 'license-key'   => esc_html__( 'License Key', 'onetone-companion' ));
			
			
			$current = 'license-key';
			if(isset($_GET['tab']))
				$current = $_GET['tab'];
				
				$html = '<h2 class="nav-tab-wrapper">';
				foreach( $tabs as $tab => $name ){
					$class = ( $tab == $current ) ? 'nav-tab-active' : '';
					$html .= '<a class="nav-tab ' . $class . '" href="?page=onetone-license&tab=' . $tab . '">' . $name . '</a>';
				}
				$html .= '</h2>';
		
				
					?>
					<div class="wrap">
					<?php echo $html;?>
					
					<form method="post" action="options.php">
						<?php
						
						settings_fields( 'onetone-settings-group' );
						$options     = get_option('onetone_companion_options',OnetoneCompanion::default_options());
						$onetone_companion_options = wp_parse_args($options,OnetoneCompanion::default_options());
						?>
						
							
        <div class="oc-license-key onetone-license-box" style=" <?php if($tab != 'license-key') {echo 'display:none;';} ?>">
        <?php OnetoneCompanion::license( $onetone_companion_options );?>
                        
        </div>
						
					
			</form>
			</div>
	<?php
		
		}
		
		
		function contact_form( $atts, $content = "" ) {
			$atts = shortcode_atts( array(
				'receiver' => get_option('admin_email'),
				'button_text' => __( 'Post', 'onetone-companion' ),
				'checkbox' => 0,
				'checkbox_prompt' => __( 'Please check the checkbox.', 'onetone-companion' ),
			), $atts, 'oc_contact' );
			
			extract($atts);

			$html = '<form class="contact-form" action="" method="post">
                      <input id="name" tabindex="1" name="name" size="22" type="text" value="" placeholder="'.esc_attr__('Name', 'onetone-companion').'" />
                      <input id="email" tabindex="2" name="email" size="22" type="text" value="" placeholder="'.esc_attr__('Email', 'onetone-companion').'" />
                      <textarea id="message" tabindex="4" cols="39" name="x-message" rows="7" placeholder="'.esc_attr__('Message', 'onetone-companion').'"></textarea>
					  '.(($checkbox == 1) ?'<div style="display: inline-block;width: 100%;"><input style="float: left; width: auto; margin-left:5px; margin-top: 8px;" type="checkbox" name="contact-form-checkbox" class="contact-form-checkbox" value="1" aria-invalid="false"><span class="onetone-contact-form-checkbox" style="float: left;padding-left: 15px;">'.wp_kses_post($content).'</span><span class="hide checkbox-notice" >'.wp_kses_post($checkbox_prompt).'</span></div>':'').
					  
                     '<input id="sendto" name="sendto" type="hidden" value="'.sanitize_email($receiver).'" />
                      <input id="submit" name="submit" type="button" value="'. esc_attr($button_text).'" />
                      </form>';
			return $html;
		}
		
		
	function onetone_contact(){
			
			if(trim($_POST['Name']) === '') {
				$Error = __('Please enter your name.','onetone-companion');
				$hasError = true;
			} else {
				$name = trim($_POST['Name']);
			}
		
			if(trim($_POST['Email']) === '')  {
				$Error = __('Please enter your email address.','onetone-companion');
				$hasError = true;
			} else if (!preg_match("/^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,4}$/i", trim($_POST['Email']))) {
				$Error = __('You entered an invalid email address.','onetone-companion');
				$hasError = true;
			} else {
				$email = trim($_POST['Email']);
			}
		
			if(trim($_POST['Message']) === '') {
				$Error =  __('Please enter a message.','onetone-companion');
				$hasError = true;
			} else {
				if(function_exists('stripslashes')) {
					$message = stripslashes(trim($_POST['Message']));
				} else {
					$message = trim($_POST['Message']);
				}
			}
		
			if(!isset($hasError)) {
			   if (isset($_POST['sendto']) && preg_match("/^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,4}$/i", trim($_POST['sendto']))) {
				 $emailTo = $_POST['sendto'];
			   }
			   else{
				 $emailTo = get_option('admin_email');
				}
				
			   if($emailTo !=""){
					$subject = 'From '.$name;
					$body = "Name: $name \n\nEmail: $email \n\nMessage: $message";
					$headers = 'From: '.$name.' <'.$emailTo.'>' . "\r\n" . 'Reply-To: ' . $email;
		
					wp_mail($emailTo, $subject, $body, $headers);
					$emailSent = true;
				}
				echo json_encode(array("msg"=>__("Your message has been successfully sent!","onetone-companion"),"error"=>0));
				
			}
			else
			{
				echo json_encode(array("msg"=>$Error,"error"=>1));
			}
			die() ;
		}
		
		
		/*
		  *  Allow tags
		  */
		  
	function html_tags_code() {
			  
		global $allowedposttags;
	
		$allowed_atts = array(
			'align'      => array(),
			'class'      => array(),
			'type'       => array(),
			'id'         => array(),
			'dir'        => array(),
			'lang'       => array(),
			'style'      => array(),
			'xml:lang'   => array(),
			'src'        => array(),
			'alt'        => array(),
			'href'       => array(),
			'rel'        => array(),
			'rev'        => array(),
			'target'     => array(),
			'novalidate' => array(),
			'type'       => array(),
			'value'      => array(),
			'name'       => array(),
			'tabindex'   => array(),
			'action'     => array(),
			'method'     => array(),
			'for'        => array(),
			'width'      => array(),
			'height'     => array(),
			'data'       => array(),
			'title'      => array(),
			'border'      => true,
			'frameborder' => true,
			"allowfullscreen" => array(),
			"allowscriptaccess" => array(),
			"media" => array(),
			"placeholder" => array(),
			"required" => array(),
			"aria-required" => array(),
		);
		$allowedposttags['form']   = $allowed_atts;
		$allowedposttags["script"] = $allowed_atts;
		$allowedposttags['iframe'] = $allowed_atts;
		$allowedposttags["object"] = $allowed_atts;
		$allowedposttags["param"]  = $allowed_atts;
		$allowedposttags['i'] = $allowed_atts;
		$allowedposttags["embed"] = $allowed_atts;
		$allowedposttags["style"] = $allowed_atts;
		$allowedposttags["link"] = $allowed_atts;
		$allowedposttags["input"] = $allowed_atts;
		$allowedposttags["select"] = $allowed_atts;
		$allowedposttags["textarea"] =$allowed_atts;
	}
	  
 }

}

new OnetoneCompanion();