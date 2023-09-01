<?php

namespace App\Controller;

use App\Service\ConversationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Conversations")
 */
#[Route('/api/conversations')]
class ConversationController extends AbstractController
{
    private $conversationService;

    public function __construct(ConversationService $conversationService)
    {
        $this->conversationService = $conversationService;
    }

    /**
     * @OA\Get(
     *     path="/api/conversations",
     *     tags={"Conversations"},
     *     summary="List user's conversations",
     *     @OA\Response(
     *         response=200,
     *         description="Returns a list of conversations for the user"
     *     )
     * )
     */
    #[Route('/', name: 'conversation_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $userId = $this->getUser()->getId();
        $conversations = $this->conversationService->getUserConversations($userId);

        return new JsonResponse($conversations);
    }

    /**
     * @OA\Get(
     *     path="/api/conversations/{id}",
     *     tags={"Conversations"},
     *     summary="Show details of a conversation",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the conversation",
     *         required=true
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Returns details of the conversation"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Conversation not found or not authorized."
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/conversations",
     *     tags={"Conversations"},
     *     summary="Create a new conversation",
     *     @OA\RequestBody(
     *         description="Data to create a new conversation",
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="user2_id", type="integer", description="ID of the second user in the conversation")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Conversation created successfully"
     *     )
     * )
     */
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
