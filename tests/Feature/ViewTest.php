<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ViewTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/view-test');

        $response->assertStatus(200);
        $response->assertSee('Taylor');
        $response->assertSee('<p>The name is Taylor.</p>', false);

    }
}
