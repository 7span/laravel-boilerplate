<?php

namespace App\Http\Controllers\Api\V1;

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use App\Services\LanguageService;
use App\Http\Controllers\Controller;

class LanguageController extends Controller
{
    use ApiResponser;

    public function __construct(private LanguageService $langService)
    {
        //
    }

    public function index(Request $request)
    {
        $data = $this->langService->collection();

        return $this->success($data, 200);
    }

    public function show($language)
    {
        $data = $this->langService->resource($language);

        return isset($data['errors']) ? $this->error($data) : $this->success($data, 200);
    }
}
