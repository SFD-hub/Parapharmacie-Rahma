<?php

namespace App\Livewire\Admin\Support;

use App\Models\SupportConversation;
use App\Services\NotificationService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin', ['title' => 'Conversation', 'subtitle' => null, 'search' => false])]
class Thread extends Component
{
    public SupportConversation $conversation;

    public string $content = '';

    public function mount(SupportConversation $conversation): void
    {
        $this->conversation = $conversation;

        $this->conversation->messages()
            ->whereNull('admin_id')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function sendMessage(NotificationService $notifications): void
    {
        $this->validate(['content' => ['required', 'string', 'max:2000']]);

        $this->conversation->messages()->create([
            'admin_id' => auth('admin')->id(),
            'content' => $this->content,
        ]);

        $this->conversation->update([
            'status' => 'open',
            'last_message_at' => now(),
        ]);

        if ($this->conversation->user_id) {
            $notifications->notify(
                $this->conversation->user,
                'support.message_replied',
                'Réponse du service client',
                'Vous avez reçu une réponse à votre message.',
                ['support_conversation_id' => $this->conversation->id],
            );
        }

        $this->content = '';
    }

    public function close(): void
    {
        $this->conversation->update(['status' => 'closed']);
    }

    public function reopen(): void
    {
        $this->conversation->update(['status' => 'open']);
    }

    public function render()
    {
        return view('livewire.admin.support.thread', [
            'messages' => $this->conversation->messages()->with('admin:id,name')->get(),
        ]);
    }
}
