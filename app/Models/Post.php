<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    //
    protected $table = 'posts';
    protected $fillable = ['postID', 'PostSaleID', 'Price', 'author_id', 'status', 'title', 'body'];
    protected $guarded = ['id'];

    public function reports()
    {
        return $this->morphMany(Report::class, 'target');
    }
}
