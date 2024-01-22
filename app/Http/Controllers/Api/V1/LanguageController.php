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

    public function __invoke(Request $request)
    {
        $data = $this->langService->collection();

        return $this->success($data, 200);
    }
}
