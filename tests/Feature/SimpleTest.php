<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Forfait;
use App\Models\Proprio;

class SimpleTest extends TestCase
{
    /**
     * Test basic page access with authentication
     */
    public function test_authenticated_page_access()
    {
        // Create a user for testing
        $user = Proprio::factory()->create();
        
        $response = $this->actingAs($user, 'proprio')
                        ->get('/forfait-ticket');
        
        echo "Response status: " . $response->getStatusCode() . "\n";
        
        // Should be 200 (success) now
        $this->assertEquals(200, $response->getStatusCode());
    }
    
    /**
     * Test unauthenticated access
     */
    public function test_unauthenticated_page_access()
    {
        $response = $this->get('/forfait-ticket');
        
        echo "Unauthenticated response status: " . $response->getStatusCode() . "\n";
        
        // Should be 302 (redirect to login)
        $this->assertEquals(302, $response->getStatusCode());
    }
}
