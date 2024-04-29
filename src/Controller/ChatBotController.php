<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface; // Import LoggerInterface
use Symfony\Component\HttpFoundation\JsonResponse;

class ChatBotController extends AbstractController
{
    private $logger; // Declare logger

    // Inject logger in the constructor
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger; // Initialize logger
    }

    #[Route('/chat/bot', name: 'app_chat_bot', methods: ['POST', 'GET'])]
    public function index(Request $request): Response
    {
        return $this->render('chat_bot/index.html.twig');
    }
}
