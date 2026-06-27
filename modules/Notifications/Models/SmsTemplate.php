<?php

namespace Modules\Notifications\Models;

use Illuminate\Database\Eloquent\Model;

class SmsTemplate extends Model
{
    protected $fillable = [
        'code',
        'name',
        'content',
        'variables',
        'is_active',
        'max_length',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'variables' => 'array',
            'max_length' => 'integer',
        ];
    }

    public function render(array $data = []): string
    {
        $content = $this->content;
        foreach ($data as $key => $value) {
            $content = str_replace("{{$key}}", $value, $content);
        }
        return $content;
    }

    public function isActive(): bool
    {
        return $this->is_active === true;
    }

    public function getCharacterCount(array $data = []): int
    {
        return strlen($this->render($data));
    }

    public function isWithinLength(array $data = []): bool
    {
        return $this->getCharacterCount($data) <= ($this->max_length ?? 160);
    }
}
