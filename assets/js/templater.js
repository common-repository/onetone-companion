( function( $ ) {
	var $document = $( document );

	wp = wp || {};

	/**
	 * The WP Updates object.
	 *
	 * @type {object}
	 */
	wp.updates = wp.updates || {};
	
	console.log(wp.updates);
	
	// Handle sidebar collapse in preview.
	$( '.mageewp-template-preview' ).on(
		'click', '.collapse-sidebar', function () {
			event.preventDefault();
			var overlay = $( '.mageewp-template-preview' );
			if ( overlay.hasClass( 'expanded' ) ) {
				overlay.removeClass( 'expanded' );
				overlay.addClass( 'collapsed' );
				return false;
			}

			if ( overlay.hasClass( 'collapsed' ) ) {
				overlay.removeClass( 'collapsed' );
				overlay.addClass( 'expanded' );
				return false;
			}
		}
	);

	// Handle responsive buttons.
	$( '.mageewp-responsive-preview' ).on(
		'click', 'button', function () {
			$( '.mageewp-template-preview' ).removeClass( 'preview-mobile preview-tablet preview-desktop' );
			var deviceClass = 'preview-' + $( this ).data( 'device' );
			$( '.mageewp-responsive-preview button' ).each(
				function () {
					$( this ).attr( 'aria-pressed', 'false' );
					$( this ).removeClass( 'active' );
				}
			);

			$( '.mageewp-responsive-preview' ).removeClass( $( this ).attr( 'class' ).split( ' ' ).pop() );
			$( '.mageewp-template-preview' ).addClass( deviceClass );
			$( this ).addClass( 'active' );
		}
	);

	// Hide preview.
	$( '.close-full-overlay' ).on(
		'click', function () {
			$( '.mageewp-template-preview .mageewp-theme-info.active' ).removeClass( 'active' );
			$( '.mageewp-template-preview' ).hide();
			$( '.mageewp-template-frame' ).attr( 'src', '' );
			$('body.mageewp-companion_page_mageewp-template').css({'overflow-y':'auto'});
		}
	);
			
	// Open preview routine.
	$( '.mageewp-preview-template' ).on(
		'click', function () {
			$('.import-return-info').remove();
			var templateSlug = $( this ).data( 'template-slug' );
			var previewUrl = $( this ).data( 'demo-url' );
			$( '.mageewp-template-frame' ).attr( 'src', previewUrl );
			$( '.mageewp-theme-info.' + templateSlug ).addClass( 'active' );
			setupImportButton();
			$( '.mageewp-template-preview' ).fadeIn();
			$('body.mageewp-companion_page_mageewp-template').css({'overflow-y':'hidden'});
		}
	);
	
	$(document).on('click', '.mageewp-preview-site',
		 function () {
			$('.import-return-info').remove();
			var siteSlug = $( this ).data( 'site-slug' );
			var previewUrl = $( this ).data( 'demo-url' );
			$( '.mageewp-template-frame' ).attr( 'src', previewUrl );
			$( '.mageewp-theme-info.' + siteSlug ).addClass( 'active' );
			setupImportSiteButton();
			$( '.mageewp-template-preview' ).fadeIn();
			$('body.mageewp-companion_page_mageewp-template').css({'overflow-y':'hidden'});
		}
	);
	
	
	$( '.mageewp-next-prev .next-theme' ).on(
				'click', function () {
					var active = $( '.mageewp-theme-info.active' ).removeClass( 'active' );
					if ( active.next() && active.next().length ) {
						active.next().addClass( 'active' );
					} else {
						active.siblings( ':first' ).addClass( 'active' );
					}
					changePreviewSource();
					setupImportButton();
				}
			);
			$( '.mageewp-next-prev .previous-theme' ).on(
				'click', function () {
					var active = $( '.mageewp-theme-info.active' ).removeClass( 'active' );
					if ( active.prev() && active.prev().length ) {
						active.prev().addClass( 'active' );
					} else {
						active.siblings( ':last' ).addClass( 'active' );
					}
					changePreviewSource();
					setupImportButton();
				}
			);

			// Change preview source.
			function changePreviewSource() {
				var previewUrl = $( '.mageewp-theme-info.active' ).data( 'demo-url' );
				$( '.mageewp-template-frame' ).attr( 'src', previewUrl );
			}
	
	function setupImportButton() {
		var installable = $( '.active .mageewp-installable' );
		if ( installable.length > 0 ) {
			$( '.wp-full-overlay-header .mageewp-import-template' ).text( onetone_companion_admin.i18n.t1 );
		} else {
			$( '.wp-full-overlay-header .mageewp-import-template' ).text( onetone_companion_admin.i18n.t2 );
		}
		var activeTheme = $( '.mageewp-theme-info.active' );
		var button = $( '.wp-full-overlay-header .mageewp-import-template' );
		$( button ).attr( 'data-template-file', $( activeTheme ).data( 'template-file' ) );
		$( button ).attr( 'data-template-title', $( activeTheme ).data( 'template-title' ) );
		$( button ).attr( 'data-template-slug', $( activeTheme ).data( 'template-slug' ) );
		
		if($( activeTheme ).data( 'template-file' ) == '' ){
				$('.mageewp-buy-now').show();
				$('.mageewp-import-template').hide();
			}else{
				$('.mageewp-buy-now').hide();
				$('.mageewp-import-template').show();
				}
	}
	
	function setupImportSiteButton() {
		var installable = $( '.active .mageewp-installable' );
		
		$('.mageewp-import-button').addClass('mageewp-import-site');
		if ( installable.length > 0 ) {
			$( '.wp-full-overlay-header .mageewp-import-site' ).text( onetone_companion_admin.i18n.t3 );
		} else {
			$( '.wp-full-overlay-header .mageewp-import-site' ).text( onetone_companion_admin.i18n.t4 );
		}
		var activeTheme = $( '.mageewp-theme-info.active' );
		var button = $( '.wp-full-overlay-header .mageewp-import-site' );
		$( button ).attr( 'data-demo-url', $( activeTheme ).data( 'demo-url' ) );
		$( button ).attr( 'data-site-wxr', $( activeTheme ).data( 'site-wxr' ) );
		$( button ).attr( 'data-site-title', $( activeTheme ).data( 'site-title' ) );
		$( button ).attr( 'data-site-slug', $( activeTheme ).data( 'site-slug' ) );
		
		$( button ).attr( 'data-template-slug', $( activeTheme ).data( 'template-slug' ) );
		$( button ).attr( 'data-site-options', $( activeTheme ).data( 'site-options' ) );
		$( button ).attr( 'data-site-widgets', $( activeTheme ).data( 'site-widgets' ) );
		$( button ).attr( 'data-site-customizer', $( activeTheme ).data( 'site-customizer' ) );
							 
		
		if($( activeTheme ).data( 'site-wxr' ) == '' ){
				$('.mageewp-buy-now').show();
				$('.mageewp-import-site').hide();
			}else{
				$('.mageewp-buy-now').hide();
				$('.mageewp-import-site').show();
				}
	}
	
	
	// Handle import click.
	$( '.wp-full-overlay-header' ).on(
		'click', '.mageewp-import-template', function () {
			$( this ).addClass( 'mageewp-import-queue updating-message mageewp-updating' ).html( '' );
			$( '.mageewp-template-preview .close-full-overlay, .mageewp-next-prev' ).remove();
			var template_url = $( this ).data( 'template-file' );
			var template_name = $( this ).data( 'template-title' );
			var template_slug = $( this ).data( 'template-slug' );
			
			if ( $( '.active .mageewp-installable' ).length || $( '.active .mageewp-activate' ).length ) {

				checkAndInstallPlugins();
			} else {
				$.ajax(
					{
						url: onetone_companion_admin.ajaxurl,
						beforeSend: function ( xhr ) {
							$( '.mageewp-import-queue' ).addClass( 'mageewp-updating' ).html( '' );
							xhr.setRequestHeader( 'X-WP-Nonce', onetone_companion_admin.nonce );
						},
						// async: false,
						data: {
							template_url: template_url,
							template_name: template_name,
							template_slug: template_slug,
							action: 'mageewp_import_elementor'
						},
						type: 'POST',
						success: function ( data ) {
							console.log( 'success' );
							console.log( data );
							$( '.mageewp-updating' ).replaceWith( '<span class="mageewp-done-import"><i class="dashicons-yes dashicons"></i></span>' );
							var obj = $.parseJSON( data );
							
							location.href = obj.redirect_url;
						},
						error: function ( error ) {
							console.log( 'error' );
						},
						complete: function() {
							$( '.mageewp-updating' ).replaceWith( '<span class="mageewp-done-import"><i class="dashicons-yes dashicons"></i></span>' );
						}
					}, 'json'
				);
			}
		}
	);

	function checkAndInstallPlugins() {
		var installable = $( '.active .mageewp-installable' );
		var toActivate = $( '.active .mageewp-activate' );
		if ( installable.length || toActivate.length ) {

			$( installable ).each(
				function () {
					var plugin = $( this );
					$( plugin ).removeClass( 'mageewp-installable' ).addClass( 'mageewp-installing' );
					$( plugin ).find( 'span.dashicons' ).replaceWith( '<span class="dashicons dashicons-update" style="-webkit-animation: rotation 2s infinite linear; animation: rotation 2s infinite linear; color: #ffb227 "></span>' );
					var slug = $( this ).find( '.mageewp-install-plugin' ).attr( 'data-slug' );
					
					if ( wp.updates.shouldRequestFilesystemCredentials && ! wp.updates.ajaxLocked ) {
						  wp.updates.requestFilesystemCredentials( event );
		  
						  $document.on( 'credential-modal-cancel', function() {
							  var $message = $( '.install-now.mageewp-installing' );
		  
							  $message
								  .removeClass( 'mageewp-installing' )
								  .text( wp.updates.l10n.installNow );
		  
							  wp.a11y.speak( wp.updates.l10n.updateCancel, 'polite' );
						  } );
					  }
			
					wp.updates.installPlugin(
						{
							slug: slug,
							success: function ( response ) {
								$( '.install-now.mageewp-installing' ).text('Activating');
								activatePlugin( response.activateUrl, plugin );
							}
						}
					);
				}
			);

			$( toActivate ).each(
				function () {
						var plugin = $( this );
						var activateUrl = $( plugin ).find( '.activate-now' ).attr( 'href' );
					if (typeof activateUrl !== 'undefined') {
						activatePlugin( activateUrl, plugin );
					}
				}
			);
		}
	}

	function activatePlugin( activationUrl, plugin ) {
		$.ajax(
			{
				type: 'GET',
				url: activationUrl,
				beforeSend: function() {
					$( plugin ).removeClass( 'mageewp-activate' ).addClass( 'mageewp-installing' );
					$( plugin ).find( 'span.dashicons' ).replaceWith( '<span class="dashicons dashicons-update" style="-webkit-animation: rotation 2s infinite linear; animation: rotation 2s infinite linear; color: #ffb227 "></span>' );
					$( plugin ).find( '.activate-now' ).removeClass('activate-now  button-primary').addClass('button-activatting button-secondary').text('Activating').attr('href','#');
				},
				success: function () {
					$( plugin ).find( '.dashicons' ).replaceWith( '<span class="dashicons dashicons-yes" style="color: #34a85e"></span>' );
					$( plugin ).find( '.button-activatting' ).text('Activated');
					$( plugin ).removeClass( 'mageewp-installing' );
				},
				complete: function() {
					if ( $( '.active .mageewp-installing' ).length === 0 ) {
						$( '.install-now.mageewp-installing' ).text('Activated');
						$( '.mageewp-import-queue' ).trigger( 'click' );
						
					}
				}
			}
		);
	}
	
	
     
})( jQuery );