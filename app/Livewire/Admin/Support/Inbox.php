<?php

namespace App\Livewire\Admin\Support;

use App\Models\SupportConversation;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin', ['title' => 'Support client', 'subtitle' => null, 'search' => false])]
class Inbox extends Component
{
    use WithPagination;

    #[Url]
    public string $status = '';

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $conversations = SupportConversation::query()
            ->withCount(['messages as unread_count' => fn ($query) => $query->whereNull('admin_id')->whereNull('read_at')])
            ->with('user:id,name')
            ->when($this->status !== '', fn ($query) => $query->where('status', $this->status))
            ->orderByDesc('last_message_at')
            ->paginate(20);

        return view('livewire.admin.support.inbox', ['conversations' => $conversations]);
    }
}
