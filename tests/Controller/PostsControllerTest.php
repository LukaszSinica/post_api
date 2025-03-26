<?php

namespace App\Tests\Controller;

use App\Entity\Posts;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class PostsControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $postRepository;
    private string $path = '/posts/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->postRepository = $this->manager->getRepository(Posts::class);

        foreach ($this->postRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Post index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    // public function testNew(): void
    // {
    //     $this->markTestIncomplete();
    //     $this->client->request('GET', sprintf('%snew', $this->path));

    //     self::assertResponseStatusCodeSame(200);

    //     $this->client->submitForm('Save', [
    //         'post[title]' => 'Testing',
    //         'post[content]' => 'Testing',
    //         'post[created_at]' => 'Testing',
    //     ]);

    //     self::assertResponseRedirects($this->path);

    //     self::assertSame(1, $this->postRepository->count([]));
    // }

    // public function testShow(): void
    // {
    //     $this->markTestIncomplete();
    //     $fixture = new Posts();
    //     $fixture->setTitle('My Title');
    //     $fixture->setContent('My Title');
    //     $fixture->setCreated_at('My Title');

    //     $this->manager->persist($fixture);
    //     $this->manager->flush();

    //     $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

    //     self::assertResponseStatusCodeSame(200);
    //     self::assertPageTitleContains('Post');

    //     // Use assertions to check that the properties are properly displayed.
    // }

    // public function testEdit(): void
    // {
    //     $this->markTestIncomplete();
    //     $fixture = new Posts();
    //     $fixture->setTitle('Value');
    //     $fixture->setContent('Value');
    //     $fixture->setCreated_at('Value');

    //     $this->manager->persist($fixture);
    //     $this->manager->flush();

    //     $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

    //     $this->client->submitForm('Update', [
    //         'post[title]' => 'Something New',
    //         'post[content]' => 'Something New',
    //         'post[created_at]' => 'Something New',
    //     ]);

    //     self::assertResponseRedirects('/posts/');

    //     $fixture = $this->postRepository->findAll();

    //     self::assertSame('Something New', $fixture[0]->getTitle());
    //     self::assertSame('Something New', $fixture[0]->getContent());
    //     self::assertSame('Something New', $fixture[0]->getCreated_at());
    // }

    // public function testRemove(): void
    // {
    //     $this->markTestIncomplete();
    //     $fixture = new Posts();
    //     $fixture->setTitle('Value');
    //     $fixture->setContent('Value');
    //     $fixture->setCreated_at('Value');

    //     $this->manager->persist($fixture);
    //     $this->manager->flush();

    //     $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
    //     $this->client->submitForm('Delete');

    //     self::assertResponseRedirects('/posts/');
    //     self::assertSame(0, $this->postRepository->count([]));
    // }
}
