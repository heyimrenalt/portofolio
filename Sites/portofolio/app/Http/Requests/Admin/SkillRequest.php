<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SkillRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'skill_category_id' => ['required', 'exists:skill_categories,id'],
            'name'              => ['required', 'string', 'max:255'],
            'icon_source'       => ['required', 'in:url,upload,none'],
            'icon_url'          => ['nullable', 'string', 'max:500'],
            'icon_upload'       => ['nullable', 'image', 'max:10240'],
            'order'             => ['required', 'integer', 'min:0'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $source = $this->input('icon_source');

            if ($source === 'url' && empty(trim((string) $this->input('icon_url')))) {
                $validator->errors()->add('icon_url', 'Icon URL is required when source is URL.');
            }

            if ($source === 'upload' && !$this->hasFile('icon_upload')) {
                // On edit, existing upload is acceptable — check via route model binding.
                $skill = $this->route('skill');
                if (!$skill || !$skill->icon_upload) {
                    $validator->errors()->add('icon_upload', 'Please upload an icon file when source is Upload.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'icon_source.required' => 'Please select an icon source.',
            'icon_source.in'       => 'Icon source must be url, upload, or none.',
            'icon_upload.max'      => 'Icon image size must not exceed 10MB. Supported formats: jpg, jpeg, png, webp.',
            'icon_upload.image'    => 'The icon must be a valid image (jpg, jpeg, png, webp).',
            'icon_upload.uploaded' => 'Icon upload failed. The file may exceed the server limit (12MB). Please use an image under 10MB.',
        ];
    }
}
