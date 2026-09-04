<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $this->get(route('admin.notifications.index'))->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_view_their_notifications(): void
    {
        $admin = Admin::factory()->create();
        $admin->notifications()->create([
            'type' => 'order.created',
            'title' => 'Nouvelle commande CMD-1',
            'message' => null,
            'data' => [],
        ]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.notifications.index'));

        $response->assertOk()->assertSee('Nouvelle commande CMD-1');
    }

    public function test_visiting_the_notifications_page_marks_them_as_read(): void
    {
        $admin = Admin::factory()->create();
        $notification = $admin->notifications()->create([
            'type' => 'order.created',
            'title' => 'Nouvelle commande',
            'message' => null,
            'data' => [],
        ]);

        $this->assertNull($notification->fresh()->read_at);

        $this->actingAs($admin, 'admin')->get(route('admin.notifications.index'));

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_an_admin_only_sees_their_own_notifications(): void
    {
        $admin = Admin::factory()->create();
        $other = Admin::factory()->create();
        $admin->notifications()->create(['type' => 'order.created', 'title' => 'Pour moi', 'message' => null, 'data' => []]);
        $other->notifications()->create(['type' => 'order.created', 'title' => 'Pour un autre', 'message' => null, 'data' => []]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.notifications.index'));

        $response->assertSee('Pour moi')->assertDontSee('Pour un autre');
    }
}
