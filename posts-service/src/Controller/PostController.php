<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\PostService;
use OpenApi\Annotations as OA;

class PostController extends AbstractController
{

    /**
     * @OA\Get(
     *     path="/api/posts",
     *     tags={"Posts"},
     *     summary="List all posts",
     *     @OA\Response(
     *         response=200,
     *         description="Returns the list of all posts"
     *     )
     * )
     */
    #[Route(path: '/api/posts', name: 'list_post', methods: ['GET'])]
    public function list(PostService $postService): Response
    {
        $posts = $postService->getAllPosts();
        
        return new JsonResponse($posts, Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/posts/{id}",
     *     tags={"Posts"},
     *     summary="Show details of a specific post",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the post to retrieve",
     *         required=true
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Returns the details of the post"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found"
     *     )
     * )
     */
    #[Route(path: '/api/posts/{id}', name: 'show_post', methods: ['GET'])]
    public function show(int $id, PostService $postService): Response
    {
        $post = $postService->getPostById($id);
        
        if (!$post) {
            return new JsonResponse(['error' => 'Post not found'], Response::HTTP_NOT_FOUND);
        }
        
        return new JsonResponse($post, Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/posts",
     *     tags={"Posts"},
     *     summary="Create a new post",
     *     @OA\Response(
     *         response=201,
     *         description="Post created successfully"
     *     )
     * )
     */
    #[Route(path: '/api/posts', name: 'create_post', methods: ['POST'])]
    public function create(Request $request, PostService $postService): Response
    {
        $user = $this->getUser();
        $authorId = $user->getId();
        $author = $user->getFirstName();
        $postData = json_decode($request->getContent(), true);
        
        $postService->createPost($postData, $authorId, $author);

        return new JsonResponse(
            ['success' => 'Post created successfully'], 
            Response::HTTP_CREATED
        );
    }

    /**
     * @OA\Put(
     *     path="/api/posts/{id}",
     *     tags={"Posts"},
     *     summary="Edit a post",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the post to edit",
     *         required=true
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post updated successfully"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Access denied"
     *     )
     * )
     */
    #[Route(path: '/api/posts/{id}', name: 'edit_post', methods: ['PUT'])]
    public function edit(int $id, Request $request, PostService $postService): Response
    {
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);
        
        try {
            $postService->updatePost($id, $data, $user); 
            return new JsonResponse(['message' => 'Post updated successfully'], Response::HTTP_OK);
        } catch (AccessDeniedException $e) {
            return new JsonResponse(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/posts/{id}",
     *     tags={"Posts"},
     *     summary="Delete a post",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the post to delete",
     *         required=true
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Access denied"
     *     )
     * )
     */
    #[Route(path: '/api/posts/{id}', name: 'delete_post', methods: ['DELETE'])]
    public function delete(int $id, PostService $postService): Response
    {
        $user = $this->getUser();

        try {
            $postService->deletePost($id, $user);
            return new JsonResponse(['message' => 'Post deleted successfully'], Response::HTTP_OK);
        } catch (AccessDeniedException $e) {
            return new JsonResponse(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }
    }
}
