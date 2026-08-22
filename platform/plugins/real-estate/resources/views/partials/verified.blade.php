<div class="row">
    @if($verified)
        <a type="button" href="javascript:void(0)" class="form-control btn btn-primary"
            style="background:#078d24 !important">
            <i class="fa fa-check-circle"></i> This property has been verified by you.
        </a>
    @else
        <a type="button" class="form-control btn btn-danger"
            href="{{ route('public.account.properties.verify', ['id' => $propertyId]) }}">
            <i class="fa fa-check-circle"></i> After verifiying everything, click here to mark this property as verified.
        </a>
    @endif
</div>