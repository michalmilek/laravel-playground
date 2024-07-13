<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Enums\Status;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Auth;
use Hash;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/register",
     *     tags={"Auth"},
     *     summary="Register a new user",
     *     description="Registers a new user and returns success status",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="response_code", type="string", example="200"),
     *             @OA\Property(property="status", type="string", example="success")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|min:4|max:20',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:8|max:20',
        ]);

        $dt        = Carbon::now();
        $join_date = $dt->toDayDateTimeString();

        $user = new User();
        $user->name         = $request->name;
        $user->email        = $request->email;
        $user->password     = Hash::make($request->password);
        $user->save();

        $data = [];
        $data['response_code']  = '200';
        $data['status']         = Status::SUCCESS;
        return response()->json($data);
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Auth"},
     *     summary="Login user",
     *     description="Logs in the user and returns a token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="response_code", type="string", example="200"),
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|string',
            'password' => 'required|string',
        ]);

        try {
            $email     = $request->email;
            $password  = $request->password;

            if (Auth::attempt(['email' => $email, 'password' => $password]))
            {
                $user = Auth::User();
                $accessToken = $user->createToken($user->email)->accessToken;

                $data = [];
                $data['response_code']  = '200';
                $data['status']         = Status::SUCCESS;
                $data['token']          = $accessToken;
                return response()->json($data);
            } else {
                $data = [];
                $data['response_code']  = '401';
                $data['status']         = Status::ERROR;
                $data['message']        = 'Unauthorised';
                return response()->json($data);
            }
        } catch(\Exception $e) {
            \Log::info($e);
            $data = [];
            $data['response_code']  = '401';
            $data['status']         = Status::ERROR;
            $data['message']        = 'fail Login';
            return response()->json($data);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/user-info",
     *     tags={"Auth"},
     *     summary="Get user information",
     *     description="Returns a paginated list of users",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="response_code", type="string", example="200"),
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="success get user list"),
     *             @OA\Property(property="data_user_list", type="array", 
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *                     @OA\Property(property="created_at", type="string", example="2023-07-13 12:34:56"),
     *                     @OA\Property(property="updated_at", type="string", example="2023-07-13 12:34:56")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function userInfo()
    {
        try {
            $userDataList = User::latest()->paginate(10);
            $data = [];
            $data['response_code']  = '200';
            $data['status']         = Status::SUCCESS;
            $data['message']        = 'success get user list';
            $data['data_user_list'] = $userDataList;
            return response()->json($data);
        } catch(\Exception $e) {
            \Log::info($e);
            $data = [];
            $data['response_code']  = '400';
            $data['status']         = Status::ERROR;
            $data['message']        = 'fail get user list';
            return response()->json($data);
        }
    }
}
