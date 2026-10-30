<?php
namespace App\Services;
use App\Models\{User,Notification};

class NotificationService
{
    public function envoyer(User $user, string $type, string $titre, string $message, array $data=[]): void
    {
        Notification::create(['user_id'=>$user->id,'type'=>$type,'titre'=>$titre,'message'=>$message,'data'=>$data]);
    }
}
