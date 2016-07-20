<?php
/**
 * Plugin Name: BDC Facebook Embed
 * Plugin URI: http://www.boston.com
 * Description: Allow authors to embed facebook posts into pages / posts
 * Inspired by the Jetpack implementation
 * Version: 0.1.0
 * Author: Greg Opperman
 * Author URI: http://www.boston.com
 *
 * @package bdc.facebook-embed
 * @version 0.1.0
 * @author Greg Opperman <gregory.opperman@globe.com>
 */

define( 'BDC_FACEBOOK_EMBED_REGEX', '#^https?://(www.)?facebook\.com/([^/]+)/(posts|photos)/([^/]+)?#' );
define( 'BDC_FACEBOOK_ALTERNATE_EMBED_REGEX', '#^https?://(www.)?facebook\.com/permalink.php\?([^\s]+)#' );
define( 'BDC_FACEBOOK_PHOTO_EMBED_REGEX', '#^https?://(www.)?facebook\.com/photo.php\?([^\s]+)#' );
define( 'BDC_FACEBOOK_PHOTO_ALTERNATE_EMBED_REGEX', '#^https?://(www.)?facebook\.com/([^/]+)/photos/([^/]+)?#' );
define( 'BDC_FACEBOOK_VIDEO_EMBED_REGEX', '#^https?://(www.)?facebook\.com/video.php\?([^\s]+)#' );
define( 'BDC_FACEBOOK_VIDEO_ALTERNATE_EMBED_REGEX', '#^https?://(www.)?facebook\.com/([^/]+)/videos/([^/]+)?#' );

class BDC_Facebook_Embed {
	/**
	 * Set up the default handlers for embedding
	*/
	function __construct() {

		// Example URL: https://www.facebook.com/VenusWilliams/posts/10151647007373076
		wp_embed_register_handler( 'facebook', BDC_FACEBOOK_EMBED_REGEX, array( $this, 'facebook_embed_handler' ) );
		// Example URL: https://www.facebook.com/permalink.php?id=222622504529111&story_fbid=559431180743788
		wp_embed_register_handler( 'facebook-alternate', BDC_FACEBOOK_ALTERNATE_EMBED_REGEX, array( $this, 'facebook_embed_handler' ) );
		// Photos are handled on a different endpoint; e.g. https://www.facebook.com/photo.php?fbid=10151609960150073&set=a.398410140072.163165.106666030072&type=1
		wp_embed_register_handler( 'facebook-photo', BDC_FACEBOOK_PHOTO_EMBED_REGEX, array( $this, 'facebook_embed_handler' ) );
		// Photos (from pages for example) can be at
		wp_embed_register_handler( 'facebook-alternate-photo', BDC_FACEBOOK_PHOTO_ALTERNATE_EMBED_REGEX, array( $this, 'facebook_embed_handler' ) );
		// Videos e.g. https://www.facebook.com/video.php?v=772471122790796
		wp_embed_register_handler( 'facebook-video', BDC_FACEBOOK_VIDEO_EMBED_REGEX, array( $this, 'facebook_video_embed_handler' ) );
		// Videos  https://www.facebook.com/WhiteHouse/videos/10153398464269238/
		wp_embed_register_handler( 'facebook-alternate-video', BDC_FACEBOOK_VIDEO_ALTERNATE_EMBED_REGEX, array( $this, 'facebook_video_embed_handler' ) );

		add_shortcode( 'facebook', array( $this, 'facebook_shortcode_handler' ) );
	}

	function facebook_embed_handler( $matches, $attr, $url ) {
		$embed = sprintf( '<div class="fb-post" data-href="%s" data-embed-provider="facebook"></div>', esc_url( $url ) );
		return $this->load_sdk( $embed );
	}

	function facebook_video_embed_handler( $matches, $attr, $url ) {
		$embed = sprintf( '<div class="fb-video" data-allowfullscreen="true" data-href="%s"></div>', esc_url( $url ) );
		return $this->load_sdk( $embed );
	}

	function load_sdk( $embed ) {
		// since Facebook is a faux embed, we need to load the JS SDK in the wpview embed iframe
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX && ! empty( $_POST['action'] ) && 'parse-embed' == $_POST['action'] ) {
			return $embed . '<script src="//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.2" async></script>';
		} else {
			wp_enqueue_script( 'bdc-facebook-embed', plugins_url( 'scripts/facebook.js', __FILE__ ), array( 'jquery' ), null, true );
			return $embed;
		}
	}
	function facebook_shortcode_handler( $atts ) {
		global $wp_embed;
		if ( empty( $atts['url'] ) ) {
			return;
		}
		if ( ! preg_match( BDC_FACEBOOK_EMBED_REGEX, $atts['url'] )
		&& ! preg_match( BDC_FACEBOOK_ALTERNATE_EMBED_REGEX, $atts['url'] )
		&& ! preg_match( BDC_FACEBOOK_PHOTO_EMBED_REGEX, $atts['url'] )
		&& ! preg_match( BDC_FACEBOOK_VIDEO_EMBED_REGEX, $atts['url'] )
		&& ! preg_match( BDC_FACEBOOK_VIDEO_ALTERNATE_EMBED_REGEX, $atts['url'] )  ) {
			return;
		}
		return $wp_embed->shortcode( $atts, $atts['url'] );
	}
}

new BDC_Facebook_Embed;
