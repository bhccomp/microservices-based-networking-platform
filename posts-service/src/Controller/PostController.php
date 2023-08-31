<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\PostService;

class PostController extends AbstractController
{
    #[Route(path: '/api/posts', name: 'list_post', methods: ['GET'])]
    public function list(PostService $postService): Response
    {
        $posts = $postService->getAllPosts();
        
        return new JsonResponse($posts, Response::HTTP_OK);
    }

    #[Route(path: '/api/posts/{id}', name: 'show_post', methods: ['GET'])]
    public function show(int $id, PostService $postService): Response
    {
        $post = $postService->getPostById($id);
        
        if (!$post) {
            return new JsonResponse(['error' => 'Post not found'], Response::HTTP_NOT_FOUND);
        }
        
        return new JsonResponse($post, Response::HTTP_OK);
    }

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
