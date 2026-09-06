@php
    $p = $property;
    $imageItems = collect($p->images)->map(function ($url) {
        return ['url' => $url, 'name' => basename($url)];
    })->values()->all();
    $documentItems = json_decode($p->documents ?: '[]', true) ?: [];
    if (! empty($documentItems) && ! isset($documentItems[0]['url']) && isset($documentItems[0]['path'])) {
        // legacy shape from before this wizard - not carried over automatically
        $documentItems = [];
    }
@endphp

<div class="wizard-panel">
    <h2 class="wizard-panel__heading">{{ __('Media & Documents') }}</h2>
    <p class="wizard-panel__description">{{ __('Add photos of the property and any supporting documents.') }}</p>

    <form data-step-form action="{{ $stepUrls['media'] }}" method="post">
        <div class="wizard-field wizard-field--span2">
            <label>{{ __('Photos') }}</label>
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

        <div class="wizard-field wizard-field--span2" style="margin-top:28px;">
            <label>{{ __('Documents') }} <span class="wizard-hint">({{ __('optional') }})</span></label>
            <div data-uploader="documents">
                <div class="wizard-upload">
                    <i class="fas fa-file-upload"></i>
                    {{ __('Click or drag ownership documents here to upload') }}
                    <input type="file" multiple>
                </div>
                <input type="hidden" data-uploader-value="documents" value="{{ json_encode($documentItems) }}">
                <div class="wizard-thumbs" data-uploader-thumbs></div>
            </div>
        </div>

        <div class="wizard-field-grid" style="margin-top:28px;">
            <label class="wizard-checkbox">
                <input type="checkbox" data-field="auto_renew" {{ ($p->auto_renew) ? 'checked' : '' }}>
                {{ __('Auto-renew this listing when it expires') }}
            </label>
            <label class="wizard-checkbox">
                <input type="checkbox" data-field="never_expired" {{ ($p->never_expired) ? 'checked' : '' }}>
                {{ __('This listing never expires') }}
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
                    <select class="wizard-select" data-field="moderation_status">
                        <option value="pending" {{ ($p->moderation_status == 'pending') ? 'selected' : '' }}>{{ __('Pending') }}</option>
                        <option value="approved" {{ ($p->moderation_status == 'approved') ? 'selected' : '' }}>{{ __('Approved') }}</option>
                        <option value="rejected" {{ ($p->moderation_status == 'rejected') ? 'selected' : '' }}>{{ __('Rejected') }}</option>
                        <option value="closed" {{ ($p->moderation_status == 'closed') ? 'selected' : '' }}>{{ __('Closed') }}</option>
                    </select>
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
