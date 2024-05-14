<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    /**
     * @OA\Post(
     *      path="/api/login",
     *      operationId="login",
     *      tags={"Authentication"},
     *      summary="Login",
     *      description="Authenticate user",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"phone","password"},
     *              @OA\Property(property="phone", type="string", example="992123456789"),
     *              @OA\Property(property="password", type="string", example="password"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful login",
     *          @OA\JsonContent(
     *              @OA\Property(property="access_token", type="string"),
     *              @OA\Property(property="token_type", type="string", example="Bearer"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Not registered"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Invalid data",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Invalid password"),
     *          )
     *      ),
     * )
     */

    public function login(Request $request)
    {
        $request->validate([
            'phone'            => 'required|string',
            'password'         => 'required|min:6|max:20',
        ]);

        $is_registered = User::where('phone', '=', $request->phone)->first();

        if ($is_registered){

            if (Auth::attempt($request->all())) {
                $user = Auth::user();
                $user->tokens()->delete();
                $token = $user->createToken('access_token');
                return response()->json(['access_token' => $token->plainTextToken, 'token_type' => 'Bearer',], 201);
            }
            return response()->json(['message' => 'Invalid password',], 422);
        }
        return response()->json(['message' => 'Not registered',], 401);
    }

    /**
     * @OA\Post(
     *      path="/api/register-driver",
     *      operationId="registerDriver",
     *      tags={"Authentication"},
     *      summary="Register a new driver",
     *      description="Registers a new driver with provided information",
     *      security={{ "bearerAuth": {} }},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"first_name","last_name","login","password"},
     *              @OA\Property(property="first_name", type="string", example="John"),
     *              @OA\Property(property="last_name", type="string", example="Doe"),
     *              @OA\Property(property="login", type="string", example="johndoe"),
     *              @OA\Property(property="password", type="string", example="password")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful registration",
     *          @OA\JsonContent(
     *              @OA\Property(property="access_token", type="string"),
     *              @OA\Property(property="token_type", type="string", example="Bearer")
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation errors",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The given data was invalid."),
     *              @OA\Property(property="errors", type="object", example={"first_name": {"The first name field is required."}})
     *          )
     *      )
     * )
     * @OA\SecurityScheme(
     *      securityScheme="bearerAuth",
     *      in="header",
     *      name="Authorization",
     *      type="http",
     *      scheme="bearer",
     *      bearerFormat="JWT"
     * )
     */


    public function registerDriver(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string',
            'last_name'  => 'required|string',
            'login'      => ['required', 'string', 'unique:drivers'],
            'password'   => 'required|string|min:6|max:20',
        ]);

        $validatedData['password'] = Hash::make($validatedData['password']);
        Driver::create($validatedData);
        $driver = Driver::where('login', $validatedData['login'])->first();

        return response()->json(['message' => 'created successfully!', 'driver' => $driver,], 201);
    }


    /**
     *
     * @OA\Post(
     *      path="/api/login-driver",
     *      operationId="loginDriver",
     *      tags={"Authentication"},
     *      summary="Login as a driver",
     *      description="Authenticate driver with provided login and password",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"login","password"},
     *              @OA\Property(property="login", type="string", example="driver_login"),
     *              @OA\Property(property="password", type="string", example="password"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful login",
     *          @OA\JsonContent(
     *              @OA\Property(property="access_token", type="string"),
     *              @OA\Property(property="token_type", type="string", example="Bearer"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Not registered",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Not registered"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Invalid password",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Invalid password"),
     *          )
     *      ),
     * )
     */

    public function loginDriver(Request $request)
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string|max:20',
        ]);

        $driver = Driver::where('login', $request->login)->first();

        if ($driver && Hash::check($request->password, $driver->password)) {
            $driver->tokens()->delete();
            $token = $driver->createToken('access_token');

            return response()->json([
                'access_token' => $token->plainTextToken,
                'token_type' => 'Bearer',
                'user' => 'driver',
            ], 201);
        }
        return response()->json(['message' => 'Invalid login or password'], 401);
    }

}
