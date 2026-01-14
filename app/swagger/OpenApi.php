<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\OpenApi(
    info: new OA\Info(
        title: "Cafe Backend API",
        version: "1.0.0",
        description: "Dokumentasi API Cafe Backend"
    ),
    components: new OA\Components(
        securitySchemes: [
            new OA\SecurityScheme(
                securityScheme: "bearerAuth",
                type: "http",
                scheme: "bearer",
                bearerFormat: "JWT",
                description: "Masukkan token JWT dengan format: Bearer {token}"
            )
        ]
    )
)]
class OpenApi {}
