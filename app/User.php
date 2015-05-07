<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['first_name', 'last_name', 'email', 'password'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * Many-to-many relationship with roles.
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany('App\Role');
    }

    /**
     * Set the password attribute only if present.
     *
     * @param string $value
     */
    public function setPasswordAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['password'] = $value;
        }
    }

    /**
     * Determines if the user can perform an action on a target.
     *
     * @param string        $action
     * @param string|object $target
     *
     * @return bool
     */
    public function can($action, $target)
    {
        Permission::load();

        $rules = Role::$rules;
        $object = null;

        if (is_object($target)) {
            $object = $target;
            $target = get_class($object);
        }

        foreach ($this->roles as $role) {
            if ($rule = $rules[$role->name][$target][$action]) {
                if (is_callable($rule)) {
                    $rule = call_user_func($rule, $this, $object);
                }

                return (bool) $rule;
            }
        }

        return false;
    }

    /**
     * Attach event handlers upon instantiation.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($user) {
            if ($user->isDirty('password')) {
                $user->password = bcrypt($user->password);
            }
        });
    }
}
