<?php

class ChatbotController extends Controller {
    private ChatbotService $chatbotService;

    public function __construct() {
        $this->chatbotService = new ChatbotService();
    }

    /**
     * Tampilan Antarmuka AI Chatbot Modern & Minimalis
     */
    public function index(): void {
        $user = $this->getUser();

        $this->view('chatbot/index', [
            'title' => 'AI Database Chatbot - ' . INSTITUTION_NAME,
            'user' => $user
        ]);
    }

    /**
     * Endpoint API Tanya Jawab Chatbot (AJAX)
     */
    public function ask(): void {
        $prompt = trim($_POST['message'] ?? '');
        $user = $this->getUser();

        if (empty($prompt)) {
            $this->json([
                'success' => false,
                'message' => 'Pesan tidak boleh kosong.'
            ], 400);
        }

        $result = $this->chatbotService->ask($prompt, $user);
        $this->json($result);
    }
}

