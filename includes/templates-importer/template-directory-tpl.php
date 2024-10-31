<?php

$preview_url = add_query_arg( 'mageewp_templates', '', home_url() );

$html = '';

if ( is_array( $templates_array ) ) {
	$html .= '<div class="mageewp-template-dir wrap">';
	$html .= '<h1 class="wp-heading-inline">' . esc_html__( 'OneTone Templates', 'onetone-companion' ) . '</h1>';
	$html .= '<div class="mageewp-template-browser">';

	foreach ( $templates_array as $template => $properties ) {
		$html .= '<div class="mageewp-template">';
		$html .= '<div class="more-details mageewp-preview-template" data-demo-url="' . esc_url( $properties['demo_url'] ) . '" data-template-slug="' . esc_attr( $template ) . '" ><span>' . esc_html__( 'More Details', 'onetone-companion' ) . '</span></div>';
		$html .= '<div class="mageewp-template-screenshot">';
		$html .= '<img src="' . esc_url( $properties['screenshot'] ) . '" alt="' . esc_html( $properties['title'] ) . '" >';
		$html .= '</div>'; // .mageewp-template-screenshot
		$html .= '<h2 class="template-name template-header">' . esc_html( $properties['title'] ) . (isset($properties['pro'] )&&$properties['pro']=='1'? apply_filters('mageewp_after_template_title','<span class="pro-template">Pro</span>'):'').'</h2>';
		$html .= '<div class="mageewp-template-actions">';

		if ( ! empty( $properties['demo_url'] ) ) {
			$html .= '<a class="button mageewp-preview-template" data-demo-url="' . esc_url( $properties['demo_url'] ) . '" data-template-slug="' . esc_attr( $template ) . '" >' . __( 'Preview', 'onetone-companion' ) . '</a>';
		}
		$html .= '</div>'; // .mageewp-template-actions
		$html .= '</div>'; // .mageewp-template
	}
	$html .= '</div>'; // .mageewp-template-browser
	$html .= '</div>'; // .mageewp-template-dir
	$html .= '<div class="wp-clearfix clearfix"></div>';
}// End if().

echo $html;
?>

<div class="mageewp-template-preview theme-install-overlay wp-full-overlay expanded" style="display: none;">
	<div class="wp-full-overlay-sidebar">
		<div class="wp-full-overlay-header">
			<button class="close-full-overlay"><span class="screen-reader-text"><?php _e( 'Close', 'onetone-companion' );?></span></button>
			<div class="mageewp-next-prev">
				<button class="previous-theme"><span class="screen-reader-text"><?php _e( 'Previous', 'onetone-companion' );?></span></button>
				<button class="next-theme"><span class="screen-reader-text"><?php _e( 'Next', 'onetone-companion' );?></span></button>
			</div>
            
			<span class="mageewp-import-template button button-primary"><?php _e( 'Import', 'onetone-companion' );?></span>
       
           
            <a target="_blank" class="mageewp-buy-now" href="<?php echo esc_url('https://mageewp.com/onetone-theme.html');?>"><span class="button orange"><?php _e( 'Purchase', 'onetone-companion' );?></span></a>
            
		</div>
		<div class="wp-full-overlay-sidebar-content">
			<?php
			foreach ( $templates_array as $template => $properties ) {
			?>
				<div class="install-theme-info mageewp-theme-info <?php echo esc_attr( $template ); ?>"
					 data-demo-url="<?php echo esc_url( $properties['demo_url'] ); ?>"
					 data-template-file="<?php echo esc_url( $properties['import_file'] ); ?>"
					 data-template-title="<?php echo esc_attr( $properties['title'] ); ?>" 
                     data-template-slug="<?php echo esc_attr( $template ); ?>">
					<h3 class="theme-name"><?php echo esc_attr( $properties['title'] ); ?></h3>
					<img class="theme-screenshot" src="<?php echo esc_url( $properties['screenshot'] ); ?>" alt="<?php echo esc_attr( $properties['title'] ); ?>">
					<div class="theme-details">
						<?php
						 	echo wp_kses_post( $properties['description'] );
						 ?>
					</div>
					<?php
					if ( ! empty( $properties['required_plugins'] ) && is_array( $properties['required_plugins'] ) ) {
					?>
					<div class="mageewp-required-plugins">
						<p><?php _e( 'Required Plugins', 'onetone-companion' );?></p>
						<?php
						foreach ( $properties['required_plugins'] as $details ) {
							$file_name = isset($details['init'])?$details['init']:'';
							$plugin_slug = isset($details['slug'])?$details['slug']:'';
							
							if ( MageewpTemplater::check_plugin_state( $plugin_slug,$file_name ) === 'install' ) {
								echo '<div class="mageewp-installable plugin-card-' . esc_attr( $plugin_slug ) . '">';
								echo '<span class="dashicons dashicons-no-alt"></span>';
								echo $details['name'];
								echo MageewpTemplater::get_button_html( $plugin_slug,$file_name );
								echo '</div>';
							} elseif ( MageewpTemplater::check_plugin_state( $plugin_slug,$file_name ) === 'activate' ) {
								echo '<div class="mageewp-activate plugin-card-' . esc_attr( $plugin_slug ) . '">';
								echo '<span class="dashicons dashicons-admin-plugins" style="color: #ffb227;"></span>';
								echo $details['name'];
								echo MageewpTemplater::get_button_html( $plugin_slug,$file_name );
								echo '</div>';
							} else {
								echo '<div class="mageewp-installed plugin-card-' . esc_attr( $plugin_slug ) . '">';
								echo '<span class="dashicons dashicons-yes" style="color: #34a85e"></span>';
								echo $details['name'];
								echo '</div>';
							}
						}
						?>
					</div>
					<?php
					}
					?>
				</div><!-- /.install-theme-info -->
			<?php } ?>
		</div>

		<div class="wp-full-overlay-footer">
			<button type="button" class="collapse-sidebar button" aria-expanded="true" aria-label="Collapse Sidebar">
				<span class="collapse-sidebar-arrow"></span>
				<span class="collapse-sidebar-label"><?php _e( 'Collapse', 'onetone-companion' ); ?></span>
			</button>
			<div class="devices-wrapper">
				<div class="devices mageewp-responsive-preview">
					<button type="button" class="preview-desktop active" aria-pressed="true" data-device="desktop">
						<span class="screen-reader-text"><?php _e( 'Enter desktop preview mode', 'onetone-companion' ); ?></span>
					</button>
					<button type="button" class="preview-tablet" aria-pressed="false" data-device="tablet">
						<span class="screen-reader-text"><?php _e( 'Enter tablet preview mode', 'onetone-companion' ); ?></span>
					</button>
					<button type="button" class="preview-mobile" aria-pressed="false" data-device="mobile">
						<span class="screen-reader-text"><?php _e( 'Enter mobile preview mode', 'onetone-companion' ); ?></span>
					</button>
				</div>
			</div>

		</div>
	</div>
	<div class="wp-full-overlay-main mageewp-main-preview">
		<iframe src="" title="Preview" class="mageewp-template-frame"></iframe>
	</div>
</div>
