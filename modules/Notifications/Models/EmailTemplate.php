<?php

namespace Modules\Notifications\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = [
        'code',
        'name',
        'subject',
        'body',
        'variables',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'variables' => 'array',
        ];
    }

    public function render(array $data = []): string
    {
        $body = $this->body;
        foreach ($data as $key => $value) {
            $body = str_replace("{{$key}}", $value, $body);
        }
        return $body;
    }

    public function renderSubject(array $data = []): string
    {
        $subject = $this->subject;
        foreach ($data as $key => $value) {
            $subject = str_replace("{{$key}}", $value, $subject);
        }
        return $subject;
    }

    public function isActive(): bool
    {
        return $this->is_active === true;
    }
}
