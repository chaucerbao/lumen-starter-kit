<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['author_id', 'slug', 'title', 'body', 'is_active', 'published_at'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['published_at'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * The validation rules for this model.
     *
     * @var array
     */
    public static $rules = [
        'author_id' => 'required|exists:users,id',
        'slug' => 'required|alpha_dash',
        'title' => 'required',
        'body' => 'required',
        'is_active' => 'boolean',
        'published_at' => 'required|date',
    ];

    /**
     * Belongs-to relationship with a user.
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author()
    {
        return $this->belongsTo('App\User', 'author_id');
    }

    /**
     * Many-to-many relationship with tags.
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function tags()
    {
        return $this->morphToMany('App\Tag', 'taggable');
    }

    /**
     * Scope for posts that are active and published before now.
     *
     * @return Builder
     */
    public function scopePublished(Builder $query)
    {
        return $query->where('is_active', true)->where('published_at', '<', Carbon::now());
    }

    /**
     * Scope for posts that are not active.
     *
     * @return Builder
     */
    public function scopeDrafts(Builder $query)
    {
        return $query->where('is_active', false);
    }
}
