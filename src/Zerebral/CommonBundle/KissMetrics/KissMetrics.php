<?php

namespace Zerebral\CommonBundle\KissMetrics;

/**
 * KissMetrics tracker
 *
 * Basic Usage
 *
 * $km = new KissMetrics('your_key');
 * $km->identify('user@mail.com');
 * $km->createEvent('broadcast', array('type' => 'text', 'property2' => 'value'));
 *
 * User profile
 *
 * You can assign additional attributes to user profile:
 * $km->identify('user@mail.com', array('gender' => 'female'));
 *
 *
 */
class KissMetrics
{
    /**
     * KissMetrics API key
     * @var null
     */
    private $apiKey = null;

    /**
     * User identity (e.g. email or user name)
     * @var string
     */
    private $identity = 'anonymous';

    /**
     * KissMetrics api server
     * @var string
     */
    private $apiServer = 'http://trk.kissmetrics.com/';

    public function __construct($apiKey = null)
    {
        $this->setApiKey($apiKey);
    }

    /**
     * Identify user
     *
     * @param string $identity user identity
     * @param array $properties user additional properties
     */
    public function identify($identity, $properties = array())
    {
        $this->identity = $identity;

        if (count($properties)) {
            $this->post('s', $properties);
        }
    }

    /**
     * Create event
     * @param string $event event name
     * @param array $properties event properties
     */
    public function createEvent($event, $properties = array())
    {
        $parameters = array(
            '_n' => $event,
        );
        $parameters = array_merge($properties, $parameters);
        $this->post('e', $parameters);
    }

    /**
     * @param string $api API section (e - for events, s - for user properties, a - for aliases)
     * @param array $parameters
     */
    private function post($api, $parameters = array())
    {
        $parameters['_k'] = $this->getApiKey();
        $parameters['_p'] = $this->getIdentity();
        $parameters['_t'] = $this->getTimestamp();

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $this->apiServer . $api . '?' . http_build_query($parameters),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_MAXREDIRS => 5,
        ));
        curl_exec($ch);
        curl_close($ch);
    }

    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function getApiKey()
    {
        return $this->apiKey;
    }

    public function setIdentity($identity)
    {
        $this->identity = $identity;
    }

    public function getIdentity()
    {
        return $this->identity;
    }

    public function getTimestamp()
    {
        return time();
    }
}