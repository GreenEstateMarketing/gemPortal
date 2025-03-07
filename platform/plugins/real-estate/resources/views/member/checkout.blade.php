@extends('plugins/real-estate::member.layouts.member_skeleton')
@section('content')
    <div class="settings pt-5">
        <div class="container">


            <div class="package-detail p-4 m-4">
                <div class="head">
                    <h2>Package Detail</h2>
                </div>
                <div class="description">
                    <!--<label class="">Name</label><label class="pl-5">{{$package->name}}</label>-->
                    <div class="row ml-4 p-1">
                        <div class="col-md-4">
                            <label class="text-center">Name</label>
                        </div>
                        <div class="col-md-4">
                            <label class="text-center">{{$package->name}}</label>
                        </div>
                    </div>
                    <div class="row  ml-4 r p-1">
                        <div class="col-md-4">
                            <label class="text-center">No. of Posts</label>
                        </div>
                        <div class="col-md-4">
                            <label class="text-center">{{$package->number_of_listings}}</label>
                        </div>
                    </div>
                    <div class="row  ml-4 r p-1">
                        <div class="col-md-4">
                            <label class="text-center">Per Account Limit</label>
                        </div>
                        <div class="col-md-4">
                            <label class="text-center">{{$package->account_limit}}</label>
                        </div>
                    </div>
                    <div class="row  ml-4 r p-1">
                        <div class="col-md-4">
                            <label class="text-center">Total Price</label>
                        </div>
                        <div class="col-md-4">
                            <label class="text-center">{{$package->currency->symbol}} {{$total_price}}</label>
                        </div>
                    </div>
                </div>
            </div>
            <!-- <div class="row m-4">
            @if(session('error'))
                <div class="offset-md-3 col-md-6">
                    <div class="alert alert-danger">
                        {{session('error')}}
                </div>

            </div>


            @else
                <div class="offset-md-3 col-md-6 error-message d-none">
                    <div class="alert alert-danger">
                        <span id="alert-danger"></span>
                    </div>

                </div>


            @endif
            @if(session('success'))
                <div class="offset-md-3 col-md-6">
                    <div class="alert alert-success">
                        {{session('success')}}
                </div>
            </div>


            @else
                <div class="offset-md-3 col-md-6 success-message d-none">
                    <div class="alert alert-success">
                        <span id="alert-success"></span>
                    </div>
                </div>


            @endif
            </div> -->
            <!-- <form id="discount_form" method="POST" action="{{route('public.member.package.discount')}}">
            <div class="row mt-4 m-4">

                <div class="col-md-3 text-align-reverse">
                    <label class="text-center">Discount Voucher:</label>
                </div>
                <div class="col-md-6">

                    @csrf
            <form-group>
                <input type="text" name="voucher" class="form-control mb-2" required />
                <input type="hidden" name="id" value="{{$package->id}}" />

                    </form-group>

                </div>
                <div class="col-md-3 pr-0">
                    <input type="submit" id="btnDiscount" value="Apply"
                        class="form-control btnDiscount  btn btn-primary" />
                </div>

            </div>
        </form> -->

            @if(session('discount'))
                <!-- (-%) $voucher->data->get('discount_percent')-->
                <div class="row mt-1 mr-4">
                    <div class="offset-md-9 col-md-3 text-right pr-0">
                        <label>Discount (-%{{session('discount_percent')}}):
                            {{$package->currency->symbol}}{{session('discount')}}</label>
                    </div>
                </div>
            @else
                <div class="row mt-1 mr-4 discount-detial d-none">
                    <div class="offset-md-9 col-md-3 text-right pr-0">
                        <label>Discount (-%<span id="discount_per"></span>): {{$package->currency->symbol}} <span
                                    id="discount_price"></span></label>
                    </div>
                </div>
            @endif

            <div class="row mt-1 mr-4">
                <div class="offset-md-9 col-md-3 text-right pr-0">
                    <label><b>TOTAL PRICE: {{$package->currency->symbol}} <span
                                    id="total_price">{{$package->price}}</span></b></label>
                </div>
            </div>
            <!-- <table class="table table-bordered m-5"  >

                <tr>
                    <td><b>Package Name: </b></td>
                    <td>{{$package->name}}</td>
                </tr>

                <tr>
                    <td><b>Price: </b></td>
                    <td> {{$package->currency->symbol}}{{$total_price}} </td>
                </tr>
                <tr>
                    <td><b> Discount Voucher: </b></td>
                    <td>

                        <form  id="discount_form" method="POST" action="{{route('public.member.package.discount')}}">
                            @csrf
            <form-group>
                <input type="text" name="voucher" class="form-control mb-2" required/>
                <input type="hidden" name="id" value="{{$package->id}}"/>
                                <input type="submit" id="btnDiscount" value="Apply" class="form-control btn btn-primary" />
                            </form-group>
                        </form>
                    </td>
                </tr>
                @if(session('discount'))

                <tr>
                    <td><b>Discount (-%{{session('discount_percent')}}): </b></td>
                        <td>{{session('discount')}}</td>
                    </tr>


            @endif
            <tr>
                <td><b>Total Price : </b></td>
                <td>
{{$package->currency->symbol}}{{$package->price}}</td>
                </tr>

            </table>
