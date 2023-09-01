<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\LikeService;
use OpenApi\Annotations as OA;


class LikeController extends AbstractController
{

    /**
     * @OA\Post(
     *     path="/api/likes/{postId}",
     *     tags={"Likes"},
     *     summary="Like a post",
     *     @OA\Parameter(
     *         name="postId",
     *         in="path",
     *         description="ID of the post to like",
     *         required=true
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Post liked successfully"
     *     )
     * )
     */
    #[Route(path: '/api/likes/{postId}', name: 'save_like', methods: ['POST'])]
    public function Save(int $postId, Request $request, LikeService $likeService): JsonResponse
    {
        $user = $this->getUser();
        $userId = $user->getId();

        $likeService->saveLike($postId, $userId);

        return new JsonResponse(['success' => 'Post liked successfully'], Response::HTTP_CREATED);
    }

    /**
     * @OA\Delete(
     *     path="/api/likes/{postId}",
     *     tags={"Likes"},
     *     summary="Remove a like from a post",
     *     @OA\Parameter(
     *         name="postId",
     *         in="path",
     *         description="ID of the post to unlike",
     *         required=true
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Like removed"
     *     )
     * )
     */    
    #[Route(path: '/api/likes/{postId}', name: 'delete_like', methods: ['DELETE'])]
    public function deleteLike(int $postId, LikeService $likeService): JsonResponse
    {   
        $user = $this->getUser();
        $userId = $user->getId();
        
        $likeService->deleteLike($postId, $userId);
        return new JsonResponse(['message' => 'Like removed'], Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/likes/{postId}",
     *     tags={"Likes"},
     *     summary="Get all likes for a post",
     *     @OA\Parameter(
     *         name="postId",
     *         in="path",
     *         description="ID of the post to get likes",
     *         required=true
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Returns the list of all likes for the post"
     *     )
     * )
     */
    #[Route(path: '/api/likes/{postId}', name: 'get_post_likes', methods: ['GET'])]
    public function getLikes(int $postId, LikeService $likeService): JsonResponse
    {
        $likes = $likeService->getAllPostLikes($postId);
        return $this->json($likes);
    }

    /**
     * @OA\Get(
     *     path="/api/is-liked/{postId}/{userId}",
     *     tags={"Likes"},
     *     summary="Check if a post is liked by a user",
     *     @OA\Parameter(
     *         name="postId",
     *         in="path",
     *         description="ID of the post to check",
     *         required=true
     *     ),
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="ID of the user to check",
     *         required=true
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Returns a boolean indicating if the post is liked by the user"
     *     )
     * )
     */
    #[Route(path: '/api/is-liked/{postId}/{userId}', name: 'is_likes', methods: ['GET'])]
    public function isLiked(int $postId, int $userId, LikeService $likeService): JsonResponse
    {
        $isLiked = $likeService->isLiked($postId, $userId);
        return $this->json(['isLiked' => $isLiked]);
    }
}

