@if(session('error'))
    <div class="alert alert-danger m-5" style="width: 50%">
        {{session('error')}}
    </div>
@endif
<table class="table table-bordered m-5" style="width: 50%" >

    <tr>
       <td><b>Package Name: </b></td>
        <td>{{$package->name}}</td>
    </tr>

    <tr>
        <td><b>Price: </b></td>
        <td> {{ format_price($package->price, $package->currency) }}</td>
    </tr>
    <tr>
        <td><b> Discount Voucher: </b></td>
        <td>
            <form action="{{route('public.member.package.postcheckout')}}" method="POST">
                @csrf
                <form-group>
                    <input type="text" name="voucher" class="form-control mb-2" required/>
                    <input type="hidden" name="id" value="{{$package->id}}"/>
                    <input type="submit" value="Calculate" class="form-control btn btn-primary" />
                </form-group>
            </form>
        </td>
    </tr>
    @if($voucher)

        <tr>
            <td><b>Discount (-{{$voucher->data->get('discount_percent')}}%): </b></td>
            <td>{{$package->price-$total_price}}</td>
        </tr>
    @endif
    <tr>
        <td><b>Total Price : </b></td>
        <td>{{ format_price($total_price,$package->currency) }}</td>
    </tr>

</table>

