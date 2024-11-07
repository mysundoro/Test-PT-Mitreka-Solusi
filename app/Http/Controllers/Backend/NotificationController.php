<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Notification};
use Auth;

class NotificationController extends Controller
{
    public function getNotification()
    {
        return Notification::where('users_id', Auth::user()->id)->orderBy('created_at', 'desc')->get();
    }

    public function markAsRead()
    {
        $data = Notification::where('users_id', Auth::user()->id);
        $data->update(['is_read' => true]);

        return redirect()->back()->with('message', 'Notification is marked as read successfully.');
    }

    public function clear()
    {
        $data = Notification::where('users_id', Auth::user()->id)->delete();

        return redirect()->back()->with('message', 'Notification successfully deleted from the system.');
    }
}