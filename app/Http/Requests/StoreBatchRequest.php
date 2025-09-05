<?php

namespace App\Http\Requests;

use App\Models\Batch;
use App\Models\File;
use App\Rules\ProcessingOptionsMatchFiles;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * @mixin Batch
 * @mixin File
 */
class StoreBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'files'                          => ['required', 'array', 'min:1', 'max:20'],
            'files.*'                        => ['required', 'file', 'max:15360', 'mimes:jpg,jpeg,png,bmp,webp', 'mimetypes:image/jpeg,image/png,image/bmp,image/webp'],
            'processing_options'             => ['sometimes', 'array', new ProcessingOptionsMatchFiles(count($this->file('files', [])))],
            'processing_options.*'           => ['sometimes', 'array'],
            'processing_options.*.operation' => ['sometimes', 'string', 'in:crop,resize,normalize'],
            'processing_options.*.width'     => ['sometimes', 'integer', 'min:1',/*'max:?*/],
            'processing_options.*.height'    => ['sometimes', 'integer', 'min:1',/*'max:?*/],
            'processing_options.*.quality'   => ['sometimes', 'integer', 'min:1', 'max:100'],
            'processing_options.*.crop'      => ['sometimes', 'boolean'],
        ];
    }

    public function messages()
    {
        return [
            'files.required' => 'Файлы обязательны к загрузке',
            'files.array'    => 'Поле :attribute должно быть массивом файлов',
            'files.min'      => 'Количество загружаемых файлов должно быть в пределах от 1 до 20',
            'files.max'      => 'Количество загружаемых файлов должно быть в пределах от 1 до 20',

            'files.*.required'  => 'Файлы обязательны к загрузке',
            'files.*.file'      => 'Поле files должно быть массивом файлов',
            'files.*.max'       => 'Размер файла не может превышать 15 Мб',
            'files.*.mimes'     => 'Некорректный mime-тип файла',
            'files.*.mimetypes' => 'Некорректный mime-тип файла',

            'processing_options.array' => 'Поле :attribute должно быть списком массивов опций',

            'processing_options.*.array' => 'Поле :attribute должно быть массивом опций',

            'processing_options.*.operation.string' => 'Тип операции должен быть строкой',
            'processing_options.*.operation.in'     => 'Некорректное значение для типа операции. Возможные типы: crop, resize, normalize.',
            'processing_options.*.width.integer'    => 'Ширина должна быть целым числом',
            'processing_options.*.width.min'        => 'Минимальное значение ширины - 1',
            'processing_options.*.height.integer'   => 'Высота должна быть целым числом',
            'processing_options.*.height.min'       => 'Минимальное значение высоты - 1',
            'processing_options.*.quality.integer'          => 'Качество должно быть целым числом',
            'processing_options.*.quality.min'          => 'Минимальное значение качества - 1',
            'processing_options.*.quality.max'          => 'Максимальное значением качества - 100',
            'processing_options.*.crop'             => 'Поле :attribute должно быть булевым значением',
        ];
    }

    protected function prepareForValidation()
    {
        // Если processing_options переданы как JSON строка - декодируем
        if ($this->has('processing_options') && is_string($this->processing_options)) {
            $options = json_decode($this->processing_options, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($options)) {
                $this->merge(['processing_options' => $options]);
            }
        }
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'error'    => 'Ошибка валидации',
            'messages' => $validator->errors(),
        ]));
    }
}
