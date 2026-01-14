<?php

namespace App\Http\Controllers\adminsite;

use App\Http\Controllers\Controller;
use App\Models\Orders;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Admin Orders",
    description: "Manajemen pesanan untuk admin (list, detail, update status, update item, hapus)"
)]
class OrdersController extends Controller
{
    #[OA\Get(
        path: "/api/orders",
        summary: "Ambil semua pesanan",
        description: "Mengambil seluruh data pesanan beserta transaksi, diurutkan dari yang terbaru",
        tags: ["Admin Orders"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Berhasil mengambil daftar pesanan",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "success"),
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(type: "object")
                        )
                    ]
                )
            )
        ]
    )]
    public function index(): JsonResponse
    {
        $orders = Orders::with('transaction')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($order) {

                if ($order->transaction) {
                    if ($order->transaction->status === 'refunded') {
                        $order->payment_status = 'refunded';
                    } elseif ($order->transaction->status === 'paid') {
                        $order->payment_status = 'paid';
                    } else {
                        $order->payment_status = 'unpaid';
                    }
                } else {
                    $order->payment_status = 'unpaid';
                }

                return $order;
            });

        return response()->json([
            'status'  => true,
            'message' => 'success',
            'data'    => $orders,
        ]);
    }



    #[OA\Get(
        path: "/api/orders/{id}",
        summary: "Ambil detail pesanan",
        description: "Mengambil detail pesanan berdasarkan ID",
        tags: ["Admin Orders"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer"),
                description: "ID Order"
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Pesanan ditemukan",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "success"),
                        new OA\Property(property: "data", type: "object")
                    ]
                )
            ),
            new OA\Response(response: 404, description: "Order tidak ditemukan")
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $order = Orders::with('transaction')->find($id);

        if (! $order) {
            return response()->json([
                'status'  => false,
                'message' => 'Order not found',
                'data'    => null,
            ], 404);
        }

        if ($order->transaction) {
            if ($order->transaction->status === 'refunded') {
                $order->payment_status = 'refunded';
            } elseif ($order->transaction->status === 'paid') {
                $order->payment_status = 'paid';
            } else {
                $order->payment_status = 'unpaid';
            }
        } else {
            $order->payment_status = 'unpaid';
        }

        return response()->json([
            'status'  => true,
            'message' => 'success',
            'data'    => $order,
        ]);
    }



    #[OA\Patch(
        path: "/api/orders/{id}/status",
        summary: "Update status pesanan",
        description: "Mengubah status order (pending, processing, ready, completed, cancelled)",
        tags: ["Admin Orders"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["status"],
                properties: [
                    new OA\Property(
                        property: "status",
                        type: "string",
                        example: "processing",
                        enum: ["pending", "processing", "ready", "completed", "cancelled"]
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Status berhasil diperbarui"),
            new OA\Response(response: 422, description: "Validation error"),
            new OA\Response(response: 404, description: "Order tidak ditemukan")
        ]
    )]
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:pending,processing,ready,completed,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'validation error',
                'data'    => $validator->errors(),
            ], 422);
        }

        $order = Orders::find($id);

        if (! $order) {
            return response()->json([
                'status'  => false,
                'message' => 'Order not found',
                'data'    => null,
            ], 404);
        }

        $order->status = $request->input('status');
        $order->save();

        return response()->json([
            'status'  => true,
            'message' => 'Status updated successfully',
            'data'    => $order,
        ]);
    }

    #[OA\Patch(
        path: "/api/orders/{orderId}/item/{index}",
        summary: "Update status item dalam order",
        description: "Mengubah status satu item di dalam field JSON items",
        tags: ["Admin Orders"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "orderId",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer"),
                description: "ID Order"
            ),
            new OA\Parameter(
                name: "index",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer"),
                description: "Index item di array items"
            )
        ],
        requestBody: new OA\RequestBody(
            required: false,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: "status",
                        type: "string",
                        example: "completed",
                        description: "Status item (default: completed)"
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Status item berhasil diperbarui"),
            new OA\Response(response: 404, description: "Order atau item tidak ditemukan")
        ]
    )]
    public function updateItemStatus(Request $request, int $orderId, int $index): JsonResponse
    {
        $order = Orders::find($orderId);

        if (! $order) {
            return response()->json([
                'status'  => false,
                'message' => 'Order not found',
            ], 404);
        }

        $rawItems = $order->items;

        if (is_array($rawItems)) {
            $items = $rawItems;
        } elseif (is_string($rawItems) && $rawItems !== '') {
            $items = json_decode($rawItems, true);
            if (! is_array($items)) {
                $items = [];
            }
        } else {
            $items = [];
        }

        if (! isset($items[$index])) {
            return response()->json([
                'status'  => false,
                'message' => 'Item tidak ditemukan.',
            ], 404);
        }

        $newStatus = $request->input('status', 'completed');
        $items[$index]['status'] = $newStatus;

        $allDone = collect($items)->every(
            fn(array $i) => ($i['status'] ?? 'pending') === 'completed'
        );

        $order->status = $allDone ? 'completed' : 'processing';
        $order->items  = $items;
        $order->save();

        return response()->json([
            'status'  => true,
            'message' => 'Status item berhasil diperbarui',
            'data'    => $order,
        ]);
    }

    #[OA\Delete(
        path: "/api/orders/{id}",
        summary: "Hapus pesanan",
        description: "Menghapus pesanan yang belum completed",
        tags: ["Admin Orders"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Pesanan berhasil dihapus"),
            new OA\Response(response: 403, description: "Pesanan yang sudah selesai tidak boleh dihapus"),
            new OA\Response(response: 404, description: "Order tidak ditemukan")
        ]
    )]
    public function destroy(int $id): JsonResponse
    {
        $order = Orders::find($id);

        if (! $order) {
            return response()->json([
                'status'  => false,
                'message' => 'Order not found',
                'data'    => null,
            ], 404);
        }

        if (in_array($order->status, ['completed'])) {
            return response()->json([
                'status'  => false,
                'message' => 'Pesanan yang sudah selesai tidak bisa dihapus',
            ], 403);
        }

        $order->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Pesanan berhasil dihapus',
            'data'    => null,
        ]);
    }
}
