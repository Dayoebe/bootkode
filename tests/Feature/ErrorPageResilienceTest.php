<?php

namespace Tests\Feature;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class ErrorPageResilienceTest extends TestCase
{
    public function test_server_error_response_renders_without_any_database_connection(): void
    {
        config(['app.debug' => false]);
        $this->withoutVite();
        $original = Model::getConnectionResolver();
        $unavailable = Mockery::mock(ConnectionResolverInterface::class);
        $unavailable->shouldNotReceive('connection');
        Model::setConnectionResolver($unavailable);

        try {
            $response = app(ExceptionHandler::class)->render(Request::create('/'), new RuntimeException('private-original-failure'));
            $this->assertSame(500, $response->getStatusCode());
            $this->assertStringContainsString('Unable to complete your request', $response->getContent());
            $this->assertStringContainsString(route('home'), $response->getContent());
            $this->assertStringNotContainsString('private-original-failure', $response->getContent());
            $this->assertStringNotContainsString('support@example.com', $response->getContent());
        } finally {
            Model::setConnectionResolver($original);
        }
    }
}
