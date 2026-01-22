<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'system_name',
        'system_logo',
        'allow_dark_sidebar',
        'allow_message',
        'allow_email',
        'enable_upload_receipt',
        'mail_mailer',
        'mail_host',
        'mail_port',
        'mail_username',
        'mail_password',
        'mail_encryption',
        'mail_from_address',
        'site_description',
        'site_contact_email',
        'site_contact_number',
    ];

    protected $searchableFields = ['*'];

    protected $casts = [
        'allow_dark_sidebar' => 'boolean',
        'allow_message' => 'boolean',
        'allow_email' => 'boolean',
        'enable_upload_receipt' => 'boolean',
    ];
}
