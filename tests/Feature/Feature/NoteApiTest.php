<?php

namespace Tests\Feature\Feature;

use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoteApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_only_sees_own_notes(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $ownerNote = Note::factory()->for($owner)->create();
        Note::factory()->for($other)->create();

        $response = $this->actingAs($owner, 'sanctum')->getJson('/api/tasks');

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $ownerNote->id);
    }

    public function test_user_can_crud_own_task(): void
    {
        $user = User::factory()->create();
        $auth = $this->actingAs($user, 'sanctum');

        $create = $auth->postJson('/api/tasks', [
            'title' => 'My task',
            'description' => 'My description',
            'statu' => 'pending',
        ]);

        $noteId = $create->json('data.id');

        $create->assertCreated()->assertJsonPath('data.title', 'My task');

        $auth->getJson("/api/tasks/{$noteId}")
            ->assertOk()
            ->assertJsonPath('data.description', 'My description');

        $auth->putJson("/api/tasks/{$noteId}", [
            'title' => 'Updated',
            'description' => 'Updated description',
            'statu' => 'done',
        ])->assertOk()
            ->assertJsonPath('data.title', 'Updated')
            ->assertJsonPath('data.statu', 'done');

        $auth->deleteJson("/api/tasks/{$noteId}")
            ->assertNoContent();
    }

    public function test_user_cannot_access_other_users_note(): void
    {
        $owner = User::factory()->create();
        $attacker = User::factory()->create();
        $note = Note::factory()->for($owner)->create();

        $this->actingAs($attacker, 'sanctum')
            ->getJson("/api/tasks/{$note->id}")
            ->assertForbidden();

        $this->actingAs($attacker, 'sanctum')
            ->putJson("/api/tasks/{$note->id}", [
                'title' => 'Hack',
            ])->assertForbidden();

        $this->actingAs($attacker, 'sanctum')
            ->deleteJson("/api/tasks/{$note->id}")
            ->assertForbidden();
    }
}
