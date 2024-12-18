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

    // /**Exercice4 */

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


        // /**Exercice5 */
    public function test_un_utilisateur_peut_supprimer_son_chirp()
    {
        $utilisateur = User::factory()->create();
        $chirp = Chirp::factory()->create(['user_id' => $utilisateur->id]);
        $this->actingAs($utilisateur);
        $reponse = $this->delete("/chirps/{$chirp->id}");
        $reponse->assertStatus(302);
        $this->assertDatabaseMissing('chirps', [
            'id' => $chirp->id,
        ]);
    }

            // /**Exercice6 */

    public function test_utilisateur_ne_peut_pas_modifier_ou_supprimer_le_chirp_d_un_autre_utilisateur()
    {
        $utilisateur1 = User::factory()->create();
        $chirp1 = Chirp::factory()->create(['user_id' => $utilisateur1->id]);
        
        $utilisateur2 = User::factory()->create();
        
        $this->actingAs($utilisateur2);
        
        $reponse = $this->delete("/chirps/{$chirp1->id}");
        $reponse->assertStatus(403);
        
        $reponse = $this->put("/chirps/{$chirp1->id}", [
            'message' => 'Nouveau message'
        ]);
        $reponse->assertStatus(403);
    }

            // /**Exercice7 */

    public function test_validation_lors_de_la_mise_a_jour_d_un_chirp()
    {
        $utilisateur = User::factory()->create();
        $chirp = Chirp::factory()->create(['user_id' => $utilisateur->id]);
                
        $this->actingAs($utilisateur);
                
        $reponse = $this->put("/chirps/{$chirp->id}", [
        'message' => ''
        ]);
        $reponse->assertSessionHasErrors('message');
                
        $reponse = $this->put("/chirps/{$chirp->id}", [
        'message' => str_repeat('A', 256) 
    ]);
       $reponse->assertSessionHasErrors('message');
    }

                // /**Exercice8 */

    public function test_utilisateur_limite_a_10_chirps()
    {
        
        $utilisateur = User::factory()->create();
        $this->actingAs($utilisateur);

        for ($i = 0; $i < 10; $i++) {
            Chirp::factory()->create(['user_id' => $utilisateur->id]);
        }
    
        $reponse = $this->post('/chirps', ['message' => 'Un autre chirp']);
        $reponse->assertSessionHasErrors('message');
        $reponse->assertStatus(302);
    }
 

}
