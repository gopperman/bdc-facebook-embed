<?php
namespace Plugin_Facebook_Embed;

class BDC_Facebook_Embed_Unit_Test extends \WP_UnitTestCase {
	/**
	 * Store an instance of our plugin's object for testing direct calls to its
	 * methods
	 */
	private $bdc_facebook_embed = null;

	/**
	 * Create an instance of our plugin's object
	 */
	public function setUp() {
		// before
		parent::setUp();

		$this->bdc_facebook_embed = new \BDC_Facebook_Embed;
	}

	/**
	 * Test that the related links shortcode works. Hits all the regexes
	 */
	function test_facebook_embed_shortcode_works() {
		//Videos
		$content = '[facebook url="https://www.facebook.com/GONGOBONK/videos/910915958994666/"]';
		$output = do_shortcode( $content );
		$this->assertEquals( '<div class="fb-video" data-allowfullscreen="true" data-href="https://www.facebook.com/GONGOBONK/videos/910915958994666/"></div>', $output );

		// Video - alternate
		$content = '[facebook url="https://www.facebook.com/video.php?v=910915958994666"]';
		$output = do_shortcode( $content );
		$this->assertEquals( '<div class="fb-video" data-allowfullscreen="true" data-href="https://www.facebook.com/video.php?v=910915958994666"></div>', $output );

		// Photos
		$content = '[facebook url="https://www.facebook.com/photo.php?fbid=10151609960150073&set=a.398410140072.163165.106666030072&type=1"]';
		$output = do_shortcode( $content );
		$this->assertEquals( '<div class="fb-post" data-href="https://www.facebook.com/photo.php?fbid=10151609960150073&#038;set=a.398410140072.163165.106666030072&#038;type=1"></div>', $output );

		// Posts
		$content = '[facebook url="https://www.facebook.com/FacebookDevelopers/posts/10151471074398553"]';
		$output = do_shortcode( $content );
		$this->assertEquals( '<div class="fb-post" data-href="https://www.facebook.com/FacebookDevelopers/posts/10151471074398553"></div>', $output );

		// Post - alternate
		$allowed_html = array(
			'div' => array(
				'class' => array(),
				'data-href' => array(),
			),
		);
		$content = '[facebook url="https://www.facebook.com/permalink.php?id=222622504529111"]';
		$output = do_shortcode( $content );
		$this->assertEquals( wp_kses( '<div class="fb-post" data-href="https://www.facebook.com/permalink.php?id=222622504529111"></div>', $allowed_html ), $output );

		// Bad URL should return nothing
		$content = '[facebook url="https://boston.com"]';
		$output = do_shortcode( $content );
		$this->assertEquals( '', $output );
	}
}
