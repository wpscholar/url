<?php

namespace wpscholar\Tests;

use PHPUnit\Framework\TestCase;
use wpscholar\Url;

class UrlTest extends TestCase {

	protected function setUp(): void {
		// Simulate server variables for getCurrentUrl tests
		$_SERVER['HTTP_HOST']   = 'example.com';
		$_SERVER['REQUEST_URI'] = '/test-path';
		$_SERVER['HTTPS']       = 'on';
	}

	protected function tearDown(): void {
		// Clean up server variables
		unset( $_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI'], $_SERVER['HTTPS'] );
	}

	public function testUrlParsing() {
		$url = new Url( 'https://user:pass@example.com:8080/path?param=value#section' );

		$this->assertEquals( 'https', $url->scheme );
		$this->assertEquals( 'example.com', $url->host );
		$this->assertEquals( 'user', $url->user );
		$this->assertEquals( 'pass', $url->pass );
		$this->assertEquals( '8080', $url->port );
		$this->assertEquals( '/path', $url->path );
		$this->assertEquals( 'param=value', $url->query );
		$this->assertEquals( 'section', $url->fragment );
	}

	public function testEmptyUrlDefaultsToCurrentUrl() {
		$url = new Url();
		$this->assertEquals( 'https://example.com/test-path', $url->toString() );
	}

	public function testQueryParameterManipulation() {
		$url = new Url( 'https://example.com/path?param=value&existing=test' );

		// Test adding query parameter
		$url->addQueryVar( 'new_param', 'value' );
		$this->assertEquals( 'value', $url->getQueryVar( 'new_param' ) );

		// Test removing query parameter
		$url->removeQueryVar( 'param' );
		$this->assertNull( $url->getQueryVar( 'param' ) );

		// Test getting all query parameters
		$expected = array(
			'existing'  => 'test',
			'new_param' => 'value',
		);
		$this->assertEquals( $expected, $url->getQueryVars() );

		// Test array query parameters
		$url->addQueryVar( 'array_param', array( 'one', 'two' ) );
		$this->assertEquals( array( 'one', 'two' ), $url->getQueryVar( 'array_param' ) );
	}

	public function testStaticHelpers() {
		// Test stripping query string
		$urlString = 'https://example.com/path?param=value#fragment';
		$this->assertEquals( 'https://example.com/path#fragment', Url::stripQueryString( $urlString ) );

		// Test building URL from parts with all components
		$urlParts = array(
			'scheme'   => 'https',
			'user'     => 'username',
			'pass'     => 'password',
			'host'     => 'example.com',
			'port'     => '8080',
			'path'     => '/path',
			'query'    => 'param=value',
			'fragment' => 'section',
		);
		$expected = 'https://username:password@example.com:8080/path?param=value#section';
		$this->assertEquals( $expected, Url::buildUrl( $urlParts ) );

		// Test building URL with minimal parts
		$minimalParts = array( 'host' => 'example.com' );
		$this->assertEquals( 'example.com', Url::buildUrl( $minimalParts ) );
	}

	public function testPathManipulation() {
		$url = new Url( 'https://example.com/blog/2023/post-title/' );

		// Test getting all segments
		$expectedSegments = array( 'blog', '2023', 'post-title' );
		$this->assertEquals( $expectedSegments, $url->getSegments() );

		// Test getting specific segments
		$this->assertEquals( 'blog', $url->getSegment( 0 ) );
		$this->assertEquals( '2023', $url->getSegment( 1 ) );
		$this->assertEquals( 'post-title', $url->getSegment( 2 ) );
		$this->assertNull( $url->getSegment( 5 ) ); // Non-existent segment

		// Test trailing slash detection
		$this->assertTrue( $url->hasTrailingSlash() );

		// Test URL without trailing slash
		$url2 = new Url( 'https://example.com/blog/2023/post-title' );
		$this->assertFalse( $url2->hasTrailingSlash() );
	}

	public function testUrlOutput() {
		$urlString = 'https://example.com/path?param=value#fragment';
		$url       = new Url( $urlString );

		// Test toString() method
		$this->assertEquals( $urlString, $url->toString() );

		// Test string casting
		$this->assertEquals( $urlString, (string) $url );

		// Test toArray() method
		$urlParts = $url->toArray();
		$this->assertIsArray( $urlParts );
		$this->assertEquals( 'https', $urlParts['scheme'] );
		$this->assertEquals( 'example.com', $urlParts['host'] );
		$this->assertEquals( '/path', $urlParts['path'] );
		$this->assertEquals( 'param=value', $urlParts['query'] );
		$this->assertEquals( 'fragment', $urlParts['fragment'] );
	}

	public function testGetCurrentScheme() {
		// Test HTTPS via server variable
		$_SERVER['HTTPS'] = 'on';
		$this->assertEquals( 'https', Url::getCurrentScheme() );

		// Test HTTPS = 1
		$_SERVER['HTTPS'] = '1';
		$this->assertEquals( 'https', Url::getCurrentScheme() );

		// Test port 443
		unset( $_SERVER['HTTPS'] );
		$_SERVER['SERVER_PORT'] = '443';
		$this->assertEquals( 'https', Url::getCurrentScheme() );

		// Test forwarded proto
		unset( $_SERVER['SERVER_PORT'] );
		$_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';
		$this->assertEquals( 'https', Url::getCurrentScheme() );

		// Test default to http
		unset( $_SERVER['HTTP_X_FORWARDED_PROTO'] );
		$this->assertEquals( 'http', Url::getCurrentScheme() );
	}

	public function testBuildPath() {
		// Test with segments
		$segments = array( 'blog', '2023', 'post-title' );
		$this->assertEquals( '/blog/2023/post-title', Url::buildPath( $segments ) );

		// Test with trailing slash
		$this->assertEquals( '/blog/2023/post-title/', Url::buildPath( $segments, true ) );

		// Test empty segments
		$this->assertEquals( '', Url::buildPath( array() ) );
		$this->assertEquals( '/', Url::buildPath( array(), true ) );
	}
}
