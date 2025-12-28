<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ForumReply extends Model {
    protected $fillable = ['user_id', 'forum_id', 'content'];

    public function user() { return $this->belongsTo(User::class); }
}