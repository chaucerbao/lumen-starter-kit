<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The rules to determine a role's ability to perform an action.
     *
     * @var array
     */
    public static $rules = [];

    /**
     * Many-to-many relationship with users.
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany('App\User');
    }

    /**
     * Create a permission rule.
     *
     * @param string        $role
     * @param string        $action
     * @param string        $target
     * @param bool|callable $allowed
     */
    public static function allow($role, $action, $target, $allowed = true)
    {
        static::$rules[$role][$target][$action] = $allowed;
    }
}
