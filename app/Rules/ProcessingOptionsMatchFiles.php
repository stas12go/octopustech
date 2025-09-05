<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ProcessingOptionsMatchFiles implements Rule
{
    public function __construct(protected int $filesCount)
    {
    }

    public function passes($attribute, $value): bool
    {
        if (is_string($value) && json_validate($value)) {
            $value = json_decode($value, true);
        }

        // Опции не должны быть пустыми и кол-во опций должно совпадать с кол-вом файлов.
        return !empty($value) && count($value) === $this->filesCount;
    }

    public function message(): string
    {
        return 'Количество файлов должно совпадать с количеством опций';
    }
}
