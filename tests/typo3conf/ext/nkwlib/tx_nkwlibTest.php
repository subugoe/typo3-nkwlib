<?php

require_once dirname(__FILE__) . '/../../../../class.tx_nkwlib.php';

/**
 * Test class for tx_nkwlib.
 * Generated by PHPUnit on 2011-05-27 at 11:28:16.
 */
class tx_nkwlibTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var tx_nkwlib
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$this->object = new tx_nkwlib;
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown() {
		
	}

	/**
	 * Testet ob der Erste Buchstabe korrekt erzeugt wird
	 */
	public function testGetFirstLetter() {

		$actual = $this->object->getFirstLetter('SUB');

		$expected = "S";
		$unexpected = "s";
		$this->assertEquals($expected, $actual);
		$this->assertNotEquals($unexpected, $actual);
	}

	/**
	 * Testet ob der Erste Buchstabe mit einem Umlaut korrekt erzeugt wird
	 */
	public function testGetFirstLetterUmlaut() {

		$actual = $this->object->getFirstLetter('Öffentlichkeit');

		$expected = "Ö";
		$unexpected = "o";
		$this->assertEquals($expected, $actual);
		$this->assertNotEquals($unexpected, $actual);
	}

	/**
	 * @todo Implement testGeocodeAddress().
	 */
	public function testGeocodeAddress() {

		$actual = $this->object->geocodeAddress("Platz der Goettinger 7, 37073 Goettingen");

		$this->assertNotNull($actual);
	}

	/**
	 * @todo Implement testGetPageUrl().
	 */
	public function testGetPageUrl() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testSetLanguage().
	 */
	public function testSetLanguage() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testGetLanguage().
	 */
	public function testGetLanguage() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testGetPageUID().
	 */
	public function testGetPageUID() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testGetLanguageStr().
	 */
	public function testGetLanguageStr() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testKeywordsForPage().
	 */
	public function testKeywordsForPage() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testGetPageTitle().
	 */
	public function testGetPageTitle() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testPageInfo().
	 */
	public function testPageInfo() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testKnotID().
	 */
	public function testKnotID() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testGetPageTreeIds().
	 */
	public function testGetPageTreeIds() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testGetPageChildIds().
	 */
	public function testGetPageChildIds() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testPageHasChild().
	 */
	public function testPageHasChild() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testAlphaListFromArray().
	 */
	public function testAlphaListFromArray() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testCheckForAlienContent().
	 */
	public function testCheckForAlienContent() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testQueryStartEndTime().
	 */
	public function testQueryStartEndTime() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testPageContent().
	 */
	public function testPageContent() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testPageKeywordsList().
	 */
	public function testPageKeywordsList() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testFormatString().
	 */
	public function testFormatString() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testHTime().
	 */
	public function testHTime() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testHReturnFormatDate().
	 */
	public function testHReturnFormatDate() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testHReturnFormatDateSortable().
	 */
	public function testHReturnFormatDateSortable() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testGetPluginConf().
	 */
	public function testGetPluginConf() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testDPrint().
	 */
	public function testDPrint() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testGenerateMarker().
	 */
	public function testGenerateMarker() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}

}

?>
