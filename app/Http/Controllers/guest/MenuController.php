<?php

namespace App\Http\Controllers\guest;

use App\Http\Controllers\Controller;
use App\Models\Products;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Guest Menu",
    description: "Endpoint API untuk menu yang diakses oleh guest / frontend"
)]
class MenuController extends Controller
{
    // #[OA\Get(
    //     path: "/api/guest/menu",
    //     summary: "Ambil daftar menu untuk guest",
    //     description: "Mengambil semua produk yang tersedia (is_available = true) beserta nama kategori",
    //     tags: ["Guest Menu"],
    //     responses: [
    //         new OA\Response(
    //             response: 200,
    //             description: "Berhasil mengambil menu guest",
    //             content: new OA\JsonContent(
    //                 type: "object",
    //                 properties: [
    //                     new OA\Property(property: "status", type: "boolean", example: true),
    //                     new OA\Property(property: "message", type: "string", example: "success"),
    //                     new OA\Property(
    //                         property: "data",
    //                         type: "array",
    //                         items: new OA\Items(
    //                             type: "object",
    //                             properties: [
    //                                 new OA\Property(property: "id", type: "integer", example: 1),
    //                                 new OA\Property(property: "name", type: "string", example: "Cappuccino"),
    //                                 new OA\Property(property: "price", type: "number", format: "float", example: 25000),
    //                                 new OA\Property(property: "is_available", type: "boolean", example: true),
    //                                 new OA\Property(property: "category_name", type: "string", example: "Minuman"),
    //                                 new OA\Property(property: "created_at", type: "string", format: "date-time"),
    //                                 new OA\Property(property: "updated_at", type: "string", format: "date-time"),
    //                             ]
    //                         )
    //                     ),
    //                 ]
    //             )
    //         ),
    //         new OA\Response(
    //             response: 500,
    //             description: "Gagal mengambil menu"
    //         )
    //     ]
    // )]
    // public function getGuestMenu()
    // {
    //     try {
    //         $products = Products::with('category')
    //             ->where('is_available', true)
    //             ->get()
    //             ->map(function ($product) {
    //                 // Tambahkan category_name agar FE bisa langsung pakai
    //                 $product->category_name = $product->category
    //                     ? $product->category->name
    //                     : null;

    //                 return $product;
    //             });

    //         return response()->json([
    //             'status'  => true,
    //             'message' => 'success',
    //             'data'    => $products
    //         ], 200);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status'  => false,
    //             'message' => 'Failed to load guest menu',
    //             'error'   => $e->getMessage(),
    //         ], 500);
    //     }
    // }

    #[OA\Get(
        path: "/api/guest/menu",
        summary: "Ambil semua produk (tanpa filter availability)",
        description: "Mengambil semua produk beserta kategori. Biasanya dipakai untuk kebutuhan admin atau internal.",
        tags: ["Guest Products"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Berhasil mengambil semua produk",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "success"),
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(
                                type: "object",
                                properties: [
                                    new OA\Property(property: "id", type: "integer", example: 1),
                                    new OA\Property(property: "name", type: "string", example: "Espresso"),
                                    new OA\Property(property: "price", type: "number", format: "float", example: 18000),
                                    new OA\Property(property: "is_available", type: "boolean", example: true),
                                    new OA\Property(property: "category_name", type: "string", example: "Minuman"),
                                    new OA\Property(property: "created_at", type: "string", format: "date-time"),
                                    new OA\Property(property: "updated_at", type: "string", format: "date-time"),
                                ]
                            )
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: "Gagal mengambil produk"
            )
        ]
    )]
    public function getProducts()
    {
        try {
            $products = Products::with('category')
                ->get()
                ->map(function ($product) {
                    $product->category_name = $product->category
                        ? $product->category->name
                        : null;

                    return $product;
                });

            return response()->json([
                'status'  => true,
                'message' => 'success',
                'data'    => $products
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to load products',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
