<?php

namespace App\Service;

use App\Entity\Post;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;

class PostService
{
    
    private $entityManager;
    private $postRepository;

    public function __construct(EntityManagerInterface $entityManager, PostRepository $postRepository)
    {
        $this->entityManager = $entityManager;
        $this->postRepository = $postRepository;
    }

    public function createPost($data, $authorId, $author)
    {
        $post = new Post();
        $post->setTitle($data['title']);
        $post->setContent($data['content']);
        $post->setAuthor($author);
        $post->setAuthorId($authorId);
        $post->setPublicationDate(new \DateTime());

        $this->entityManager->persist($post);
        $this->entityManager->flush();

        return $post;
    }

    public function updatePost(int $id, array $data): ?Post
    {
        $post = $this->entityManager->getRepository(Post::class)->find($id);
        if (!$post) {
            return null;
        }

        if (isset($data['title'])) {
            $post->setTitle($data['title']);
        }

        if (isset($data['content'])) {
            $post->setContent($data['content']);
        }

        $this->entityManager->persist($post);
        $this->entityManager->flush();

        return $post;
    }

    public function deletePost(int $id): bool
    {
        $post = $this->em->getRepository(Post::class)->find($id);
        if (!$post) {
            return false;
        }

        $this->em->remove($post);
        $this->em->flush();

        return true;
    }

    public function getAllPosts(): array
    {
        $posts = $this->postRepository->findAll();

        $postArray = [];
        foreach ($posts as $post) {
            $postArray[] = [
                'id' => $post->getId(),
                'title' => $post->getTitle(),
                'content' => $post->getContent(),
                'publication_date' => $post->getPublicationDate()
            ];
        }

        return $postArray;
    }

    public function getPostById(int $id): ?array
    {
        $post = $this->postRepository->find($id);

        if (!$post) {
            return null;
        }

        return [
            'id' => $post->getId(),
            'title' => $post->getTitle(),
            'content' => $post->getContent(),
            'publication_date' => $post->getPublicationDate()
        ];
    }

}
