<?php

namespace App\Http\Controllers\adminsite;

use App\Http\Controllers\Controller;
use App\Models\Orders;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Admin Transactions",
    description: "Manajemen transaksi pembayaran dan refund oleh admin"
)]
class TransactionsController extends Controller
{
    #[OA\Get(
        path: "/api/transactions",
        summary: "Ambil semua transaksi",
        description: "Mengambil seluruh data transaksi beserta relasi order",
        tags: ["Admin Transactions"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Berhasil mengambil daftar transaksi",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "success"),
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(type: "object")
                        ),
                    ]
                )
            )
        ]
    )]
    public function index()
    {
        $data = Transactions::with('order')->latest()->get();

        return response()->json([
            'status'  => true,
            'message' => 'success',
            'data'    => $data,
        ]);
    }

    #[OA\Get(
        path: "/api/transactions/{id}",
        summary: "Ambil detail transaksi",
        description: "Mengambil satu transaksi berdasarkan ID beserta order terkait",
        tags: ["Admin Transactions"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer"),
                description: "ID transaksi"
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Transaksi ditemukan"),
            new OA\Response(response: 404, description: "Transaksi tidak ditemukan")
        ]
    )]
    public function show($id)
    {
        $trx = Transactions::with('order')->find($id);

        if (!$trx) {
            return response()->json([
                'status'  => false,
                'message' => 'Transaction not found',
                'data'    => null
            ], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'success',
            'data'    => $trx
        ]);
    }

    #[OA\Post(
        path: "/api/transactions",
        summary: "Konfirmasi pembayaran / buat transaksi",
        description: "Membuat transaksi baru atau mengupdate transaksi lama menjadi paid, sekaligus mengubah order.payment_status menjadi paid",
        tags: ["Admin Transactions"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["order_id", "payment_method"],
                properties: [
                    new OA\Property(property: "order_id", type: "integer", example: 10),
                    new OA\Property(property: "payment_method", type: "string", example: "cash")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Pembayaran berhasil dikonfirmasi"),
            new OA\Response(response: 409, description: "Order sudah dibayar"),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
    public function store(Request $request)
    {
        $request->validate([
            'order_id'       => 'required|exists:orders,id',
            'payment_method' => 'required|string|max:50',
        ]);

        $order = Orders::findOrFail($request->order_id);
        $trx   = Transactions::where('order_id', $order->id)->first();

        // Jika sudah paid → tolak
        if ($trx && $trx->status === 'paid') {
            return response()->json([
                'status'  => false,
                'message' => 'Order already paid',
            ], 409);
        }

        // Buat atau update transaksi
        if (!$trx) {
            $trx = Transactions::create([
                'order_id'       => $order->id,
                'payment_method' => $request->payment_method,
                'total'          => $order->total,
                'status'         => 'paid',
                'payment_time'   => now(),
            ]);
        } else {
            $trx->update([
                'status'       => 'paid',
                'payment_time' => now(),
            ]);
        }

        // 🔴 INI YANG SEBELUMNYA HILANG
        $order->payment_status = 'paid';
        $order->save();

        return response()->json([
            'status'  => true,
            'message' => 'Payment confirmed',
            'data'    => [
                'transaction' => $trx,
                'order'       => $order,
            ],
        ]);
    }


    #[OA\Post(
        path: "/api/transactions/{id}/refund",
        summary: "Refund transaksi",
        description: "Mengubah status transaksi menjadi refunded dan membatalkan order terkait",
        tags: ["Admin Transactions"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer"),
                description: "ID transaksi"
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Refund berhasil"),
            new OA\Response(response: 404, description: "Transaksi tidak ditemukan"),
            new OA\Response(response: 400, description: "Transaksi tidak valid untuk refund")
        ]
    )]
    public function refund($id)
    {
        $trx = Transactions::findOrFail($id);

        if ($trx->status !== 'paid') {
            return response()->json([
                'status'  => false,
                'message' => 'Transaction already refunded or invalid',
            ], 400);
        }

        DB::transaction(function () use ($trx) {

            // Update transaksi
            $trx->update([
                'status' => 'refunded'
            ]);

            // Update order secara eksplisit by ID
            Orders::where('id', $trx->order_id)->update([
                'payment_status' => 'refunded',
                'status'         => 'cancelled',
            ]);
        });

        $trx->load('order');

        return response()->json([
            'status'  => true,
            'message' => 'Refund success',
            'data'    => [
                'transaction' => $trx,
                'order'       => $trx->order,
            ]
        ]);
    }
}
