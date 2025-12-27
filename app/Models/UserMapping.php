<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMapping extends Model
{
    use HasFactory;

    protected $table = 'user_mappings';
    protected $primaryKey = 'local_id';

    protected $fillable = [
        'external_uid',
        'email',
        'full_name',
        'role',
        'last_synced'
    ];

    protected $casts = [
        'last_synced' => 'datetime',
    ];
}
