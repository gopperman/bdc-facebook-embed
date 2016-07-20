<?php
namespace Plugin_Facebook_Embed;

class BDC_Facebook_Embed_Integration_Test extends \WP_UnitTestCase {
	/**
	 * Test that the shortcode exists using WordPress's `shortcode_exists`
	 */
	function test_shortcode_added() {
		$this->assertTrue( shortcode_exists( 'facebook' ) );
	}
}
