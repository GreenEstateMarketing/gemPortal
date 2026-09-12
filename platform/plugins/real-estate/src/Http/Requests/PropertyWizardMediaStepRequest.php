<?php

namespace Botble\RealEstate\Http\Requests;

use Botble\RealEstate\Http\Requests\Rules\ValidImageCount;
use Botble\RealEstate\Models\CategoryDocument;
use Botble\Support\Http\Requests\Request;

class PropertyWizardMediaStepRequest extends Request
{
    public function rules(): array
    {
        return [
            'images' => ['required', 'array', new ValidImageCount(1, 20)],
            'documents' => 'nullable|array',
            'documents.*.document_id' => 'nullable|integer',
            'documents.*.url' => 'nullable|string',
            'auto_renew' => 'nullable|boolean',
            'never_expired' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'moderation_status' => 'nullable|string',
        ];
    }

    /**
     * Which document types are required is dynamic - it depends on the
     * category the property was given in step 1 (admin-configured under
     * Real Estate > Category Documents) - so this can't be a static rule.
     * Each missing one is reported under its own `document_{id}` key so
     * the matching upload slot in media.blade.php can show its own error,
     * the same way every other per-field error already works.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            /** @var \Botble\RealEstate\Models\Property|null $property */
            $property = $this->route('property');

            if (!$property || !$property->category_id) {
                return;
            }

            $required = CategoryDocument::with('document')
                ->where('category_id', $property->category_id)
                ->where('required', true)
                ->get();

            if ($required->isEmpty()) {
                return;
            }

            $submittedDocumentIds = collect($this->input('documents', []))
                ->pluck('document_id')
                ->filter()
                ->map(fn ($id) => (int) $id)
                ->all();

            foreach ($required as $requirement) {
                if (!in_array((int) $requirement->document_id, $submittedDocumentIds, true)) {
                    $validator->errors()->add(
                        'document_' . $requirement->document_id,
                        sprintf('%s is required.', optional($requirement->document)->name ?: 'This document')
                    );
                }
            }
        });
    }
}
