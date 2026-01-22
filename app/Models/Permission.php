<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    use HasFactory;

    const CREATED_AT = null;
    const UPDATED_AT = null;
    protected $table = 'davinci_permissions';

    protected $fillable = [
        'name',
        'category'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'davinci_user_permissions');
    }
    public function role(){
        return $this->belongsToMany(Role::class);
    }
}
