<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Country extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['iso', 'name', 'nicename', 'iso3'];

    protected $searchableFields = ['*'];

    public function applicants()
    {
        return $this->hasMany(Applicant::class);
    }
}
