@php
    $currentBuyer = $property->buyer;
    $statusOptions = \Botble\RealEstate\Enums\PropertyStatusEnum::labels();
    $buyerStatuses = [
        \Botble\RealEstate\Enums\PropertyStatusEnum::SOLD,
        \Botble\RealEstate\Enums\PropertyStatusEnum::RENTED,
    ];
@endphp

<div class="modal fade" id="property-status-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" data-status-modal-content>
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Manage Listing Status') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form method="post" action="{{ route('property.wizard.update-status', ['property' => $property->id]) }}" data-status-form>
                @csrf
                <div class="modal-body">
                    <div class="wizard-status-modal__alert" data-status-alert style="display:none;"></div>

                    <div class="form-group">
                        <label>{{ __('Current Status') }}</label>
                        <div>{!! $property->status->toHtml() !!}</div>
                    </div>

                    <div class="form-group">
                        <label for="property-status-select">{{ __('New Status') }}</label>
                        <select name="new_status" id="property-status-select" class="form-control" data-status-select>
                            @foreach ($statusOptions as $value => $label)
                                <option value="{{ $value }}" {{ (string) $property->status === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <div class="wizard-error" data-error-for="new_status"></div>
                    </div>

                    <div class="form-group">
                        <label for="property-status-comment">{{ __('Comment') }}</label>
                        <textarea name="comment" id="property-status-comment" class="form-control" rows="3" placeholder="{{ __('Optional note about this status change...') }}"></textarea>
                        <div class="wizard-error" data-error-for="comment"></div>
                    </div>

                    <div data-buyer-fields style="display:none;">
                        <hr>
                        <h6 data-buyer-heading>{{ __('Buyer Information') }}</h6>

                        <div class="form-group">
                            <label>{{ __('Full Name') }}</label>
                            <input type="text" name="buyer_name" class="form-control" value="{{ $currentBuyer->name ?? '' }}">
                            <div class="wizard-error" data-error-for="buyer_name"></div>
                        </div>

                        <div class="form-group">
                            <label>{{ __('Phone') }}</label>
                            <input type="text" name="buyer_phone" class="form-control" placeholder="+1234567890" value="{{ $currentBuyer->phone ?? '' }}">
                            <div class="wizard-error" data-error-for="buyer_phone"></div>
                        </div>

                        <div class="form-group">
                            <label>{{ __('Email') }}</label>
                            <input type="email" name="buyer_email" class="form-control" value="{{ $currentBuyer->email ?? '' }}">
                            <div class="wizard-error" data-error-for="buyer_email"></div>
                        </div>

                        <div class="form-group">
                            <label data-buyer-amount-label>{{ __('Sale Amount') }}</label>
                            <input type="number" step="0.01" min="0" name="buyer_amount" class="form-control" value="{{ $currentBuyer->amount ?? '' }}">
                            <div class="wizard-error" data-error-for="buyer_amount"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary" data-status-submit>{{ __('Save Status') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    'use strict';

    var modalEl = document.getElementById('property-status-modal');
    if (!modalEl) {
        return;
    }

    var form = modalEl.querySelector('[data-status-form]');
    var select = modalEl.querySelector('[data-status-select]');
    var buyerFields = modalEl.querySelector('[data-buyer-fields]');
    var buyerHeading = modalEl.querySelector('[data-buyer-heading]');
    var buyerAmountLabel = modalEl.querySelector('[data-buyer-amount-label]');
    var alertBox = modalEl.querySelector('[data-status-alert]');
    var submitBtn = modalEl.querySelector('[data-status-submit]');
    var buyerStatuses = @json($buyerStatuses);
    var labels = {
        buyer: '{{ __('Buyer Information') }}',
        renter: '{{ __('Renter Information') }}',
        saleAmount: '{{ __('Sale Amount') }}',
        rentAmount: '{{ __('Rent Amount') }}',
    };

    function syncBuyerFields() {
        if (!select || !buyerFields) {
            return;
        }
        var isRented = select.value === 'rented';
        var show = buyerStatuses.indexOf(select.value) !== -1;
        buyerFields.style.display = show ? '' : 'none';
        if (buyerHeading) {
            buyerHeading.textContent = isRented ? labels.renter : labels.buyer;
        }
        if (buyerAmountLabel) {
            buyerAmountLabel.textContent = isRented ? labels.rentAmount : labels.saleAmount;
        }
    }

    function clearErrors() {
        modalEl.querySelectorAll('[data-error-for]').forEach(function (el) {
            el.textContent = '';
        });
        alertBox.style.display = 'none';
        alertBox.textContent = '';
    }

    function applyErrors(errors) {
        clearErrors();
        Object.keys(errors || {}).forEach(function (field) {
            var el = modalEl.querySelector('[data-error-for="' + field + '"]');
            if (el) {
                el.textContent = errors[field][0];
            }
        });
    }

    if (select) {
        select.addEventListener('change', syncBuyerFields);
        syncBuyerFields();
    }

    if (form) {
        form.addEventListener('submit', function (event) {
            event.preventDefault();
            clearErrors();

            if (submitBtn) {
                submitBtn.disabled = true;
            }

            var formData = new FormData(form);
            var meta = document.querySelector('meta[name="csrf-token"]');
            var token = meta ? meta.getAttribute('content') : formData.get('_token');

            fetch(form.getAttribute('action'), {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            }).then(function (response) {
                return response.json().then(function (json) {
                    return { ok: response.ok, json: json };
                });
            }).then(function (result) {
                if (submitBtn) {
                    submitBtn.disabled = false;
                }
                if (result.ok) {
                    // The confirmation itself is a flashed success_msg the
                    // server set on this same request (see updateStatus()) -
                    // this reload is what both picks that up (through the
                    // same success_msg/Botble.showSuccess() toast every
                    // other admin action already uses) and refreshes the
                    // Current Status badge, global-header lock state, etc.
                    window.location.reload();
                } else if (result.json && result.json.errors) {
                    applyErrors(result.json.errors);
                } else {
                    alertBox.textContent = '{{ __('Something went wrong. Please try again.') }}';
                    alertBox.style.display = '';
                }
            }).catch(function () {
                if (submitBtn) {
                    submitBtn.disabled = false;
                }
                alertBox.textContent = '{{ __('Something went wrong. Please try again.') }}';
                alertBox.style.display = '';
            });
        });
    }
})();
</script>
