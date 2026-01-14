<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Authentication",
    description: "Endpoint autentikasi menggunakan JWT"
)]
class AuthController extends Controller
{
    #[OA\Post(
        path: "/api/auth/login",
        summary: "Login user dan mendapatkan JWT token",
        tags: ["Authentication"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "password"],
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "admin@gmail.com"),
                    new OA\Property(property: "password", type: "string", example: "12345678"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Login berhasil",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "access_token", type: "string"),
                        new OA\Property(property: "token_type", type: "string", example: "Bearer"),
                        new OA\Property(property: "expires_in", type: "integer", example: 3600),
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Invalid credentials")
        ]
    )]
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = Auth::guard('api')->user();

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'expires_in'   => JWTAuth::factory()->getTTL() * 60,
            'user' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
            ],
        ]);
    }


    #[OA\Get(
        path: "/api/auth/me",
        summary: "Ambil data user yang sedang login",
        tags: ["Authentication"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Data user berhasil diambil"),
            new OA\Response(response: 401, description: "Unauthorized")
        ]
    )]
    public function me()
    {
        return response()->json(Auth::guard('api')->user());
    }

    #[OA\Post(
        path: "/api/auth/refresh",
        summary: "Refresh JWT token",
        tags: ["Authentication"],
        requestBody: new OA\RequestBody(
            required: false,
            description: "Token bisa dikirim lewat Authorization header atau lewat body",
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: "token",
                        type: "string",
                        example: "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Token berhasil diperbarui",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "access_token", type: "string"),
                        new OA\Property(property: "token_type", type: "string", example: "Bearer"),
                        new OA\Property(property: "expires_in", type: "integer", example: 3600),
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthorized / Token tidak valid")
        ]
    )]
    public function refresh(Request $request)
    {
        try {
            // Ambil token dari body kalau ada
            if ($request->filled('token')) {
                JWTAuth::setToken($request->input('token'));
            }

            // Kalau tidak ada di body, JWTAuth akan otomatis cari di Authorization header
            $newToken = JWTAuth::refresh();

            return response()->json([
                'access_token' => $newToken,
                'token_type'   => 'Bearer',
                'expires_in'   => JWTAuth::factory()->getTTL() * 60,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ], 401);
        }
    }


    #[OA\Post(
        path: "/api/auth/logout",
        summary: "Logout user dan invalidasi token",
        tags: ["Authentication"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Logout berhasil"),
            new OA\Response(response: 401, description: "Unauthorized")
        ]
    )]
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
}
