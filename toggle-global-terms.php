<?php

// Plugin Name: Toggle global_terms_enabled

$toggle_global_terms_enabled = new Toggle_Global_Terms_Enabled;

class Toggle_Global_Terms_Enabled {

	function __construct() {
		add_action( 'init', array( $this, 'init' ) );
	}

	function init() {
		if ( ! is_multisite() || ! is_super_admin() ) return;

		// create menu button
		add_action( 'admin_bar_menu',              array( $this, 'insert_toolbar_item' ), 99 );
		// register ajax callback
		add_action( 'wp_ajax_toggle_global_terms', array( $this, 'ajax_callback' ) );
		// put js in footer
		add_action( 'admin_print_footer_scripts',  array( $this, 'scripts' ) );
	}

	function get_title( $status ) {
		$label = $status ? __( 'enabled', 'fills in a blank, see below', 'toggle-global-terms' ) : __( 'disabled', 'fills in a blank, see below', 'toggle-global-terms');
		return sprintf( __( 'Global Terms is %s', 'toggle-global-terms' ), $label );
	}

	function insert_toolbar_item( $wp_admin_bar ) {
		$wp_admin_bar->add_menu( array(
			'id'    => 'toggle-global-terms',
			'title' => $this->get_title( global_terms_enabled() ),
			'href'  => '#',
			'meta'  => array(
				'title' => __( 'Toggle Global Terms', 'toggle-global-terms' ),
			),
		) );
	}

	function ajax_callback() {

		$status = !global_terms_enabled();
		update_site_option( 'global_terms_enabled', $status );

		die( $this->get_title( $status ) );

	}

	function scripts() {
		?><script>
jQuery(document).ready(function($) {
	$('#wp-admin-bar-toggle-global-terms a').on( 'click', function( ev ) {
		var menu = $(this);
		ev.preventDefault();
		$.post( ajaxurl, {
			action: 'toggle_global_terms'
		}, function( r ) {
			menu.text( r );
		} );
	});
});
		</script><?php
	}
}
