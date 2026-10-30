<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
       public function index()
    {
        $notifications = Notification::where('user_id',auth()->id())
            ->orderByDesc('created_at')->paginate(20);
        // Marquer tout comme lu à l'ouverture
        Notification::where('user_id',auth()->id())->where('lu',false)->update(['lu'=>true,'etat'=>'lu']);
        return view('notifications.index',compact('notifications'));
    }

    public function marquerLue(Notification $notification)
    {
        abort_unless($notification->user_id === auth()->id(),403);
        $notification->marquerLue();
        return back();
    }

    public function marquerToutLu()
    {
        Notification::where('user_id',auth()->id())->where('lu',false)
            ->update(['lu'=>true,'etat'=>'lu']);
        return back()->with('success','Toutes les notifications marquées comme lues.');
    }

}
