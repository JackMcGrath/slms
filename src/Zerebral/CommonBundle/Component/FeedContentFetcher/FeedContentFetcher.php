<?php
namespace Zerebral\CommonBundle\Component\FeedContentFetcher;

use Zerebral\BusinessBundle\Model\Feed\FeedContent;

class FeedContentFetcher
{

    /** @var FeedContent */
    protected $feedContent;

    public static $urlRegexp = '/(https?\:\/\/|\s)[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})(\/+[a-z0-9_.\:\;-]*)*(\?[\&\%\|\+a-z0-9_=,\.\:\;-]*)?([\&\%\|\+&a-z0-9_=,\:\;\.-]*)([\!\#\/\&\%\|\+a-z0-9_=,\:\;\.-]*)}*/i';
    public static $metaTagsList = array('title', 'description', 'image_src', 'og:title', 'og:description', 'og:image');

    protected $tempFileName;
    protected $tempFileMimeType;



    public function __construct(FeedContent $feedContent)
    {
        $this->feedContent = $feedContent;
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


    protected function parseWebSite($html)
    {
        $dom = new \DOMDocument();
        @$dom->loadHTML(file_get_contents($html));
        $metaTags = $this->nodeListToArray($dom->getElementsByTagName('meta'));


        $urlComponents['title'] = isset($metaTags['og:title']) ? $metaTags['og:title'] : (isset($metaTags['title']) ? $metaTags['title'] : $dom->getElementsByTagName('title')->item(0)->nodeValue);
        $urlComponents['description'] = mb_strimwidth((isset($metaTags['og:description']) ? $metaTags['og:description'] : (isset($metaTags['description']) ? $metaTags['description'] : '')), 0, 253, '...');
        $urlComponents['thumbnail_url'] = isset($metaTags['og:image']) ? $metaTags['og:image'] : (isset($metaTags['image_src']) ? $metaTags['image_src'] : $dom->getElementsByTagName('img')->item(0)->getAttribute('src'));

        return $urlComponents;
    }


    protected function saveToTempFile($content)
    {
        $tempFieName = tempnam(sys_get_temp_dir(), md5(rand(0, 1000)));
        $tempFileDescriptor = fopen($tempFieName, 'w+');
        fwrite($tempFileDescriptor, $content);
        fclose($tempFileDescriptor);
        $this->tempFileName = $tempFieName;
        $this->tempFileMimeType = mime_content_type($tempFieName);
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
        $response = curl_exec($curlHandler);
        $info = curl_getinfo($curlHandler, CURLINFO_CONTENT_TYPE);
        curl_close($curlHandler);

        $this->saveToTempFile($response);
        $this->tempFileMimeType = $info;
    }

    public function fetch()
    {
        if (preg_match(self::$urlRegexp, $this->feedContent->getLinkUrl())) {
            $this->loadUrl($this->feedContent->getLinkUrl());

            if (strstr($this->tempFileMimeType, 'text/html')) {
                $urlComponents = $this->parseWebSite($this->tempFileName);

                $this->feedContent->setLinkTitle($urlComponents['title']);
                $this->feedContent->setLinkDescription($urlComponents['description']);
                $this->feedContent->setLinkThumbnailUrl($urlComponents['thumbnail_url']);

                return true;
            }
        }

        return false;
    }
}