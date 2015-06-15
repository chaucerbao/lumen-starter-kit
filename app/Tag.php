<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['slug', 'name'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The validation rules for this model.
     *
     * @var array
     */
    public static $rules = [
        'slug' => 'required|alpha_dash',
        'name' => 'required',
    ];

    /**
     * Many-to-many relationship with posts.
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function posts()
    {
        return $this->morphedByMany('App\Post', 'taggable');
    }

    /**
     * Attach event handlers upon instantiation.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($tag) {
            if (empty($tag->slug)) {
                $tag->slug = str_slug($tag->name, '-');
            }
        });
    }
}
