<?php
namespace App\Models;
use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Course extends Model
{
    use HasFactory;
    use Searchable;
    protected $fillable = [
        'title',
        'fee',
        'duration',
        'has_discount',
        'discount',
        'teaching_method',
        'is_active',
        'category',
    ];
    protected $searchableFields = ['*'];

    public function teachingMethodLabel(): string
    {
        return match ($this->teaching_method) {
            'online' => 'Online',
            'both' => 'Both (Face to Face & Online)',
            default => 'Face to Face',
        };
    }
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
}
