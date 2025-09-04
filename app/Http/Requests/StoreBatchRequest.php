<?php

namespace App\Http\Requests;

use App\Rules\ProcessingOptionsMatchFiles;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * @mixin \App\Models\Batch
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

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'error'    => 'Validation failed',
            'messages' => $validator->errors(),
        ]));
    }
}
