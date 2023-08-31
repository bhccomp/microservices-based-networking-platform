<?php 

namespace App\Controller;

use App\Service\MessageService;
use App\Message\SaveMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

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

    #[Route('/', name: 'message_index', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $userId = $this->getUser()->getId();
        $messages = $this->messageService->getUserMessages($userId);

        return new JsonResponse($messages);
    }

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

