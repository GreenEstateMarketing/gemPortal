@extends('plugins/real-estate::account.layouts.skeleton')
@section('content')
    <div class="settings pt-5">
        <div class="container">
            <div class="package-detail p-4 m-4">
                <div class="head">
                    <h2>Package Detail</h2>
                </div>
                <div class="description">
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

            <div class="row mt-1 mr-4">
                <div class="offset-md-9 col-md-3 text-right pr-0">
                    <label><b>TOTAL PRICE: {{$package->currency->symbol}} <span
                                    id="total_price">{{$package->price}}</span></b></label>
                </div>
            </div>

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
                        <button type="submit" class="btn btn-custon-four btn-danger" id="run" onclick="startLoading()">PAY ONLINE</button>
                    </center>
                </form>
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
