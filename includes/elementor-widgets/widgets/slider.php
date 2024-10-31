<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Mageewp_Widget_Slider extends Widget_Base {
	
	public function get_categories() {
		return [ 'onetone-elements' ];
	}
	
   public function get_name() {
      return 'onetone-slider';
   }

   public function get_title() {
      return __( 'Slider', 'onetone-companion' );
   }

   public function get_icon() { 
        return 'eicon-wordpress';
   }

   protected function _register_controls() {
		$this->start_controls_section(
			'section_onetone_slider',
			[
				'label' => __( 'Slide Items', 'onetone-companion' ),
			]
		);

		$this->add_control(
			'onetone_slider',
			[
				'label' => '',
				'type' => Controls_Manager::REPEATER,
				'default' => [
					
				],
				'fields' => [
					
					[
						'name' => 'image',
						'label' => __( 'Image', 'onetone-companion' ),
						'type' => Controls_Manager::MEDIA,
						'label_block' => true,
						'default' => '',
					],
					
					[
						'name' => 'text',
						'label' => __( 'Description', 'onetone-companion' ),
						'type' => Controls_Manager::WYSIWYG,
						'label_block' => true,
						'placeholder' => __( 'Description', 'onetone-companion' ),
						'default' => __( 'Description', 'onetone-companion' ),
					],
					
					[
						'name' => 'btn_txt',
						'label' => __( 'Button Text', 'onetone-companion' ),
						'type' => Controls_Manager::TEXT,
						'label_block' => true,
						'placeholder' => '',
					],
					[
						'name' => 'btn_link',
						'label' => __( 'Button Link', 'onetone-companion' ),
						'type' => Controls_Manager::URL,
						'placeholder' => __( 'https://your-link.com', 'onetone-companion' ),
						'show_external' => true,
						'default' => [
							'url' => '',
							'is_external' => true,
							'nofollow' => true,
						],
					],

				],
				//'title_field' => '<i class="{{ icon }}" aria-hidden="true"></i> {{{ text }}}',
			]
		);
		
		
		$this->add_control(
			'slider_control',
			[
				'label' => __( 'Slider Control', 'onetone-companion' ),
				'type' => Controls_Manager::SWITCHER,
				'label_block' => true,
				'label_on' => __( 'Show', 'onetone-companion' ),
				'label_off' => __( 'Hide', 'onetone-companion' ),
				'return_value' => '1',
				'default' => '1',
			]
		);
		
		$this->add_control(
			'slider_pagination',
			[
				'label' => __( 'Slider Pagination', 'onetone-companion' ),
				'type' => Controls_Manager::SWITCHER,
				'label_block' => true,
				'label_on' => __( 'Show', 'onetone-companion' ),
				'label_off' => __( 'Hide', 'onetone-companion' ),
				'return_value' => '1',
				'default' => '1',
			]
		);
		
		$this->add_control(
			'slide_autoplay',
			[
				'label' => __( 'AutoPlay', 'onetone-companion' ),
				'type' => Controls_Manager::SWITCHER,
				'label_block' => true,
				'label_on' => __( 'Yes', 'onetone-companion' ),
				'label_off' => __( 'No', 'onetone-companion' ),
				'return_value' => '1',
				'default' => '1',
			]
		);
		
		$this->add_control(
			'slide_speed',
			[
				'label' => __( 'Speed', 'onetone-companion' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => '3000',
			]
		);
		
		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_slider',
			[
				'label' => __( 'Style', 'onetone-companion' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		

		$this->add_control(
			'slider_color',
			[
				'label' => __( 'Color', 'onetone-companion' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} #onetone-e-owl-slider,{{WRAPPER}} #onetone-e-owl-slider div,{{WRAPPER}} #onetone-e-owl-slider h2,{{WRAPPER}} #onetone-e-owl-slider p,{{WRAPPER}} #onetone-e-owl-slider .btn' => 'color: {{VALUE}};',
					'{{WRAPPER}} #onetone-e-owl-slider .btn, {{WRAPPER}} #onetone-e-owl-slider .owl-dot, {{WRAPPER}} #onetone-e-owl-slider .owl-prev:before, {{WRAPPER}} #onetone-e-owl-slider .owl-next:before' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} #onetone-e-owl-slider .owl-dot.active' => 'background-color: {{VALUE}};',
				],
				
			]
		);
		


	}

   protected function render( $instance = [] ) {

      // get our input from the widget settings.

	 $settings = $this->get_settings();

     $return = '<section class="homepage-slider"><div id="onetone-e-owl-slider" class="owl-carousel">';
	
	$i = 1;

	if (is_array($settings['onetone_slider']) && !empty($settings['onetone_slider']) ){
		
		foreach( $settings['onetone_slider'] as $index => $slide ){

		 $active     = '';
		 $text       = $slide['text'];
		 $image      = $slide['image'];
		 $btn_txt    = $slide['btn_txt'];
		 $btn_link   = $slide['btn_link'];
		 
		 if (is_numeric($image)) {
					$image_attributes = wp_get_attachment_image_src($image, 'full');
					$image       = $image_attributes[0];
				  }
		 
		 $target = $btn_link['is_external'] ? ' target="_blank"' : '';
		 $nofollow = $btn_link['nofollow'] ? ' rel="nofollow"' : '';
		
		 $btn_str    = '';
		 
		 if( $btn_txt != '' ){
			 
			 $btn_str    = '<br/><a class="btn" '.$target.$nofollow.' href="'.esc_url($btn_link['url']).'">'.do_shortcode(wp_kses_post($btn_txt)).'</a>';
			 
			 }
		
		 if( isset($image['url']) && $image['url'] != "" ){
			  $return .= '<div class="item"><img src="'.esc_url($image['url']).'" alt="Slide image '.$i.'"><div class="inner"><div class="caption"><div class="caption-inner">'. do_shortcode(wp_kses_post($text)) .$btn_str.'</div></div></div></div>';
			  $i++;
	       }

	}
			}
	
		$return .= '</div></section>';
		
		$slider_control = (isset($settings['slider_control']  )&& $settings['slider_control'] !='')? $settings['slider_control']:1;
		$slider_pagination = (isset($settings['slider_pagination'] ) && $settings['slider_pagination'] !='')? $settings['slider_pagination']:0;
		$slide_autoplay = (isset($settings['slide_autoplay'] ) && $settings['slide_autoplay'] !='')? $settings['slide_autoplay']:0;
		$slide_speed = (isset($settings['slide_speed'] ) && $settings['slide_speed'] !='')? $settings['slide_speed']:'3000';
		
		$return .= "<script> jQuery(document).ready(function($){
			
			var onetoneESlider = function(){
				var slider_control = ".$slider_control.",
				slider_pagination = ".$slider_pagination.",
				slide_autoplay = ".$slide_autoplay.",
				slide_speed = ".$slide_speed.";
				
				$('#onetone-e-owl-slider').owlCarousel({
					nav:(slider_control == '1' || slider_control == 'yes')?true:false,
					navigation:(slider_control == '1' || slider_control == 'yes')?true:false,
					dots:(slider_pagination == '1' || slider_pagination == 'yes')?true:false,
					slideSpeed : 300,
					items:1,
					autoplay:(slide_autoplay == '1'|| slide_autoplay == 'yes')?true:false,
					margin:0,
					loop:true,
					paginationSpeed : 400,
					singleItem:true,
					autoplayTimeout:parseInt(slide_speed)
			});
			}
			onetoneESlider();
		$( window ).on( 'elementor/frontend/init', function() {
			onetoneESlider();
			});
	});</script>";
		
		echo $return;


   }

   protected function content_template() {}

   public function render_plain_content( $instance = [] ) {}

}
Plugin::instance()->widgets_manager->register_widget_type( new Mageewp_Widget_Slider );
