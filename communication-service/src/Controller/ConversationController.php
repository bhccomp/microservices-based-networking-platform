<?php

namespace App\Controller;

use App\Service\ConversationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/api/conversations')]
class ConversationController extends AbstractController
{
    private $conversationService;

    public function __construct(ConversationService $conversationService)
    {
        $this->conversationService = $conversationService;
    }

    #[Route('/', name: 'conversation_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $userId = $this->getUser()->getId();
        $conversations = $this->conversationService->getUserConversations($userId);

        return new JsonResponse($conversations);
    }

    #[Route('/{id}', name: 'conversation_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $userId = $this->getUser()->getId();
        $conversation = $this->conversationService->getUserConversation($id, $userId);

        if (!$conversation) {
            return new JsonResponse(['error' => 'Conversation not found or not authorized.'], JsonResponse::HTTP_NOT_FOUND);
        }

        return new JsonResponse($conversation);
    }

    #[Route('/', name: 'conversation_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user1Id = $this->getUser()->getId();
        $user2Id = $data['user2_id'];

        $conversation = $this->conversationService->createConversation($user1Id, $user2Id);

        return new JsonResponse($conversation, JsonResponse::HTTP_CREATED);
    }

}
