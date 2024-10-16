<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginUserRequest;
use App\Http\Requests\ApiLoginRequest;
use App\Models\User;
use App\Permisson\V1\Abilities;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ApiResponses;
    public function Login(LoginUserRequest $request) {
        $request->validated($request->all());

        if(!Auth::attempt($request->only('email', 'password'))){
            return $this->error('Invalid credentials', 401);
        }
        $user = User::firstWhere('email', $request->email);
        $data = [
            'token' => $user->createToken('API token for ' . $user->email, [Abilities::getAbilites($user)], now()->addMinutes($value = 60))->plainTextToken,
        ];
        return $this->ok('Authenticated', $data);
    }

    public function Logout(Request $request) {
        // $request->user()->tokens()->delete();
        // $request->user()->tokens()->where('id', $tokenId)->delete();
        $request->user()->currentAccessToken()->delete();
         
    }

    public function register() {
        // return $this->ok('Register');
    }
}

  