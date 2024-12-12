<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
class ChirpTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**Exercice1 */
    public function test_un_utilisateur_peut_creer_un_chirp(): void
    {
        $utilisateur = User::factory()->create();

        $this->actingAs($utilisateur);

        $reponse = $this->post('/chirps', [
            'message' => 'Malthus first chirp !',
            '_token' => csrf_token(), 
        ]);

        $reponse->assertStatus(302);
        $this->assertDatabaseHas('chirps', [
            'message' => 'Malthus first chirp !',
            'user_id' => $utilisateur->id,
        ]);
    }

    /**Exercice2 */
    public function test_un_chirp_ne_peut_pas_avoir_un_contenu_vide()
    {
        $utilisateur = User::factory()->create();
        $this->actingAs($utilisateur);
        
        $reponse = $this->post('/chirps', [
            'message' => ''
        ]);
        
        $reponse->assertSessionHasErrors(['message']);
    }

    public function test_un_chirp_ne_peut_pas_depasse_255_caracteres()
    {
    $utilisateur = User::factory()->create();
    $this->actingAs($utilisateur);
    
    $reponse = $this->post('/chirps', [
        'message' => str_repeat('a', 256)
    ]);
    
    $reponse->assertSessionHasErrors(['message']);
    }

}
