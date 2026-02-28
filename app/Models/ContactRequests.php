<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactRequests extends Model
{
    use HasFactory, \App\Models\Traits\RecordsModelChangeNotifications;

    protected $fillable = ['name', 'email', 'phone', 'message', 'is_read'];
}
