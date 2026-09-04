<?php

namespace App\Listeners;

use App\Models\SupportConversation;
use App\Models\User;
use Illuminate\Auth\Events\Login;

class MergeGuestSupportConversationOnLogin
{
    /**
     * When a client logs in, fold whatever guest support thread their
     * browser session was tracking into their account's thread — same
     * pattern as MergeGuestCartOnLogin.
     */
    public function handle(Login $event): void
    {
        if ($event->guard !== 'web' || ! $event->user instanceof User) {
            return;
        }

        $token = session('support.guest_token');

        if (! $token) {
            return;
        }

        $guestConversation = SupportConversation::query()
            ->whereNull('user_id')
            ->where('guest_token', $token)
            ->first();

        if (! $guestConversation) {
            return;
        }

        $userConversation = SupportConversation::query()->where('user_id', $event->user->id)->first();

        if ($userConversation) {
            $guestConversation->messages()->update(['support_conversation_id' => $userConversation->id]);
            $userConversation->update(['last_message_at' => $guestConversation->last_message_at ?? $userConversation->last_message_at]);
            $guestConversation->delete();
        } else {
            $guestConversation->update(['user_id' => $event->user->id, 'guest_token' => null]);
        }

        session()->forget('support.guest_token');
    }
}
