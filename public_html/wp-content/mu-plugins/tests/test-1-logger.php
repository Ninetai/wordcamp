<?php

namespace WordCamp\Logger\Tests;
use function WordCamp\Logger\{ redact_keys };
use WP_UnitTestCase;

defined( 'WPINC' ) || die();

/**
 * @group mu-plugins
 * @group logger
 */
class Test_Logger extends WP_UnitTestCase {
	/**
	 * Setup conditions for all tests.
	 */
	public static function wpSetUpBeforeClass() : void {
		require_once dirname( __DIR__ ) . '/1-logger.php';
	}

	/**
	 * @covers WordCamp\Logger\redact_keys
	 *
	 * @dataProvider data_redact_keys
	 */
	public function test_redact_keys( array $data, array $expected ) : void {
		$actual = $data;
		redact_keys( $actual );

		$this->assertSame( $expected, $actual );
	}

	/**
	 * Data provider for `test_redact_keys()`.
	 */
	public function data_redact_keys() : array {
		$cases = array(
			'exact' => array(
				'input' => array(
					'foo'           => 'not a secret',
					'Authorization' => 'secret',
					'key'           => 'secret',
					'user_pass'     => 'secret',
					'pwd'           => 'secret',
					'pass1-text'    => 'secret',
					'pass1'         => 'secret',
					'pass2'         => 'secret',
					'bar'           => 'not a secret',
				),
				'expected' => array(
					'foo'           => 'not a secret',
					'Authorization' => '[redacted]',
					'key'           => '[redacted]',
					'user_pass'     => '[redacted]',
					'pwd'           => '[redacted]',
					'pass1-text'    => '[redacted]',
					'pass1'         => '[redacted]',
					'pass2'         => '[redacted]',
					'bar'           => 'not a secret',
				),
			),

			'fuzzy' => array(
				'input' => array(
					'foo'             => 'not a secret',
					'password1'       => 'secret',
					'my_password_var' => 'secret',
					'user_password'   => 'secret',
					'my_Nonce'        => 'secret',
					'nonce_action'    => 'secret',
					'api_key'         => 'secret',
					'my_apikey'       => 'secret',
					'secret_sauce'    => 'secret',
					'bar'             => 'not a secret',
				),
				'expected' => array(
					'foo'             => 'not a secret',
					'password1'       => '[redacted]',
					'my_password_var' => '[redacted]',
					'user_password'   => '[redacted]',
					'my_Nonce'        => '[redacted]',
					'nonce_action'    => '[redacted]',
					'api_key'         => '[redacted]',
					'my_apikey'       => '[redacted]',
					'secret_sauce'    => '[redacted]',
					'bar'             => 'not a secret',
				),
			),

			'case-insensitive' => array(
				'input' => array(
					'Foo'           => 'not a secret',
					'authorization' => 'secret',
					'my_apikey'     => 'secret',
				),
				'expected' => array(
					'Foo'           => 'not a secret',
					'authorization' => '[redacted]',
					'my_apikey'     => '[redacted]',
				),
			),
		);

		return $cases;
	}
}
