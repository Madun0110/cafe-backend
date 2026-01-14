<?php

namespace App\Http\Controllers\guest;

use App\Http\Controllers\Controller;
use App\Models\Orders;
use App\Models\Products;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Guest Orders",
    description: "Endpoint API untuk pembuatan order oleh guest / frontend"
)]
class OrderController extends Controller
{
    #[OA\Post(
        path: "/api/guest/order",
        summary: "Membuat order baru",
        description: "Menerima data order dari guest. Bisa berupa cart_json (dari Blade FE) atau array products langsung.",
        tags: ["Guest Orders"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: "object",
                required: ["table", "customer_name", "customer_phone", "payment_method", "products"],
                properties: [
                    new OA\Property(
                        property: "table",
                        type: "string",
                        example: "A1",
                        description: "Nomor/meja pelanggan"
                    ),
                    new OA\Property(
                        property: "customer_name",
                        type: "string",
                        example: "Budi"
                    ),
                    new OA\Property(
                        property: "customer_phone",
                        type: "string",
                        example: "08123456789"
                    ),
                    new OA\Property(
                        property: "payment_method",
                        type: "string",
                        example: "cash"
                    ),
                    new OA\Property(
                        property: "products",
                        type: "array",
                        description: "Daftar produk yang dipesan",
                        items: new OA\Items(
                            type: "object",
                            required: ["id", "quantity"],
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "quantity", type: "integer", example: 2),
                            ]
                        )
                    ),
                    new OA\Property(
                        property: "cart_json",
                        type: "string",
                        nullable: true,
                        example: "[{\"id\":1,\"quantity\":2},{\"id\":3,\"quantity\":1}]",
                        description: "Alternatif input jika request berasal dari Blade FE"
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Order berhasil dibuat",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Order created successfully"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            description: "Detail order beserta transaksi",
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 10),
                                new OA\Property(property: "table", type: "string", example: "A1"),
                                new OA\Property(property: "customer_name", type: "string", example: "Budi"),
                                new OA\Property(property: "customer_phone", type: "string", example: "08123456789"),
                                new OA\Property(property: "status", type: "string", example: "pending"),
                                new OA\Property(property: "total", type: "number", format: "float", example: 75000),
                                new OA\Property(
                                    property: "items",
                                    type: "array",
                                    items: new OA\Items(
                                        type: "object",
                                        properties: [
                                            new OA\Property(property: "product_id", type: "integer", example: 1),
                                            new OA\Property(property: "product_name", type: "string", example: "Cappuccino"),
                                            new OA\Property(property: "category_name", type: "string", example: "Minuman"),
                                            new OA\Property(property: "quantity", type: "integer", example: 2),
                                            new OA\Property(property: "price", type: "number", format: "float", example: 25000),
                                            new OA\Property(property: "total", type: "number", format: "float", example: 50000),
                                        ]
                                    )
                                ),
                                new OA\Property(
                                    property: "transaction",
                                    type: "object",
                                    properties: [
                                        new OA\Property(property: "id", type: "integer", example: 5),
                                        new OA\Property(property: "payment_method", type: "string", example: "cash"),
                                        new OA\Property(property: "total", type: "number", format: "float", example: 75000),
                                    ]
                                ),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validation error"
            ),
            new OA\Response(
                response: 500,
                description: "Internal server error"
            )
        ]
    )]
    public function store(Request $request)
    {
        /**
         * 1. Kalau datang dari Blade FE: terima cart_json
         *    Konversi ke format "products" (id + quantity)
         */
        if ($request->has('cart_json') && !$request->has('products')) {
            $cart = json_decode($request->cart_json, true) ?? [];

            $products = [];
            foreach ($cart as $row) {
                if (!isset($row['id'])) continue;

                $products[] = [
                    'id'       => (int) $row['id'],
                    'quantity' => (int) ($row['quantity'] ?? 1),
                ];
            }

            $request->merge([
                'products' => $products,
            ]);
        }

        /**
         * 2. Validasi request
         */
        $validator = Validator::make(
            $request->all(),
            [
                'table'               => 'required',
                'customer_name'       => 'required|string|max:100',
                'customer_phone'      => 'required|string|max:20',
                'payment_method'      => 'required|string|max:50',

                'products'            => 'required|array|min:1',
                'products.*.id'       => 'required|integer|exists:products,id',
                'products.*.quantity' => 'required|integer|min:1',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors()
            ], 422);
        }

        /**
         * 3. Proses order & transaksi dalam DB Transaction
         */
        return DB::transaction(function () use ($request) {

            // 3.1 Buat order baru
            $order = Orders::create([
                'table'          => $request->table,
                'customer_name'  => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'items'          => [],
                'total'          => 0,
                'status'         => 'pending',
            ]);

            $itemsRequest = $request->products;
            $grandTotal   = 0;
            $itemsPayload = [];

            // Ambil produk dalam 1 query
            $productIds = collect($itemsRequest)->pluck('id')->all();
            $products   = Products::with('category')
                ->whereIn('id', $productIds)
                ->get()
                ->keyBy('id');

            // 3.2 Hitung total dan siapkan payload items
            foreach ($itemsRequest as $item) {
                $product   = $products[$item['id']];
                $qty       = (int) $item['quantity'];
                $lineTotal = $product->price * $qty;

                $grandTotal += $lineTotal;

                $itemsPayload[] = [
                    'product_id'    => $product->id,
                    'product_name'  => $product->name,
                    'category_id'   => $product->category_id,
                    'category_name' => optional($product->category)->name,
                    'quantity'      => $qty,
                    'price'         => $product->price,
                    'total'         => $lineTotal,
                ];
            }

            // 3.3 Update order
            $order->update([
                'items' => $itemsPayload,
                'total' => $grandTotal,
            ]);

            // 3.4 Buat transaksi
            $transaction = Transactions::create([
                'order_id'       => $order->id,
                'payment_method' => $request->payment_method,
                'total'          => $grandTotal,
            ]);

            $order->load(['transaction']);

            /**
             * 3.5 Response API murni JSON
             */
            return response()->json([
                'status'  => true,
                'message' => 'Order created successfully',
                'data'    => $order
            ], 201);
        });
    }
}
