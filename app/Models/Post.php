<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','title','content'];
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function image()
    {
        return $this->hasOne(PostImage::class);
    }


    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($post) {
            $post->image()->delete();
        });
    }
}
