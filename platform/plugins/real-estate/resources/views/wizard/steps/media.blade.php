@php
    $p = $property;
    $imageItems = collect($p->images)->map(function ($image) {
        // $p->images is normally a flat array of relative storage paths
        // (matching every other place in the app that reads it - property
        // detail galleries, PropertyResource, etc.) but tolerate an object
        // shape here defensively in case it was ever saved differently.
        $url = is_string($image) ? $image : (is_array($image) ? ($image['url'] ?? '') : (is_object($image) ? ($image->url ?? '') : ''));

        return $url === '' ? null : [
            'url' => $url,
            'full_url' => RvMedia::getImageUrl($url),
            'name' => basename($url),
        ];
    })->filter()->values()->all();
    $documentItems = json_decode($p->documents ?: '[]', true) ?: [];
    if (! empty($documentItems) && ! isset($documentItems[0]['url']) && isset($documentItems[0]['path'])) {
        // legacy shape from before this wizard - not carried over automatically
        $documentItems = [];
    }
    $documentItemsByType = collect($documentItems)->groupBy('document_id');
@endphp

<div class="wizard-panel">
    <h2 class="wizard-panel__heading">{{ __('Media & Documents') }}</h2>
    <p class="wizard-panel__description">{{ __('Add photos of the property and any supporting documents.') }}</p>

    <form data-step-form action="{{ $stepUrls['media'] }}" method="post">
        <div class="wizard-field wizard-field--span2">
            <label>{{ __('Photos') }}</label>
            <p class="wizard-hint" style="margin-bottom:10px;">{{ __('Add between 1 and 20 photos of the property.') }} <span data-uploader-count="images"></span></p>
            <div data-uploader="images">
                <div class="wizard-upload">
                    <i class="fas fa-cloud-upload-alt"></i>
                    {{ __('Click or drag photos here to upload') }}
                    <input type="file" accept="image/*" multiple>
                </div>
                <input type="hidden" data-uploader-value="images" value="{{ json_encode($imageItems) }}">
                <div class="wizard-thumbs" data-uploader-thumbs></div>
            </div>
            <div class="wizard-error" data-error-for="images"></div>
        </div>

        @if ($categoryDocuments->isNotEmpty())
            <h3 style="margin-top:32px;margin-bottom:6px;font-size:16px;">{{ __('Documents') }}</h3>
            <p class="wizard-hint" style="margin-bottom:14px;">{{ __('Documents required for this property type.') }}</p>
            @foreach ($categoryDocuments as $categoryDocument)
                @php
                    $document = $categoryDocument->document;
                    $accept = collect(explode(',', $document->type ?? ''))->filter()->implode(',');
                    $slotItems = $documentItemsByType->get($document->id, collect())->values()->all();
                @endphp
                <div class="wizard-field wizard-field--span2" style="margin-top:20px;">
                    <label>
                        {{ $document->name }}
                        <span class="wizard-hint">({{ $categoryDocument->required ? __('required') : __('optional') }})</span>
                    </label>
                    <div data-uploader="documents_{{ $document->id }}">
                        <div class="wizard-upload">
                            <i class="fas fa-file-upload"></i>
                            {{ __('Click or drag file here to upload') }}
                            @if ($accept)
                                <span class="wizard-hint">({{ $accept }})</span>
                            @endif
                            <input type="file" accept="{{ $accept }}">
                        </div>
                        <input type="hidden" data-uploader-value="documents_{{ $document->id }}" data-document-id="{{ $document->id }}" data-document-required="{{ $categoryDocument->required ? '1' : '0' }}" value="{{ json_encode($slotItems) }}">
                        <div class="wizard-thumbs" data-uploader-thumbs></div>
                    </div>
                    <div class="wizard-error" data-error-for="document_{{ $document->id }}"></div>
                </div>
            @endforeach
        @else
            <div class="wizard-field wizard-field--span2" style="margin-top:28px;">
                <label>{{ __('Documents') }} <span class="wizard-hint">({{ __('optional') }})</span></label>
                <div data-uploader="documents">
                    <div class="wizard-upload">
                        <i class="fas fa-file-upload"></i>
                        {{ __('Click or drag ownership documents here to upload') }}
                        <input type="file">
                    </div>
                    <input type="hidden" data-uploader-value="documents" value="{{ json_encode($documentItems) }}">
                    <div class="wizard-thumbs" data-uploader-thumbs></div>
                </div>
            </div>
        @endif

        <div class="wizard-field-grid" style="margin-top:28px;">
            <label class="wizard-checkbox">
                <input type="checkbox" data-field="auto_renew" {{ ($p->auto_renew) ? 'checked' : '' }}>
                {{ __('Auto-renew this listing when it expires') }}
            </label>
            @if ($wizardContext['can']['setFeatured'])
                <label class="wizard-checkbox">
                    <input type="checkbox" data-field="is_featured" {{ ($p->is_featured) ? 'checked' : '' }}>
                    {{ __('Feature this listing') }}
                </label>
            @endif

            @if ($wizardContext['can']['setModerationStatus'])
                <div class="wizard-field">
                    <label>{{ __('Moderation Status') }}</label>
                    {{-- Approved is intentionally left out for now - it'll be
                         set automatically at a later stage of the overall
                         listing journey, not chosen manually here. --}}
                    <select class="wizard-select" data-field="moderation_status" id="wizard-moderation-status">
                        <option value="pending" {{ ($p->moderation_status == 'pending') ? 'selected' : '' }}>{{ __('Pending') }}</option>
                        <option value="rejected" {{ ($p->moderation_status == 'rejected') ? 'selected' : '' }}>{{ __('Rejected') }}</option>
                        <option value="closed" {{ ($p->moderation_status == 'closed') ? 'selected' : '' }}>{{ __('Closed') }}</option>
                    </select>
                </div>

                <div class="wizard-field wizard-field--span2" data-reject-reason-field style="{{ $p->moderation_status == 'rejected' ? '' : 'display:none;' }}">
                    <label>{{ __('Rejection Reason') }}</label>
                    <textarea class="wizard-textarea" data-field="reject_reason" placeholder="{{ __('Explain why this listing is being rejected...') }}">{{ $p->reject_reason }}</textarea>
                    <div class="wizard-error" data-error-for="reject_reason"></div>
                </div>
            @endif
        </div>

        <div class="wizard-panel__actions">
            <a href="{{ $showBaseUrl }}?step=2" class="wizard-btn wizard-btn--ghost"><i class="fas fa-arrow-left"></i> {{ __('Back') }}</a>
            <button type="submit" class="wizard-btn wizard-btn--primary" data-step-submit data-loading-text="{{ __('Saving...') }}">
                {{ __('Save & Continue') }} <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </form>
</div>
