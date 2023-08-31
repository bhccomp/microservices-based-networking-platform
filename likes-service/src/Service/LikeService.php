<?php

namespace App\Service;

use App\Entity\Like;
use App\Repository\LikeRepository;
use Doctrine\ORM\EntityManagerInterface;

class LikeService
{
    private $entityManager;
    private $likeRepository;

    public function __construct(EntityManagerInterface $entityManager, LikeRepository $likeRepository)
    {
        $this->entityManager = $entityManager;
        $this->likeRepository = $likeRepository;
    }

    public function saveLike(int $postId, int $userId)
    {
        // Check if the user has already liked the post
        if ($this->isLiked($postId, $userId)) {
            throw new \Exception("User has already liked this post");
        }

        $this->entityManager->beginTransaction();

        try {
            $like = new Like();
            $like->setPostId($postId);
            $like->setUserId($userId);
            $like->setDateLiked(new \DateTime());

            $this->entityManager->persist($like);
            $this->entityManager->flush();

            $this->entityManager->commit();
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            throw $e;
        }
    }

    public function deleteLike(int $postId, int $userId): bool
    {   
        $like = $this->likeRepository->findOneBy([
            'user_id' => $userId,
            'post_id' => $postId
        ]);

        if (!$like) {
            return false;
        }

        $this->entityManager->beginTransaction();

        try {
            $this->entityManager->remove($like);
            $this->entityManager->flush();

            $this->entityManager->commit();

            return true;
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            throw $e;
        }
    }

    public function getAllPostLikes(int $postId): array
    {
        $likes = $this->likeRepository->findBy([
            'postId' => $postId
        ]);

        $likeArray = [];
        foreach ($likes as $like) {
            $likeArray[] = [
                'user_id' => $like->getUserId()
            ];
        }

        return $likeArray;
    }

    public function isLiked(int $postId, int $userId): bool
    {
        $like = $this->likeRepository->findOneBy([
            'userId' => $userId,
            'postId' => $postId
        ]);

        return $like !== null;
    }
}
