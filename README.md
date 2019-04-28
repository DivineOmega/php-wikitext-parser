# PHP Wikitext Parser
The library provides an easy way to parse Wikitext in PHP.

## Installation

Just run the following Composer command at the root of 
your project.

```bash
composer require divineomega/php-wikitext-parser
```

## Usage

The most basic usage is to convert a Wikitext formatted
string to plain text.

```php
$plainText = (new WikitextParser())
    ->setWikitext($wikitext)
    ->parse();
```

### Alternative Format

You are also able to specify alternative formats to 
convert to, using the `setFormat` method. By default, 
this is set to plain text.

For example, you can convert Wikitext to HTML, as shown
below.

```php
$plainText = (new WikitextParser())
    ->setWikitext($wikitext)
    ->setFormat(Format::HTML)
    ->parse();
```

### Caching

By default, file caching is used. If you wish, you can
specify any PSR-6 compliant caching library. This is
done using the `setCache` method as should below.

```php
$cache = new OtherPsr6CacheItemPool();

$plainText = (new WikitextParser())
    ->setCache($cache)
    ->setWikitext($wikitext)
    ->parse();
```
