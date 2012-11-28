<?php

namespace Zerebral\FrontendBundle\Tests\Controller;


class DefaultControllerTest extends \Zerebral\FrontendBundle\Tests\TestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertTrue($crawler->filter('h1:contains("Hello dev!")')->count() > 0);
    }
}
