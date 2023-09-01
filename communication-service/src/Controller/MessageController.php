<?php 

namespace App\Controller;

use App\Service\MessageService;
use App\Message\SaveMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Messages")
 */
#[Route('/api/messages')]
class MessageController extends AbstractController
{
    private $messageService;
    private $messageBus;

    public function __construct(MessageService $messageService, MessageBusInterface $messageBus )
    {
        $this->messageService = $messageService;
        $this->messageBus = $messageBus;
    }

    /**
     * @OA\Get(
     *     path="/api/messages",
     *     tags={"Messages"},
     *     summary="List user's messages",
     *     @OA\Response(
     *         response=200,
     *         description="Returns a list of messages for the user"
     *     )
     * )
     */
    #[Route('/', name: 'message_index', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $userId = $this->getUser()->getId();
        $messages = $this->messageService->getUserMessages($userId);

        return new JsonResponse($messages);
    }

    /**
     * @OA\Get(
     *     path="/api/messages/{id}",
     *     tags={"Messages"},
     *     summary="Show details of a message",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the message",
     *         required=true
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Returns details of the message"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Message not found or not authorized."
     *     )
     * )
     */
    #[Route('/{id}', name: 'message_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $userId = $this->getUser()->getId();
        $message = $this->messageService->getUserMessage($id, $userId);

        if (!$message) {
            return new JsonResponse(['error' => 'Message not found or not authorized.'], JsonResponse::HTTP_NOT_FOUND);
        }

        return new JsonResponse($message);
    }

    /**
     * @OA\Post(
     *     path="/api/messages",
     *     tags={"Messages"},
     *     summary="Create a new message",
     *     @OA\RequestBody(
     *         description="Data to send a new message",
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"content"},
     *             @OA\Property(property="content", type="string", description="Content of the message"),
     *             @OA\Property(property="parentMessage", type="string", description="Parent message if any"),
     *             @OA\Property(property="conversation_id", type="integer", description="ID of the conversation the message belongs to")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Message sent successfully"
     *     )
     * )
     */
    #[Route('/', name: 'message_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $senderId = $this->getUser()->getId();
        $senderName = $this->getUser()->getFirstName();

        $messageContent = $data['content'] ?? null;
        $additionalData = [
            'parentMessage' => $data['parentMessage'] ?? null,
            'conversation_id' => $data['conversation_id'] ?? null
        ];

        if (!$messageContent) {
            return new JsonResponse(['error' => 'Message content is required.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $message = new SaveMessage($messageContent, $senderId, $senderName, $additionalData);
        $this->messageBus->dispatch($message);
        
        return new JsonResponse("Message sent", JsonResponse::HTTP_OK);

    }
}

