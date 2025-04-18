<div id="buyer-info" class="{{ $buyerInfo && $properties && in_array($properties->status, ['sold', 'rented', 'closed']) ? '' : 'd-none' }}">
<div id="success-message"></div> <!-- Success alert goes here -->
    @csrf
    <input type="hidden" id="buyer-agent-id" name="agent_id" value="{{ $properties ? $properties->author_id : ''  }}"/>
    <input type="hidden" id="buyer-seller-id" name="seller_id" value="{{ $properties ? $properties->member_id : ''  }}"/>
    <input type="hidden" id="buyer-property-id" name="property_id" value="{{ $properties ? $properties->id : '' }}"/>
    <input type="hidden" id="buyer-transaction-type" name="transaction_type" value="{{ $properties ? $properties->type : '' }}"/>

    <div class="row mb-3">
        <div class="col-md-6">
            <label for="name" class="form-label">Name</label>
            <input type="text" value="{{ $buyerInfo? $buyerInfo->name : ''  }}" class="form-control" id="buyer-name" name="buyer-name" placeholder="Enter your name">
            <div class="invalid-feedback d-block" id="error-buyer-name"></div>
        </div>

        <div class="col-md-6">
            <label for="phone" class="form-label">Phone</label>
            <input type="tel" value="{{ $buyerInfo? $buyerInfo->phone : ''  }}" class="form-control" id="buyer-phone" name="buyer-phone"
                   placeholder="Enter your phone number">
            <div class="invalid-feedback d-block" id="error-buyer-phone"></div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label for="email" class="form-label">Email</label>
            <input type="email" value="{{ $buyerInfo? $buyerInfo->email : ''  }}" class="form-control" id="buyer-email" name="buyer-email"
                   placeholder="Enter your email">
            <div class="invalid-feedback d-block" id="error-buyer-email"></div>
        </div>

        <div class="col-md-6">
            <label for="amount" class="form-label">Amount</label>
            <input type="number" value="{{ $buyerInfo? $buyerInfo->amount : ''  }}" class="form-control" id="buyer-amount" name="buyer-amount"
                   placeholder="Enter amount">
            <div class="invalid-feedback d-block" id="error-buyer-amount"></div>
        </div>
    </div>

    <button id="save-buyer-info" type="button" class="btn btn-primary">{{ $buyerInfo ? 'Update' : 'Save'  }} Buyer Info</button>
</div>


<script>
    $(document).ready(function () {
        $('#save-buyer-info').on('click', function (e) {
            e.preventDefault();

            // Clear old errors and messages
            $('.invalid-feedback').html('');
            $('#success-message').html('');

            // Gather input values
            let data = {
                name: $('#buyer-name').val(),
                phone: $('#buyer-phone').val(),
                email: $('#buyer-email').val(),
                amount: $('#buyer-amount').val(),
                agent_id: $('#buyer-agent-id').val(),
                seller_id: $('#buyer-seller-id').val(),
                property_id: $('#buyer-property-id').val(),
                transaction_type: $('#buyer-transaction-type').val()
            };

            $.ajax({
                url: '{{ route("save-buyer-info") }}',
                method: 'POST',
                data: data,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function (response) {
                    const form = document.getElementById('buyer-form');
                    if (form) {
                        form.reset();
                    }

                    $('#success-message').html(`
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        ${response.message}
                    </div>
                `);
                },
                error: function (xhr) {
                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function (field, messages) {
                            $('#error-buyer-' + field).html(messages[0]);
                        });
                    } else {
                        $('#success-message').html(`
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            Something went wrong. Please try again.
                        </div>
                    `);
                    }
                }
            });
        });
    });


</script>
