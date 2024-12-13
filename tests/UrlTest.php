<?php

namespace wpscholar\Tests;

use PHPUnit\Framework\TestCase;
use wpscholar\Url;

class UrlTest extends TestCase {

	public function testUrlParsing() {
		$url = new Url( 'https://example.com/path?param=value#section' );

		$this->assertEquals( 'https', $url->scheme );
		$this->assertEquals( 'example.com', $url->host );
		$this->assertEquals( '/path', $url->path );
		$this->assertEquals( 'param=value', $url->query );
		$this->assertEquals( 'section', $url->fragment );
	}

	public function testQueryParameterManipulation() {
		$url = new Url( 'https://example.com/path?param=value' );

		// Test adding query parameter
		$url->addQueryVar( 'new_param', 'value' );
		$this->assertEquals( 'value', $url->getQueryVar( 'new_param' ) );

		// Test removing query parameter
		$url->removeQueryVar( 'param' );
		$this->assertNull( $url->getQueryVar( 'param' ) );

		// Test getting all query parameters
		$expected = array( 'new_param' => 'value' );
		$this->assertEquals( $expected, $url->getQueryVars() );
	}

	public function testStaticHelpers() {
		// Test stripping query string
		$urlString = 'https://example.com/path?param=value';
		$this->assertEquals( 'https://example.com/path', Url::stripQueryString( $urlString ) );

		// Test building URL from parts
		$urlParts = array(
			'scheme' => 'https',
			'host'   => 'example.com',
			'path'   => '/path',
			'query'  => 'param=value',
		);
		$expected = 'https://example.com/path?param=value';
		$this->assertEquals( $expected, Url::buildUrl( $urlParts ) );
	}

	public function testPathManipulation() {
		$url = new Url( 'https://example.com/blog/2023/post-title' );

		// Test getting all segments
		$expectedSegments = array( 'blog', '2023', 'post-title' );
		$this->assertEquals( $expectedSegments, $url->getSegments() );

		// Test getting specific segments
		$this->assertEquals( 'blog', $url->getSegment( 0 ) );
		$this->assertEquals( '2023', $url->getSegment( 1 ) );
		$this->assertEquals( 'post-title', $url->getSegment( 2 ) );
	}

	public function testUrlOutput() {
		$urlString = 'https://example.com/path?param=value';
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
	}
}
