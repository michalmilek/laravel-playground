<?php

namespace App\Http\Controllers\API;

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
    /** register new account */
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
        $user->name         = $request->name ;
        $user->email        = $request->email;
        $user->password     = Hash::make($request->password);
        $user->save();

        $data = [];
        $data['response_code']  = '200';
        $data['status']         = Status::SUCCESS;
        return response()->json($data);
    }

    /**
     * Login Req
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

            if (Auth::attempt(['email' => $email,'password' => $password]))
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

    /** user info */
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
