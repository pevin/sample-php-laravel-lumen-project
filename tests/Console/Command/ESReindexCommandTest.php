<?php

namespace Tests\Console\Command;

use App\ES\ESReindexService;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class ESReindexCommandTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testHandle()
    {
        $service = $this->getMockForAbstractClass(ESReindexService::class);
        $service->method('reindex');
        $indexTypes = [
            'test_type' => '/Test/ESReIndexCommand/TestType'
        ];
        app()->instance('/Test/ESReIndexCommand/TestType', $service);

        $this->app['config']->set('es-reindex', $indexTypes);

        Artisan::call('es:reindex', ['--type' => 'test_type']);

        $resultAsText = Artisan::output();

        $this->assertEmpty($resultAsText);
    }

    public function testHandleInvalidType()
    {
        $indexTypes = [
            'test_type' => '/Test/ESReIndexCommand/TestType'
        ];

        $this->app['config']->set('es-reindex', $indexTypes);

        Artisan::call('es:reindex', ['--type' => 'invalid_type']);

        $resultAsText = Artisan::output();

        $this->assertEquals($resultAsText, "Index type specified is unknown\n");
    }
}
