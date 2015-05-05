<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PendingUpdate extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['model', 'id', 'update', 'expires_at'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['expires_at'];

    /**
     * Serialize the update attribute.
     *
     * @param array $value
     */
    public function setUpdateAttribute(array $value)
    {
        $this->attributes['update'] = serialize($value);
    }

    /**
     * Deserialize the update attribute.
     *
     * @return array
     */
    public function getUpdateAttribute()
    {
        return unserialize($this->attributes['update']);
    }

    /**
     * Apply the pending updates.
     *
     * @param string $token
     * @param array  $update Override the update in storage
     *
     * @return bool
     */
    public static function apply($token, array $update = [])
    {
        if ($pending = static::where('token', $token)->where('expires_at', '>', Carbon::now())->first()) {
            $model = call_user_func([$pending->model, 'find'], $pending->id);
            $model->fill(empty($update) ? $pending->update : $update);

            $result = $model->save();
            $pending->delete();

            return $result;
        }

        return false;
    }

    /**
     * Attach event handlers upon instantiation.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pending) {
            $pending->token = base_convert(bin2hex(openssl_random_pseudo_bytes(8)), 16, 36);

            if (!$pending->expires_at) {
                $pending->expires_at = Carbon::now()->addDay();
            }
        });
    }
}
