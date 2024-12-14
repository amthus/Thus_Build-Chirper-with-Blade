<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Chirp;
use App\Models\User;
use Database\Factories\ChirpFactory;
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

    // /**Exercice3 */
    // public function test_les_chirps_sont_affiches_sur_la_page_d_accueil()
    // {
    //     $chirps = Chirp::factory()->count(3)->create();

    //     $reponse = $this->get('/');
    //     foreach ($chirps as $chirp) {
    //         $reponse->assertSee($chirp->message);
    //     }
    // }

    public function test_un_utilisateur_peut_modifier_son_chirp()
    {
        $utilisateur = User::factory()->create();
        $chirp = Chirp::factory()->create(['user_id' => $utilisateur->id]);

        $this->actingAs($utilisateur);
        $nouveauContenu = 'Chirp modifié';

        $reponse = $this->put("/chirps/{$chirp->id}", [
            'message' => $nouveauContenu
        ]);

        $reponse->assertStatus(302);
        $this->assertDatabaseHas('chirps', [
            'id' => $chirp->id,
            'message' => $nouveauContenu,
        ]);
    }



}
