<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;



    public function userfrom()
    {
        return $this->belongsTo(User::class, 'from_id', 'id');
    }

    public function userto()
    {
        return $this->belongsTo(User::class, 'to_id', 'id');
    }


   
    
}
