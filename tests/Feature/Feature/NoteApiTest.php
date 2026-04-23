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

        $response = $this->actingAs($owner, 'sanctum')->getJson('/api/notes');

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $ownerNote->id);
    }

    public function test_user_can_crud_own_note(): void
    {
        $user = User::factory()->create();
        $auth = $this->actingAs($user, 'sanctum');

        $create = $auth->postJson('/api/notes', [
            'title' => 'My note',
            'content' => 'My content',
        ]);

        $noteId = $create->json('data.id');

        $create->assertCreated()->assertJsonPath('data.title', 'My note');

        $auth->getJson("/api/notes/{$noteId}")
            ->assertOk()
            ->assertJsonPath('data.content', 'My content');

        $auth->putJson("/api/notes/{$noteId}", [
            'title' => 'Updated',
            'content' => 'Updated content',
        ])->assertOk()
            ->assertJsonPath('data.title', 'Updated');

        $auth->deleteJson("/api/notes/{$noteId}")
            ->assertNoContent();
    }

    public function test_user_cannot_access_other_users_note(): void
    {
        $owner = User::factory()->create();
        $attacker = User::factory()->create();
        $note = Note::factory()->for($owner)->create();

        $this->actingAs($attacker, 'sanctum')
            ->getJson("/api/notes/{$note->id}")
            ->assertForbidden();

        $this->actingAs($attacker, 'sanctum')
            ->putJson("/api/notes/{$note->id}", [
                'title' => 'Hack',
            ])->assertForbidden();

        $this->actingAs($attacker, 'sanctum')
            ->deleteJson("/api/notes/{$note->id}")
            ->assertForbidden();
    }
}
