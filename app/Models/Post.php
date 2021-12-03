<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    const DATE_FORMAT = 'Y-m-d H:i:s';

    protected $fillable = [
        'scraped_post_id',
        'title',
        'author',
        'image',
        'excerpt',
        'scraped_date',
        'article_date'
    ];

    public $timestamps = false;

    protected $casts = [
        'scraped_date' => 'date:' . self::DATE_FORMAT,
        'article_date' => 'date:' . self::DATE_FORMAT
    ];

    public static function getTableName() {
        return app(self::class)->getTable();
    }
}
