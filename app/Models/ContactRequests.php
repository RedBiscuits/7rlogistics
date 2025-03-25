<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContactRequests extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'phone', 'message', 'is_read'];
}
