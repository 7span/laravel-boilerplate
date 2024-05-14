<?php

namespace App\Http\Controllers\Api\V1;

use App\Traits\ApiResponser;
use App\Services\UserService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\UpdateProfile;
use App\Http\Resources\User\Resource as UserResource;

class UserController extends Controller
{
    use ApiResponser;

    public function __construct(private UserService $userService)
    {
        //
    }

    /**
     * @OA\Get(
     *     path="/api/v1/me",
     *     tags={"Auth"},
     *     summary="Get logged-in user details",
     *
     *     @OA\Response(response="200", description="Success"),
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="X-Requested-With",
     *         in="header",
     *         required=true,
     *         description="Custom header for XMLHttpRequest",
     *
     *         @OA\Schema(
     *             type="string",
     *             default="XMLHttpRequest"
     *         )
     *     )
     * )
     */
    public function me()
    {
        $user = $this->userService->resource(Auth::id());

        return $this->resource(new UserResource($user));
    }

    /**
     * @OA\Post(
     *     path="/api/v1/me",
     *     operationId="updateProfile",
     *     tags={"Auth"},
     *     summary="Update Profile",
     *
     *      @OA\RequestBody(
     *           required=true,
     *
     *           @OA\JsonContent(
     *               required={"first_name","last_name","username","email"},
     *
     *               @OA\Property(
     *                     property="first_name",
     *                     type="string",
     *                     format="first_name",
     *                     example="Test"
     *               ),
     *               @OA\Property(
     *                      property="last_name",
     *                      type="string",
     *                      format="last_name",
     *                      example="User"
     *              ),
     *              @OA\Property(
     *                      property="username",
     *                      type="string",
     *                      format="username",
     *                      example="user12"
     *              ),
     *              @OA\Property(
     *                      property="email",
     *                      type="string",
     *                      format="email",
     *                      example="test@gmail.com"
     *              ),
     *              @OA\Property(
     *                      property="mobile_number",
     *                      type="string",
     *                      format="mobile",
     *                      example="9090909090"
     *              ),
     *           ),
     *       ),
     *
     *     @OA\Response(response="200", description="Profile updated successfully"),
     *     @OA\Response(response="401", description="Validation errors!"),
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="X-Requested-With",
     *         in="header",
     *         required=true,
     *         description="Custom header for XMLHttpRequest",
     *
     *         @OA\Schema(
     *             type="string",
     *             default="XMLHttpRequest"
     *         )
     *     )
     *  )
     */
    public function updateProfile(UpdateProfile $request)
    {
        $data = $this->userService->update(Auth::id(), $request->all());

        return isset($data['errors']) ? $this->error($data) : $this->success($data, 200);
    }
}
