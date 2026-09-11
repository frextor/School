<?php

namespace App\Http\Controllers;

use App\Models\AdminNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Portage de `Notifications.php` (CodeIgniter).
 *
 * NON couvert par ce portage : le canal Pusher temps réel
 * (`notifications_library`, `my_miracle_channels`) — cela suppose de
 * décider d'une solution de websockets/broadcast côté Laravel (Reverb,
 * Pusher, Ably...) ce qui dépasse le cadre d'un simple portage de route.
 * Les endpoints CRUD (liste, marquer comme lu) sont en revanche portés.
 */
class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $limit = (int) $request->input('limit', 20);
        $offset = (int) $request->input('offset', 0);

        $notifications = AdminNotification::query()
            ->where('id_admin', Auth::guard('admin')->id())
            ->orderByDesc('id_notification')
            ->skip($offset)
            ->take($limit)
            ->get()
            ->map(function (AdminNotification $notification) {
                return [
                    ...$notification->toArray(),
                    'date_format' => $notification->date?->diffForHumans(),
                ];
            });

        return $this->response('Notifications', 'OK', $notifications);
    }

    public function markOneRead(Request $request): JsonResponse
    {
        $ids = $request->filled('id_notification')
            ? [$request->integer('id_notification')]
            : array_filter(explode(',', (string) $request->input('ids_notification')));

        AdminNotification::whereIn('id_notification', $ids)
            ->where('id_admin', Auth::guard('admin')->id())
            ->update(['lue' => true]);

        return $this->response('Mark one read', 'OK', ['ids' => $ids]);
    }

    public function markAllRead(): JsonResponse
    {
        AdminNotification::where('id_admin', Auth::guard('admin')->id())
            ->update(['lue' => true]);

        return $this->response('Mark all read', 'OK');
    }

    private function response(string $message, string $status, mixed $data = []): JsonResponse
    {
        return response()->json(['status' => $status, 'message' => $message, 'data' => $data]);
    }
}
