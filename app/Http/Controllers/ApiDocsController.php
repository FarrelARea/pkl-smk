<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Facade;
use OpenApi\Annotations\OpenApi;

/**
 * API Documentation Controller
 * 
 * Provides access to Swagger UI and OpenAPI JSON specs
 */
class ApiDocsController extends Controller
{
    /**
     * Redirect to Swagger UI
     */
    public function index()
    {
        return redirect('/api/documentation');
    }
}
