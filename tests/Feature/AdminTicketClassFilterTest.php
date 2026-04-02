<?php

namespace Tests\Feature;

use App\Models\ClassRoom;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminTicketClassFilterTest extends TestCase
{
    use RefreshDatabase;

    private function createEvent(): Event
    {
        return Event::create([
            'name' => 'Test Event',
            'slug' => 'test-event',
            'status' => 'published',
            'starts_at' => now(),
            'ends_at' => now()->addDays(1),
        ]);
    }

    private function createTicket(Event $event, ?string $classRoomName): Ticket
    {
        return Ticket::create([
            'event_id' => $event->id,
            'name' => 'Parent Name',
            'parent_name' => 'Parent Name',
            'email' => 'parent' . Str::random(5) . '@example.com',
            'registrant_type' => 'parent',
            'qr_token' => bin2hex(random_bytes(32)),
            'children' => $classRoomName
                ? [['name' => 'Child', 'class_room' => $classRoomName]]
                : [['name' => 'Child', 'class_room' => null]],
        ]);
    }

    public function test_ticket_list_shows_all_tickets_without_filter(): void
    {
        $user = User::factory()->create();
        $event = $this->createEvent();
        $this->createTicket($event, 'Kelas 1A');
        $this->createTicket($event, 'Kelas 2B');

        $response = $this->actingAs($user)
            ->get("/admin/events/{$event->id}/tickets");

        $response->assertStatus(200);
        $response->assertViewHas('tickets', fn($tickets) => $tickets->total() === 2);
    }

    public function test_ticket_list_filters_by_class_id(): void
    {
        $user = User::factory()->create();
        $event = $this->createEvent();
        $classRoom1A = ClassRoom::create(['name' => 'Kelas 1A']);
        $classRoom2B = ClassRoom::create(['name' => 'Kelas 2B']);
        $this->createTicket($event, 'Kelas 1A');
        $this->createTicket($event, 'Kelas 2B');

        $response = $this->actingAs($user)
            ->get("/admin/events/{$event->id}/tickets?class_id={$classRoom1A->id}");

        $response->assertStatus(200);
        $response->assertViewHas('tickets', fn($tickets) => $tickets->total() === 1);
    }

    public function test_ticket_list_passes_class_rooms_to_view(): void
    {
        $user = User::factory()->create();
        $event = $this->createEvent();
        ClassRoom::create(['name' => 'Kelas 3C']);

        $response = $this->actingAs($user)
            ->get("/admin/events/{$event->id}/tickets");

        $response->assertStatus(200);
        $response->assertViewHas('classRooms', fn($classRooms) => $classRooms->isNotEmpty());
    }
}
