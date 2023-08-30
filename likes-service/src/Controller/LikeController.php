<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\LikeService;

class LikeController extends AbstractController
{
    #[Route(path: '/api/likes/{postId}', name: 'save_like', methods: ['POST'])]
    public function Save(int $postId, Request $request, LikeService $likeService): JsonResponse
    {
        $user = $this->getUser();
        $userId = $user->getId();

        $likeService->saveLike($postId, $userId);

        return new JsonResponse(['success' => 'Post liked successfully'], Response::HTTP_CREATED);
    }

    #[Route(path: '/api/likes/{postId}', name: 'delete_like', methods: ['DELETE'])]
    public function deleteLike(int $postId, LikeService $likeService): JsonResponse
    {   
        $user = $this->getUser();
        $userId = $user->getId();
        
        $likeService->deleteLike($postId, $userId);
        return new JsonResponse(['message' => 'Like removed'], Response::HTTP_OK);
    }

    #[Route(path: '/api/likes/{postId}', name: 'get_post_likes', methods: ['GET'])]
    public function getLikes(int $postId, LikeService $likeService): JsonResponse
    {
        $likes = $likeService->getAllPostLikes($postId);
        return $this->json($likes);
    }

    #[Route(path: '/api/is-liked/{postId}/{userId}', name: 'is_likes', methods: ['GET'])]
    public function isLiked(int $postId, int $userId, LikeService $likeService): JsonResponse
    {
        $isLiked = $likeService->isLiked($postId, $userId);
        return $this->json(['isLiked' => $isLiked]);
    }
}

