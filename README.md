# URL Handler

A PHP library for parsing, manipulating, and building URLs.

## Description

This library provides a simple and intuitive way to work with URLs in PHP. It allows you to parse, manipulate, and build URLs while handling all the common URL components including scheme, host, port, path, query parameters, and fragments.

## Features

- Parse URLs into their component parts
- Build URLs from component parts
- Add, remove, and modify query parameters
- Handle URL fragments
- Detect and manipulate trailing slashes
- Get current URL and scheme detection
- Path segment manipulation
- URL string conversion


## Requirements

- PHP 5.6 or higher

## Installation

Install via Composer:

```bash
composer require wpscholar/url
```

## Basic Usage

```php
use wpscholar\Url;

// Create from a URL string
$url = new Url('https://example.com/path?param=value#section');

// Get the current URL
$currentUrl = new Url(); // Automatically uses current URL

// Access URL components
echo $url->scheme; // 'https'
echo $url->host; // 'example.com'
echo $url->path; // '/path'
echo $url->query; // 'param=value'
echo $url->fragment; // 'section'

// Modify query parameters
$url->addQueryVar('new_param', 'value');
$url->removeQueryVar('old_param');

// Get specific query parameter
$value = $url->getQueryVar('param_name');

// Get all query parameters as array
$params = $url->getQueryVars();
```

## Static Helpers

```php
// Get current URL
$currentUrl = Url::getCurrentUrl();

// Get current scheme (http/https)
$scheme = Url::getCurrentScheme();

// Strip query string from URL
$cleanUrl = Url::stripQueryString($url);

// Build URL from parts
$url = Url::buildUrl([
    'scheme' => 'https',
    'host' => 'example.com',
    'path' => '/path',
    'query' => 'param=value'
]);
```

## Path Manipulation

```php
// Given URL: https://example.com/blog/2023/post-title

// Get all path segments as array
$segments = $url->getSegments();
// Returns: ['blog', '2023', 'post-title']

// Get specific segment by index (zero-based)
$year = $url->getSegment(1);     // Returns: '2023'
$section = $url->getSegment(0);  // Returns: 'blog'
$slug = $url->getSegment(2);     // Returns: 'post-title'
```