<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Reusable notification message templates supporting placeholder substitution.
 */
class NotificationTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'channel',
        'subject',
        'body',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Render template body with placeholder map.
     *
     * @param array<string, string|int|float> $variables
     */
    public function render(array $variables = []): string
    {
        $content = $this->body;
        foreach ($variables as $key => $value) {
            $content = str_replace('{' . $key . '}', (string) $value, $content);
        }
        return $content;
    }
}

