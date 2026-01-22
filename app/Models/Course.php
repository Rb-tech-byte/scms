<?php
namespace App\Models;
use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Course extends Model
{
    use HasFactory;
    use Searchable;
    protected $fillable = ['title', 'fee', 'duration', 'has_discount', 'discount'];
    protected $searchableFields = ['*'];
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
}
