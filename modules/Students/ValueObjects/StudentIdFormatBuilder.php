<?php

namespace Modules\Students\ValueObjects;

final class StudentIdFormatBuilder
{
    private array $elements = [];
    private string $separator = '-';

    public static function create(): self
    {
        return new self();
    }

    public function withElements(array $elements): self
    {
        $validElements = ['filiere', 'YYYY', 'YY', 'MM', 'DD', '####', '###', '##', '#'];
        foreach ($elements as $element) {
            if (!in_array($element, $validElements)) {
                throw new \InvalidArgumentException("Invalid element: {$element}");
            }
        }
        $this->elements = $elements;
        return $this;
    }

    public function withSeparator(string $separator): self
    {
        $this->separator = $separator;
        return $this;
    }

    public function build(): string
    {
        if (empty($this->elements)) {
            throw new \InvalidArgumentException("At least one element must be specified");
        }

        return '{' . implode('}' . $this->separator . '{', $this->elements) . '}';
    }

    public static function fromConfiguration(array $config): string
    {
        $elements = $config['elements'] ?? ['filiere', 'YYYY', '####'];
        $separator = $config['separator'] ?? '-';

        return self::create()
            ->withElements($elements)
            ->withSeparator($separator)
            ->build();
    }
}
