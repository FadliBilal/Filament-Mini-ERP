<?php

namespace App\Filament\Pages;

use App\Services\ChatbotService;
use Filament\Pages\Page;
use Livewire\Component;

class Chatbot extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static string $view = 'filament.pages.chatbot';

    public string $userInput = '';
    public array $chatHistory = [];

    public function mount(): void
    {
        // Pesan sambutan awal
        $this->chatHistory[] = ['role' => 'ai', 'content' => 'Halo! Saya ERPav. Silakan ajukan pertanyaan seputar penjualan, pembelian, atau stok produk.'];
    }

    public function sendMessage(): void
    {
        if (empty($this->userInput)) {
            return;
        }

        // Tambahkan pesan user ke riwayat chat
        $userMessage = $this->userInput;
        $this->chatHistory[] = ['role' => 'user', 'content' => $userMessage];
        $this->userInput = ''; // Kosongkan input

        // Tampilkan status "mengetik..."
        $this->chatHistory[] = ['role' => 'ai', 'content' => '...', 'loading' => true];

        // Proses pesan dan dapatkan jawaban dari AI
        $chatbotService = new ChatbotService();
        $aiResponse = $chatbotService->processMessage($userMessage);

        // Hapus status "mengetik..." dan ganti dengan jawaban AI
        array_pop($this->chatHistory);
        $this->chatHistory[] = ['role' => 'ai', 'content' => $aiResponse];
    }
}