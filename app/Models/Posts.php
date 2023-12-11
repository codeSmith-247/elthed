<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Posts extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'post_id');
    }

    public function views(): HasMany
    {
        return $this->hasMany(PostView::class, 'post_id');
    }

    public function categories(): HasManyThrough
    {
        return $this->hasManyThrough(Category::class, PostCategory::class, 'post_id', 'id', 'id', 'category_id');
    }

}
