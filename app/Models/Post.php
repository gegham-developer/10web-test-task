<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'scraped_post_id',
        'title',
        'author',
        'image',
        'excerpt',
        'scraped_date',
        'article_date'
    ];

    public function getImageUrlAttribute() {
        return public_path(config('global.posts.images_location_path') . ($this->image ?? 'noimage.png'));
    }
}
