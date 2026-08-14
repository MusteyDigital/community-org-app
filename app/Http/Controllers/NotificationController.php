<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class NotificationController extends Controller
{
    public function markRead(Request $request, string $id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();
        abort_unless($notification, 404);
        $notification->markAsRead();
        $url = $notification->data['url'] ?? null;
        return $url ? redirect($url) : back();
    }
    public function markAllRead(Request $request)
    {
        Auth::user()->unreadNotifications->markAsRead();
        return back();
    }
}