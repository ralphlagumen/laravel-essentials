<?php

namespace Lagumen\LaravelEssential\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Lagumen\LaravelEssential\Concerns\CanPerformSearch;

class User extends Model
{
    use CanPerformSearch;

    /**
     * The attributes that are guarded.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
    ];

    protected $searchableColumns = [
        'name',
        'setting' => ['timezone'],
    ];

    public function setting()
    {
        return $this->hasOne(UserSetting::class, 'user_id');
    }
}
