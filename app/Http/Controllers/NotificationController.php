<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function read($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        
        $notification->markAsRead();

        return redirect($notification->data['url'] ?? route('admin.dashboard'));
    }
}
