@php
    $p = $property;
    $currency = $p->currency;
    $priceLabel = $p->price ? number_format($p->price, 0) . ($currency && $currency->symbol ? ' ' . $currency->symbol : '') : '-';
    $imageItems = collect($p->images)->values()->all();
    $documentItems = json_decode($p->documents ?: '[]', true) ?: [];
    $documentNameById = $categoryDocuments->pluck('document.name', 'document_id');
@endphp

<div class="wizard-document">
    @if ($verifiedByAgent)
        <div class="wizard-stamp wizard-stamp--agent">{{ __('Verified by Agent') }}</div>
    @endif
    @if ($verifiedByAdmin)
        <div class="wizard-stamp wizard-stamp--admin">{{ __('Verified by GEM') }}</div>
    @endif

    <div class="wizard-document__header">
        <h2 class="wizard-document__title">{{ $p->name }}</h2>
        <p class="wizard-document__subtitle">{{ __('Property') }} #{{ $p->id }}</p>
    </div>

    <div class="wizard-review-section">
        <div class="wizard-review-section__head">
            <h3>{{ __('Basics & Price') }}</h3>
        </div>
        <dl class="wizard-review-grid">
            <div class="wizard-review-item"><dt>{{ __('Title') }}</dt><dd>{{ $p->name }}</dd></div>
            <div class="wizard-review-item"><dt>{{ __('Listing Type') }}</dt><dd>{{ $p->type == 'rent' ? __('For Rent') : __('For Sale') }}</dd></div>
            <div class="wizard-review-item"><dt>{{ __('Category') }}</dt><dd>{{ optional($p->category)->name ?: '-' }}</dd></div>
            <div class="wizard-review-item"><dt>{{ __('Price') }}</dt><dd>{{ $priceLabel }} {{ $p->price_unit }}</dd></div>
            <div class="wizard-review-item"><dt>{{ __('Area') }}</dt><dd>{{ $p->square_text }}</dd></div>
            <div class="wizard-review-item"><dt>{{ __('Bed / Bath') }}</dt><dd>{{ $p->number_bedroom ?: 0 }} / {{ $p->number_bathroom ?: 0 }}</dd></div>
        </dl>
    </div>

    <div class="wizard-review-section">
        <div class="wizard-review-section__head">
            <h3>{{ __('Location & Details') }}</h3>
        </div>
        <dl class="wizard-review-grid">
            <div class="wizard-review-item wizard-field--span2"><dt>{{ __('Address') }}</dt><dd>{{ $p->location }}</dd></div>
            <div class="wizard-review-item"><dt>{{ __('City') }}</dt><dd>{{ optional($p->city)->name ?: '-' }}</dd></div>
            <div class="wizard-review-item"><dt>{{ __('City Area') }}</dt><dd>{{ optional($p->cityArea)->city_area_name ?: '-' }}</dd></div>
            <div class="wizard-review-item"><dt>{{ __('Features') }}</dt><dd>{{ $p->features->pluck('name')->join(', ') ?: '-' }}</dd></div>
        </dl>
    </div>

    @if ($p->description)
        <div class="wizard-review-section">
            <div class="wizard-review-section__head">
                <h3>{{ __('Description') }}</h3>
            </div>
            <p class="wizard-document__description">{{ strip_tags($p->description) }}</p>
        </div>
    @endif

    <div class="wizard-review-section">
        <div class="wizard-review-section__head">
            <h3>{{ __('Media & Documents') }}</h3>
        </div>
        <div class="wizard-thumbs">
            @forelse ($imageItems as $image)
                <div class="wizard-thumb"><img src="{{ RvMedia::getImageUrl($image) }}" alt=""></div>
            @empty
                <p class="wizard-hint">{{ __('No photos added yet.') }}</p>
            @endforelse
        </div>

        <h4 class="wizard-review-subheading">{{ __('Documents') }}</h4>
        <div class="wizard-doc-grid">
            @forelse ($documentItems as $document)
                @php
                    $documentId = is_array($document) ? ($document['document_id'] ?? null) : null;
                    $documentLabel = ($documentId && $documentNameById->has($documentId))
                        ? $documentNameById->get($documentId)
                        : (is_array($document) ? ($document['name'] ?? __('Document')) : __('Document'));
                    $documentUrl = is_array($document) ? ($document['url'] ?? '') : '';
                    $documentExt = strtolower(pathinfo($documentUrl, PATHINFO_EXTENSION));
                    $documentIsImage = in_array($documentExt, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']);
                    if ($documentExt === 'pdf') {
                        $documentIcon = 'fa-file-pdf';
                    } elseif (in_array($documentExt, ['doc', 'docx'])) {
                        $documentIcon = 'fa-file-word';
                    } elseif ($documentIsImage) {
                        $documentIcon = 'fa-file-image';
                    } else {
                        $documentIcon = 'fa-file-alt';
                    }
                @endphp
                <div class="wizard-doc-card">
                    <div class="wizard-doc-card__preview">
                        @if ($documentIsImage && $documentUrl)
                            <img src="{{ RvMedia::getImageUrl($documentUrl) }}" alt="{{ $documentLabel }}">
                        @else
                            <i class="fas {{ $documentIcon }}"></i>
                        @endif
                    </div>
                    <div class="wizard-doc-card__name" title="{{ $documentLabel }}">{{ $documentLabel }}</div>
                    @if ($documentUrl)
                        <div class="wizard-doc-card__actions">
                            <a href="{{ RvMedia::url($documentUrl) }}" target="_blank" rel="noopener" class="wizard-doc-card__btn" title="{{ __('Preview') }}"><i class="fas fa-eye"></i> {{ __('Preview') }}</a>
                            <a href="{{ RvMedia::url($documentUrl) }}" download class="wizard-doc-card__btn wizard-doc-card__btn--primary" title="{{ __('Download') }}"><i class="fas fa-download"></i> {{ __('Download') }}</a>
                        </div>
                    @endif
                </div>
            @empty
                <p class="wizard-hint">{{ __('No documents added.') }}</p>
            @endforelse
        </div>
    </div>
</div>
