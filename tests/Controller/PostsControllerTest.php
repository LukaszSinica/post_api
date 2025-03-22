<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class PostsControllerTest extends WebTestCase
{
    public function testShowPosts(): void
    {
        $client = static::createClient();
        $client->request('GET', '/posts');

        self::assertResponseIsSuccessful();
    }
}
