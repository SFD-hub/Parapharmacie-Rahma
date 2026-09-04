<?php

namespace App\Livewire\Support;

use App\Models\SupportConversation;
use App\Services\NotificationService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.web', ['title' => 'Discutez avec nous', 'search' => false])]
class Chat extends Component
{
    public SupportConversation $conversation;

    public string $name = '';

    public string $content = '';

    public function mount(): void
    {
        $this->conversation = auth()->check()
            ? SupportConversation::query()->firstOrCreate(['user_id' => auth()->id()])
            : SupportConversation::query()->firstOrCreate(['guest_token' => SupportConversation::currentGuestToken()]);

        $this->conversation->messages()
            ->whereNotNull('admin_id')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function sendMessage(NotificationService $notifications): void
    {
        $this->validate([
            'name' => [auth()->check() || $this->conversation->guest_name ? 'nullable' : 'required', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:2000'],
        ]);

        $this->conversation->messages()->create([
            'admin_id' => null,
            'content' => $this->content,
        ]);

        $this->conversation->update([
            'guest_name' => $this->conversation->guest_name ?? ($this->name ?: null),
            'status' => 'open',
            'last_message_at' => now(),
        ]);

        $notifications->notifyAdmins(
            'support.message_received',
            'Nouveau message client',
            "{$this->conversation->displayName()} a envoyé un nouveau message sur le chat support.",
            ['support_conversation_id' => $this->conversation->id],
        );

        $this->content = '';
    }

    public function render()
    {
        return view('livewire.support.chat', [
            'messages' => $this->conversation->messages()->with('admin:id,name')->get(),
        ]);
    }
}