-->
            <!-- <h2 class="m-4 pl-4 pr-4">Payment Method</h2> -->
            <div class="row justify-content-center pb-5">

                <form action="{{ $ssoUrl  }}" id="PageRedirectionForm" method="post"
                      novalidate="novalidate">
                    <input id="AuthToken" name="AuthToken" type="hidden" value="{{ $AuthToken }}">
                    <input id="RequestHash" name="RequestHash" type="hidden" value="{{ $hashRequest1 }}">
                    <input id="ChannelId" name="ChannelId" type="hidden" value="{{ $HS_ChannelId }}">
                    <input id="Currency" name="Currency" type="hidden" value="{{ $Currency }}">
                    <input id="IsBIN" name="IsBIN" type="hidden" value="{{ $IsBIN }}">
                    <input id="ReturnURL" name="ReturnURL" type="hidden" value="{{ $HS_ReturnURL }}">
                    <input id="MerchantId" name="MerchantId" type="hidden" value="{{ $HS_MerchantId }}">
                    <input id="StoreId" name="StoreId" type="hidden" value="{{ $HS_StoreId }}">
                    <input id="MerchantHash" name="MerchantHash" type="hidden" value="{{ $HS_MerchantHash }}">
                    <input id="MerchantUsername" name="MerchantUsername" type="hidden"
                           value="{{ $HS_MerchantUsername }}">
                    <input id="MerchantPassword" name="MerchantPassword" type="hidden"
                           value="{{ $HS_MerchantPassword }}">
                    <input id="TransactionTypeId" name="TransactionTypeId" type="hidden"
                           value="{{ $TransactionTypeId }}">

                    <input autocomplete="off" id="TransactionReferenceNumber" name="TransactionReferenceNumber"
                           placeholder="Order ID" type="hidden" value="{{ $HS_TransactionReferenceNumber }}">
                    <input autocomplete="off" id="TransactionAmount" name="TransactionAmount"
                           placeholder="Transaction Amount" type="hidden" value="{{ $TransactionAmount }}">


                    <br>
                    <center>
                        <button type="submit" class="btn btn-custon-four btn-danger" onclick="startLoading()" id="run">PAY ONLINE</button>
                    </center>
                </form>

                <!-- <div class="col-xs-12">
                    {!! do_shortcode('[payment-form currency="' . strtoupper($package->currency->title) . '" amount="' . $package->price . '" package_id="' . $package->id . '" name="' . $package->name . '" return_url="' . route('public.member.packages') . '" callback_url="' . route('public.member.package.subscribe.callback', $package->id) . '"][/payment-form]') !!}
                </div> -->
            </div>
        </div>
    </div>

@stop


<script>
    function startLoading() {
        var btn = document.getElementById("run");
        btn.innerText = "Redirecting...";
        btn.disabled = true;

        setTimeout(function () {
            document.getElementById("PageRedirectionForm").submit();
        }, 100);
    }
</script>