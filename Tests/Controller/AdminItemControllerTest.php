<?php

namespace RB\BoltBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminItemControllerTest extends WebTestCase
{
    public function testNew()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/new');
    }

    public function testEdit()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/edit');
    }

    public function testDel()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/del');
    }

    public function testLoad()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/load');
    }

}
