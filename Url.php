<?php

namespace wpscholar;

/**
 * Class URL
 */
class Url {

	/**
	 * The URL
	 *
	 * @var string
	 */
	public $url;

	/**
	 * The scheme
	 *
	 * @var string
	 */
	public $scheme;

	/**
	 * The host
	 *
	 * @var string
	 */
	public $host;

	/**
	 * The path
	 *
	 * @var string
	 */
	public $path;

	/**
	 * The query string
	 *
	 * @var string
	 */
	public $query;

	/**
	 * The fragment
	 *
	 * @var string
	 */
	public $fragment;

	/**
	 * Set the URL and parse it.
	 *
	 * @param string $url
	 */
	public function __construct( $url = null ) {
		if ( is_null( $url ) ) {
			$url = self::getCurrentUrl();
		}
		$this->parseUrl( $url );
	}

	/**
	 * Gets the current URL
	 *
	 * @return string
	 */
	public static function getCurrentUrl() {

		$is_ssl = (boolean) getenv( 'HTTPS' ) || '443' === getenv( 'SERVER_PORT' ) || 'https' === getenv( 'HTTP_X_FORWARDED_PROTO' );
		$scheme = $is_ssl ? 'https' : 'http';

		return $scheme . '://' . getenv( 'HTTP_HOST' ) . getenv( 'REQUEST_URI' );
	}

	/**
	 * Build a URL from its component parts
	 *
	 * @param array $parts
	 *
	 * @return string
	 */
	public static function buildUrl( array $parts ) {
		$url = '';
		if ( ! empty( $parts['scheme'] ) ) {
			$url .= $parts['scheme'] . '://';
		}
		if ( ! empty( $parts['host'] ) ) {
			$url .= $parts['host'];
		}
		if ( ! empty( $parts['path'] ) ) {
			$url .= $parts['path'];
		}
		if ( ! empty( $parts['query'] ) ) {
			$url .= '?' . $parts['query'];
		}
		if ( ! empty( $parts['fragment'] ) ) {
			$url .= '#' . $parts['fragment'];
		}

		return $url;
	}

	/**
	 * Parse a URL into its component parts
	 *
	 * @param string $url
	 *
	 * @return $this
	 */
	public function parseUrl( $url ) {
		$this->url = $url;
		$this->scheme = parse_url( $url, PHP_URL_SCHEME );
		$this->host = parse_url( $url, PHP_URL_HOST );
		$this->path = parse_url( $url, PHP_URL_PATH );
		$this->query = parse_url( $url, PHP_URL_QUERY );
		$this->fragment = parse_url( $url, PHP_URL_FRAGMENT );

		return $this;
	}

	/**
	 * Get URL path segments
	 *
	 * @return array
	 */
	public function getSegments() {
		return array_filter( explode( '/', trim( $this->path, '/' ) ) );
	}

	/**
	 * Get a specific path segment from a URL
	 *
	 * @param int $key
	 *
	 * @return string|null
	 */
	public function getSegment( $key = 0 ) {
		$segments = $this->getSegments();

		return array_key_exists( $key, $segments ) ? $segments[ $key ] : null;
	}

	/**
	 * Build the path from its component parts
	 *
	 * @param array $segments
	 * @param bool $trailing_slash
	 *
	 * @return string
	 */
	public static function buildPath( array $segments, $trailing_slash = false ) {
		$path = '';
		if ( ! empty( $segments ) ) {
			$path .= '/' . implode( '/', $segments );
		}
		if ( $trailing_slash ) {
			$path .= '/';
		}

		return $path;
	}

	/**
	 * Check if the URL path has a trailing slash
	 *
	 * @return bool
	 */
	public function hasTrailingSlash() {
		return is_string( $this->path ) && '/' === substr( $this->path, - 1, 1 );
	}

	/**
	 * Get query variables
	 *
	 * @return array
	 */
	public function getQueryVars() {
		$query_vars = array();
		parse_str( $this->query, $query_vars );

		return $query_vars;
	}

	/**
	 * Add a query var
	 *
	 * @param string $key
	 * @param string $value
	 *
	 * @return string
	 */
	public function addQueryVar( $key, $value ) {
		$query_vars = $this->getQueryVars();
		$query_vars[ $key ] = $value;
		$this->query = http_build_query( $query_vars );
		$this->url = self::buildUrl( $this->toArray() );

		return $this->url;
	}

	/**
	 * Remove a query var
	 *
	 * @param string $key
	 *
	 * @return string
	 */
	public function removeQueryVar( $key ) {
		$query_vars = $this->getQueryVars();
		unset( $query_vars[ $key ] );
		$this->query = http_build_query( $query_vars );
		$this->url = self::buildUrl( $this->toArray() );

		return $this->url;
	}

	/**
	 * Get a query var
	 *
	 * @param string $key
	 *
	 * @return string|null
	 */
	public function getQueryVar( $key ) {
		$query_vars = $this->getQueryVars();

		return array_key_exists( $key, $query_vars ) ? $query_vars[ $key ] : null;
	}

	/**
	 * Strip the query string from a URL
	 *
	 * @param string $url
	 *
	 * @return mixed
	 */
	public static function stripQueryString( $url ) {
		$parts = explode( '?', $url, 2 );

		return $parts[0];
	}

	/**
	 * Add a fragment
	 *
	 * @param string $value
	 *
	 * @return string
	 */
	public function addFragment( $value ) {
		$this->fragment = $value;
		$this->url = self::buildUrl( $this->toArray() );

		return $this->url;
	}

	/**
	 * Returns the URL in array form
	 *
	 * @return array
	 */
	public function toArray() {
		return array(
			'scheme'   => $this->scheme,
			'host'     => $this->host,
			'path'     => $this->path,
			'query'    => $this->query,
			'fragment' => $this->fragment,
		);
	}

	/**
	 * Returns the URL in string form
	 *
	 * @return string
	 */
	public function toString() {
		return $this->url;
	}

	/**
	 * Magic method to output object as string
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->toString();
	}

}
