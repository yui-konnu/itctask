<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use App\Services\ApiClientService;


trait CreatesApiClient
{
    /**
     * Creates ApiClientService object.
     *
     * @return \App\Services\ApiClientService
     */
    public function createApiClient() {
        return app(ApiClientService::class);
    }
}
