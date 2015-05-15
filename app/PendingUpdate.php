<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PendingUpdate extends Model
{
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'token';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['model', 'update', 'expires_at'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['expires_at'];

    /**
     * Serialize the model attribute.
     *
     * @param Model $model
     */
    public function setModelAttribute(Model $model)
    {
        $this->attributes['model'] = serialize([
            get_class($model),
            $model->id,
        ]);
    }

    /**
     * Deserialize the model attribute.
     *
     * @return Model
     */
    public function getModelAttribute()
    {
        list($class, $id) = unserialize($this->attributes['model']);

        return call_user_func([$class, 'find'], $id);
    }

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
        if ($pending = static::where('expires_at', '>', Carbon::now())->find($token)) {
            $model = $pending->model;
            $model->forceFill(empty($update) ? $pending->update : $update);

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
