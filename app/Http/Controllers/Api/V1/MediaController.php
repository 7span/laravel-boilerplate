<?php

<<<<<<< HEAD:app/Http/Controllers/Api/MediaController.php
declare(strict_types=1);

namespace App\Http\Controllers\Api;
=======
namespace App\Http\Controllers\Api\V1;
>>>>>>> origin/master:app/Http/Controllers/Api/V1/MediaController.php

use App\Models\Media;
use App\Traits\ApiResponser;
use App\Services\MediaService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Dedoc\Scramble\Attributes\Group;

/**
 * @tags Media
 */
#[Group('Media', weight: 50)]
class MediaController extends Controller
{
    use ApiResponser;

    public function __construct(private MediaService $mediaService) {}

    /**
     * Delete.
     *
     * @response array{message: string}
     */
    public function destroy(Media $media): JsonResponse
    {
        $data = $this->mediaService->destroy($media);

        return $this->success($data);
    }
}
