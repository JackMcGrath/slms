<?php
namespace Zerebral\CommonBundle\Component\FeedContentFetcher;

use Zerebral\BusinessBundle\Model\Feed\FeedContent;

class FeedContentFetcher
{

    /** @var FeedContent */
    protected $feedContent;
    protected $feedContentResponse;
    protected $feedContentResponseMimeType;

    protected $linkTitle;
    protected $linkDescription;
    protected $linkThumbmnailUrl;

    public static $urlRegexp = '/(https?\:\/\/|\s)[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})(\/+[a-z0-9_.\:\;-]*)*(\?[\&\%\|\+a-z0-9_=,\.\:\;-]*)?([\&\%\|\+&a-z0-9_=,\:\;\.-]*)([\!\#\/\&\%\|\+a-z0-9_=,\:\;\.-]*)}*/i';
    public static $metaTagsList = array('title', 'description', 'image_src', 'og:title', 'og:description', 'og:image');

    public function getLinkDescription()
    {
        return $this->linkDescription;
    }

    public function getLinkThumbmnailUrl()
    {
        return $this->linkThumbmnailUrl;
    }

    public function getLinkTitle()
    {
        return $this->linkTitle;
    }

    public function __construct(FeedContent $feedContent)
    {
        $this->feedContent = $feedContent;

        if (preg_match(self::$urlRegexp, $this->feedContent->getLinkUrl())) {
            $this->loadUrl($this->feedContent->getLinkUrl());

            if (strstr($this->feedContentResponseMimeType, 'text/html')) {
                $responseMeta = $this->parseWebSite();

                $this->linkTitle = $responseMeta['title'];
                $this->linkDescription = $responseMeta['description'];
                $this->linkThumbmnailUrl = $responseMeta['thumbnail_url'];

                return true;
            }
        }

        return false;

    }


    protected function nodeListToArray(\DOMNodeList $nodeList)
    {
        $metaTags = array();
        for ($i = 0; $i < $nodeList->length; $i++) {
            $node = $nodeList->item($i);
            $tagName = $node->getAttribute('name');
            if (in_array($tagName, self::$metaTagsList)) {
                $metaTags[$tagName] = $node->getAttribute('content');
            }

        }
        return $metaTags;
    }


    protected function parseWebSite()
    {
        $dom = new \DOMDocument();
        @$dom->loadHTML($this->feedContentResponse);
        $metaTags = $this->nodeListToArray($dom->getElementsByTagName('meta'));


        $urlComponents['title'] = isset($metaTags['og:title']) ? $metaTags['og:title'] : (isset($metaTags['title']) ? $metaTags['title'] : $dom->getElementsByTagName('title')->item(0)->nodeValue);
        $urlComponents['description'] = mb_strimwidth((isset($metaTags['og:description']) ? $metaTags['og:description'] : (isset($metaTags['description']) ? $metaTags['description'] : '')), 0, 253, '...');
        $urlComponents['thumbnail_url'] = isset($metaTags['og:image']) ? $metaTags['og:image'] : (isset($metaTags['image_src']) ? $metaTags['image_src'] : $dom->getElementsByTagName('img')->item(0)->getAttribute('src'));

        return $urlComponents;
    }

    /** @todo: Handle all possible response codes */
    protected function loadUrl($url)
    {
        $curlHandler = curl_init();
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER => true,            // do not return headers
            CURLOPT_FOLLOWLOCATION => true,     // follow redirects
            CURLOPT_USERAGENT => "spider",      // who am i
            CURLOPT_AUTOREFERER => true,        // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
            CURLOPT_TIMEOUT => 120,             // timeout on response
            CURLOPT_MAXREDIRS => 10             // stop after 10 redirects
        );
        curl_setopt_array($curlHandler, $options);
        $this->feedContentResponse = curl_exec($curlHandler);
        $info = curl_getinfo($curlHandler, CURLINFO_CONTENT_TYPE);
        curl_close($curlHandler);
        $this->tempFileMimeType = $info;
    }
}