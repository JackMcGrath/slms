<?php
namespace Zerebral\BusinessBundle\ContentFetcher;

use Zerebral\BusinessBundle\Model\Feed\FeedContent;

class Fetcher
{

    protected $url;
    protected $urlContent;
    protected $urlType;
    protected $urlResponseCode;

    protected $title;
    protected $description;
    protected $thumbmnailUrl;

    protected static $metaTagsList = array('title', 'description', 'image_src', 'og:title', 'og:description', 'og:image');

    public function getDescription()
    {
        return $this->description;
    }

    public function getThumbmnailUrl()
    {
        return $this->thumbmnailUrl;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function __construct($url)
    {
        $this->url = $url;
        $this->loadUrl();
    }


    protected function nodeListToArray(\DOMNodeList $nodeList)
    {
        $metaTags = array();
        for ($i = 0; $i < $nodeList->length; $i++) {
            $node = $nodeList->item($i);
            $tagName = strtolower($node->getAttribute('name'));
            if (in_array($tagName, self::$metaTagsList)) {
                $metaTags[$tagName] = $node->getAttribute('content');
            }

            $tagProperty = strtolower($node->getAttribute('property'));
            if (in_array($tagProperty, self::$metaTagsList)) {
                $metaTags[$tagProperty] = $node->getAttribute('content');
            }
        }
        return $metaTags;
    }


    /** @todo: Need to parse uncorrect HTML to get meta */
    public function parse()
    {
        $dom = new \DOMDocument();
        @$dom->loadHTML($this->urlContent);
        $metaTags = $this->nodeListToArray($dom->getElementsByTagName('meta'));

        //$this->title = isset($metaTags['og:title']) ? $metaTags['og:title'] : (isset($metaTags['title']) ? $metaTags['title'] : $dom->getElementsByTagName('title')->item(0)->nodeValue);
        if (isset($metaTags['og:title'])) {
            $this->title = $metaTags['og:title'];
        } else {
            if (isset($metaTags['title'])) {
                $this->title = $metaTags['title'];
            } else {
                $titleTag = $dom->getElementsByTagName('title')->item(0); //
                if (is_null($titleTag)) {
                    $this->title = $this->url;
                } else {
                    $this->title = $titleTag->nodeValue;
                }
            }
        }
        $this->title = mb_strimwidth($this->title, 0, 97, '...');

        //$this->description = mb_strimwidth((isset($metaTags['og:description']) ? $metaTags['og:description'] : (isset($metaTags['description']) ? $metaTags['description'] : '')), 0, 253, '...');
        if (isset($metaTags['og:description'])) {
            $this->description = $metaTags['og:description'];
        } else {
            if (isset($metaTags['description'])) {
                $this->description = $metaTags['description'];
            } else {
                $this->description = '';
            }
        }
        $this->description = mb_strimwidth($this->description, 0, 253, '...');


        //$thumbnailUrl = isset($metaTags['og:image']) ? $metaTags['og:image'] : (isset($metaTags['image_src']) ? $metaTags['image_src'] : $dom->getElementsByTagName('img')->item(0)->getAttribute('src'));
        if (isset($metaTags['og:image'])) {
            $this->thumbmnailUrl = $metaTags['og:image'];
        } else {
            if (isset($metaTags['image_src'])) {
                $this->thumbmnailUrl = $metaTags['image_src'];
            } else {
                $imageTag = $dom->getElementsByTagName('img')->item(0);
                if (is_null($imageTag)) {
                    $this->thumbmnailUrl = null;
                } else {
                    $this->thumbmnailUrl = $imageTag->getAttribute('src');
                }
            }
        }

        if (!is_null($this->thumbmnailUrl)) {
            $thumbnailUrl = $this->thumbmnailUrl;
            if (strtolower(substr($thumbnailUrl, 0, 4)) !== 'http') {
                $urlInfo = parse_url($this->url);
                if (strtolower(substr($thumbnailUrl, 0, 2)) == '//') {
                    $this->thumbmnailUrl = $urlInfo['scheme'] . ':' . $thumbnailUrl;
                } else {
                    $thumbnailUrl = ltrim($thumbnailUrl, '/');
                    $this->thumbmnailUrl = $urlInfo['scheme'] . '://' . $urlInfo['host'] . '/' . $thumbnailUrl;
                }
            }
        }

    }

    /** @todo: Handle all possible response codes */
    protected function loadUrl()
    {
        $curlHandler = curl_init();
        $options = array(
            CURLOPT_URL => $this->url,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true, // return web page
            CURLOPT_HEADER => false, // do not return headers
            CURLOPT_FOLLOWLOCATION => true, // follow redirects
            CURLOPT_USERAGENT => "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:15.0) Gecko/20120427 Firefox/15.0a1", // who am i
            CURLOPT_AUTOREFERER => true, // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120, // timeout on connect
            CURLOPT_TIMEOUT => 120, // timeout on response
            CURLOPT_MAXREDIRS => 10 // stop after 10 redirects
        );
        curl_setopt_array($curlHandler, $options);
        $this->urlContent = curl_exec($curlHandler);
        $this->urlType = curl_getinfo($curlHandler, CURLINFO_CONTENT_TYPE);
        $this->urlResponseCode = curl_getinfo($curlHandler, CURLINFO_HTTP_CODE);
        curl_close($curlHandler);
    }

    public function isLoaded()
    {
        return $this->urlResponseCode == 200;
    }

    public function isMatchType($type)
    {
        switch ($type) {
            case 'website':
                return (strtolower(substr($this->urlType, 0, 9)) === 'text/html');
            case 'image':
                return (strtolower(substr($this->urlType, 0, 5)) === 'image');
            case 'video':
            {
                $urlInfo = parse_url($this->url);
                $host = strtolower(ltrim($urlInfo['host'], 'www.'));
                return in_array($host, array('vimeo.com', 'youtube.com'));
                break;
            }
            default:
                return true;
        }
    }
}