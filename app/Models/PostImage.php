<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class PostImage extends Model
{
    use HasFactory;
    protected $fillable = ['post_id','image_path'];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }


    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($postImage) {
            $imagePath = public_path($postImage->image_path);
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
        });
    }

}
