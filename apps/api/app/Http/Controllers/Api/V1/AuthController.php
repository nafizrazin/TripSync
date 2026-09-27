<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\{LoginRequest,RegisterRequest};
use App\Http\Resources\UserResource;
use App\Models\{Role,User};
use Illuminate\Http\{JsonResponse,Request};
use Illuminate\Support\Facades\Auth;
final class AuthController extends Controller {
    public function csrf(Request $request): JsonResponse { return response()->json(['csrf_token'=>csrf_token()]); }
    public function register(RegisterRequest $request): JsonResponse {
        $user=User::query()->create($request->safe()->only(['name','email','phone','password'])+['status'=>'active']);
        $customer=Role::query()->where('name','customer')->firstOrFail();
        $user->roles()->attach($customer->id);
        Auth::login($user); $request->session()->regenerate();
        return (new UserResource($user->load('roles')))->response()->setStatusCode(201);
    }
    public function login(LoginRequest $request): JsonResponse {
        if(!Auth::attempt($request->validated(),true)) return response()->json(['message'=>'The supplied credentials are invalid.','code'=>'INVALID_CREDENTIALS'],422);
        $request->session()->regenerate();
        return (new UserResource($request->user()->load('roles')))->response();
    }
    public function logout(Request $request): JsonResponse {
        Auth::guard('web')->logout(); $request->session()->invalidate(); $request->session()->regenerateToken();
        return response()->json(['message'=>'Signed out.']);
    }
    public function me(Request $request): UserResource { return new UserResource($request->user()->load('roles')); }
}
