<?php

namespace Tests\Feature\Account;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('account.notifications.index'))->assertRedirect(route('login'));
    }

    public function test_user_sees_only_their_own_notifications(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $service = app(NotificationService::class);
        $service->notify($user, 'loyalty.points_earned', 'Points gagnés pour moi');
        $service->notify($other, 'loyalty.points_earned', 'Points gagnés pour un autre');

        $response = $this->actingAs($user)->get(route('account.notifications.index'));

        $response->assertSee('Points gagnés pour moi')->assertDontSee('Points gagnés pour un autre');
    }

    public function test_viewing_the_list_marks_notifications_as_read(): void
    {
        $user = User::factory()->create();
        app(NotificationService::class)->notify($user, 'loyalty.points_earned', 'Points gagnés');

        $this->assertDatabaseHas('notifications', ['notifiable_id' => $user->id, 'read_at' => null]);

        $this->actingAs($user)->get(route('account.notifications.index'));

        $this->assertDatabaseMissing('notifications', ['notifiable_id' => $user->id, 'read_at' => null]);
    }
}
