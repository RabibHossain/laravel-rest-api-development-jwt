<?php

namespace App\Http\Controllers\Api\Auth;

use App\HelperService\Helpers;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Contracts\Providers\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;




class AuthController extends Controller
{
    private $loginAfterSignUp = true;
    private $helpers;
    private $created;
    private $createdCode;
    private $badRequest;
    private $badRequestCode;
    private $unauthorized;
    private $unauthorizedCode;
    private $accepted;
    private $acceptedCode;
    private $internalError;
    private $internalErrorCode;

    public function __construct(
        Helpers $helpers
    )
    {
        $this->created = 'Created';
        $this->createdCode = 201;
        $this->badRequest = 'Bad Request';
        $this->badRequestCode = 400;
        $this->unauthorized = 'Unauthorized';
        $this->unauthorizedCode = 401;
        $this->accepted = 'Accept';
        $this->acceptedCode = 200;
        $this->internalError = 'Internal Error';
        $this->internalErrorCode = 500;
        $this->helpers = $helpers;
    }
    
    protected function createNewToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60
        ];
    }
    
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $user = new User();
        $user->name = $request->get('name');
        $user->email = $request->get('email');
        $plainPassword = $request->get('password');
        $user->password = Hash::make($plainPassword);
        $user->save();

        $response = [
            "user" => $user
        ];
        return $this->helpers->response(true, $this->created, $response, null, $this->createdCode);
    }

    public function login(Request $request)
    {
        $rules = [
            'email.required' => 'Email can not be empty.',
            'password.required' => 'Password can not be empty.'
        ];
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string',
        ], $rules);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->helpers->response(false, $this->badRequest, null, $errors, $this->badRequestCode);
        }

        $credentials = $request->only("email", "password");
        $token = null;
       
        if (!$token = JWTAuth::attempt($credentials)) {
            return $this->helpers->response(false, $this->unauthorized, null, null, $this->unauthorizedCode);
        }

        
        return $this->helpers->response(true, $this->accepted, $this->createNewToken($token), null, $this->acceptedCode);
    }

    public function logout(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);
 
        try {
            JWTAuth::invalidate($request->token);
            $details = "Logged out";
            return $this->helpers->response(true, $this->accepted, $details, null, $this->acceptedCode);
        } catch (JWTException $exception) {
            $details = "Please try again!";
            return $this->helpers->response(false, $this->internalError, $details, null, $this->internalErrorCode);
        }
    }

    public function getAuthUser(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);
 
        $user = JWTAuth::authenticate($request->token);
        $response = [
            "user" => $user
        ];

        return $this->helpers->response(true, $this->accepted, $response, null, $this->acceptedCode);
    }    

    public function refresh()
    {
        $refreshToken = $this->createNewToken(JWTAuth::refresh());
        return $this->helpers->response(true, $this->accepted, $refreshToken, null, $this->acceptedCode);
    }

}
