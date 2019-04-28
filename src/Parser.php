<?php

namespace DivineOmega\WikitextParser;

use DivineOmega\DOFileCachePSR6\CacheItemPool;
use DivineOmega\WikitextParser\Enums\Format;
use Psr\Cache\CacheItemPoolInterface;

class Parser
{
    private $wikitext;
    private $format = Format::PLAIN_TEXT;

    private $endpoint = 'https://en.wikipedia.org/w/api.php';
    private $queryString = '?action=parse&format=json&contentmodel=wikitext&text=';

    /** @var CacheItemPoolInterface */
    private $cache = null;

    public function __construct()
    {
        $cacheItemPool = new CacheItemPool();
        $cacheItemPool->changeConfig([
            'cacheDirectory' => __DIR__.'/../cache/',
        ]);
        $this->setCache($cacheItemPool);
    }

    /**
     * Set an alternative PSR-6 compliant cache item pool.
     *
     * @param CacheItemPoolInterface $cacheItemPool
     * @return $this
     */
    public function setCache(CacheItemPoolInterface $cacheItemPool)
    {
        $this->cache = $cacheItemPool;
        return $this;
    }

    /**
     * Set the Wikitext to parse.
     *
     * @param string $wikitext
     * @return Parser
     */
    public function setWikitext(string $wikitext) : Parser
    {
        $this->wikitext = $wikitext;
        return $this;
    }

    /**
     * Sets the format you wish the values to parsed into.
     *
     * @see Format
     *
     * @param mixed $format
     * @return Parser
     */
    public function setFormat(string $format = Format::PLAIN_TEXT) : Parser
    {
        $this->format = $format;
        return $this;
    }

    /**
     * Constructs the Wikitext parser URL.
     *
     * @return string
     */
    private function buildUrl()
    {
        return $this->endpoint.$this->queryString.urlencode($this->wikitext);
    }

    /**
     * Parses wikitext into desired format, caches result, and returns it.
     *
     * @return mixed|string
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function parse()
    {
        $cacheKey = sha1(serialize(['wikitext', $this->wikitext, $this->format]));

        $item = $this->cache->getItem($cacheKey);

        if ($item->isHit()) {
            return $item->get();
        }

        $url = $this->buildUrl();

        $data = json_decode(file_get_contents($url), true);

        $dom = new \DOMDocument();
        $dom->loadXML($data['parse']['text']['*']);

        $element = $dom->childNodes[0]->childNodes[0];

        $returnValue = $element->ownerDocument->saveXML($element);

        if ($this->format === Format::PLAIN_TEXT) {
            $returnValue = strip_tags($returnValue);
        }

        $returnValue = trim($returnValue);

        $item->set($returnValue);
        $this->cache->save($item);

        return $returnValue;
    }
}