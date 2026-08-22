<div class="container-fluid mortage-calculator">
    <div class="row">
        <form class="form-horizontal well" id="mortgage-calculator" method="POST">
            <div class="col-xs-12 text-center">
                <p>Enter the <i>house price</i>, <i>interest rate</i>, <i>length of the loan</i>, <i>your down
                        payment</i>, <i>insurance</i> and <i>taxes</i> to see how much your monthly mortgage payment
                    will be:</p>
            </div>
            <div class="row align-items-end">
                <div class="col-md-4">
                    <div class="form-group">
                        <div id="house-price-group" class="input-spacer requisite">
                            <label class="control-label">House Price<span> PKR</span></label>
                            <div class="input-group">

                                <input disabled type="number" class="form-control" placeholder="Amount"
                                    name="house-price" tabindex="1" value="{{ $property->price }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div id="interest-rate-group" class="input-spacer requisite">
                        <label class="control-label">Interest Rate <span> %</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control" name="interest-rate" tabindex="2" step="0.001">

                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div id="years-group" class="input-spacer requisite">
                        <label class="control-label"># of Years</label>
                        <div class="btn-group btn-group-justified btn-year" data-toggle="buttons" name="years">
                            <label class="btn btn-default" name="years-button-15">
                                <input type="radio" name="years-radio" class="year-radio" autocomplete="off" value="15"
                                    checked>15</label>
                            <label class="btn btn-default" name="years-button-30">
                                <input type="radio" name="years-radio" class="year-radio" autocomplete="off"
                                    value="30">30</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div id="down-payment-group" class="input-spacer requisite">
                        <label class="control-label">Down Payment <span> %</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control" name="down-payment" tabindex="3" step="0.001">
                            <span class="error-dp"></span>

                        </div>
                    </div>
                </div>
                <!--                <div class="col-md-4">
                    <div id="insurance-group" class="requisite">
                        <label class="control-label">Insurance  <span> PKR</span></label>
                        &lt;!&ndash;                            <button type="button" class="btn btn-xs btn-default" data-toggle="popover" data-trigger="hover" title="Annual Insurance" data-content="Enter the amount of your annual premium.">?</button>&ndash;&gt;
                        <div class="input-group">

                            <input type="number" class="form-control" placeholder="Amount" name="insurance" tabindex="4">
                        </div>
                    </div>
                </div>-->
                <!--                <div class="col-md-4">
                    <div id="taxes-group" class="requisite">
                        <label class="control-label">Taxes <span> PKR</span></label>
                        &lt;!&ndash;                        <button type="button" class="btn btn-xs btn-default" data-toggle="popover" data-trigger="hover" title="Annual Taxes" data-content="Enter the amount of your annual tax bill.">?</button>&ndash;&gt;
                        <div class="input-group">

                            <input type="number" class="form-control" placeholder="Amount" name="taxes" tabindex="5">
                        </div>
                    </div>
                </div>-->
            </div>
            <div class="row mt-4">

                <div class="col-xs-12 col-md-4 offset-md-4">
                    <button type="button" class="btn btn-primary btn-lg btn-block" name="calculate"
                        tabindex="6">Calculate</button>
                </div>

            </div>


            <!--            <h3 class="text-center">Monthly Insurance & Taxes</h3>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <div id="insurance-monthly-group" class="">
                            <label class="control-label" for="insurance-monthly">Insurance <span> PKR</span></label>
                            <div class="input-group">

                                <input type="number" class="form-control" name="insurance-monthly" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <div id="taxes-monthly-group" class="">
                            <label class="control-label" for="monthly-taxes">Taxes <span> PKR</span></label>
                            <div class="input-group">

                                <input type="number" class="form-control" name="taxes-monthly" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>-->
            <h3 class="text-center">Total Monthly Payments <br><small></small></h3>
            <!--<div class="row">


            </div>-->
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label" for="conv">Total PKR</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="conv" readonly>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label" for="conv-interest">Interest</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="conv-interest" readonly>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label" for="conv-principal">Principal</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="conv-principal" readonly>
                        </div>
                    </div>
                </div>
            </div>

            <!--    <p class="col-xs-12 col-sm-8 col-sm-offset-2 text-center">If you would like the information presented in this calculator to keep for offline viewing, printing, or sharing, enter your email below and click <q>Send</q>.</p>
                <div class="form-group">
                    <div id="email-group" class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 col-xs-12">
                        <div class="input-group">
                            <input type="email" class="form-control" name="email" placeholder="Email"></input>
                            <span class="input-group-btn">
                            <button type="submit" class="btn btn-success" name="submitEmail">Send</button>
                          </span>
                        </div>
                    </div>
                </div>-->
            <div class="form-group" name="response">
            </div>
        </form>
    </div>
</div>
<script>
    $(document).ready(function () {
        // Initiate popovers for '?' buttons next to input labels
        $("[data-toggle='popover']").popover({
            container: "body",
            placement: "auto"
        });

        // Lay out number inputs as Jquery selector to iterate upon for validation
        var inputs = $("input[name=house-price], input[name=interest-rate], input[name=down-payment], input[name=insurance], input[name=taxes]");

        // Declare input variables for ease of use
        var housePrice = $("input[name=house-price]");
        var interestRate = $("input[name=interest-rate]");
        var years;
        var downPayment = $("input[name=down-payment]");
        var insurance = $("input[name=insurance]");
        var taxes = $("input[name=taxes]");

        // Booleans for input validation
        var housePriceValid = true;
        var interestRateValid = false;
        var yearsValid = true;
        var downPaymentValid = false;
        var insuranceValid = false;
        var taxesValid = false;

        // Iterate upon inputs, making white when empty.
        $.each(inputs, function () {
            var input = $(this);
            input.keyup(function () {
                if (!input.val()) {
                    input.closest($("div .requisite")).removeClass("has-error").removeClass("has-success");
                }
            });
        });

        // Check if House Price input is greater than 0 on each keyup.
        // housePrice.keyup(function() {
        //     if ($(this).val()) {
        //         if (parseFloat($(this).val()) > 0) {
        //             $(this).closest($("div .requisite")).removeClass("has-error").addClass("has-success");
        //             housePriceValid = true;
        //         } else {
        //             $(this).closest($("div .requisite")).removeClass("has-success").addClass("has-error");
        //             housePriceValid = false;
        //         }
        //     }
        // });

        // Check if Interest Rate input is greater than 0 and less than 100 on each keyup.
        interestRate.keyup(function () {
            if ($(this).val()) {
                if (parseFloat($(this).val()) > 0 && parseFloat($(this).val()) < 100) {
                    $(this).closest($("div .requisite")).removeClass("has-error").addClass("has-success");
                    interestRateValid = true;
                } else {
                    $(this).closest($("div .requisite")).removeClass("has-success").addClass("has-error");
                    interestRateValid = false;
                }
            }
        });

        // Handle switching of radio buttons
        $("input:radio[name=years-radio]").change(function () {

            years = $("input:radio[name=years-radio]:checked");
            if ($("input:radio[name=years-radio]:checked").val() === "15") {
                $("div#years-group").removeClass("has-error").addClass("has-success");
                $("label[name=years-button-30]").removeClass("btn-success btn-danger");
                $("label[name=years-button-15]").removeClass("btn-danger").addClass("btn-success");
                yearsValid = true;
            } else if ($("input:radio[name=years-radio]:checked").val() === "30") {

                $("div#years-group").removeClass("has-error").addClass("has-success");
                $("label[name=years-button-15]").removeClass("btn-success btn-danger");
                $("label[name=years-button-30]").removeClass("btn-danger").addClass("btn-success");
                yearsValid = true;
            }
            $("div#years-group").addClass("has-success");
        });

        $("input:radio[name=years-radio]").trigger("change");
        // Check if Down Payment input is >= 0 and < 100 for validation
        downPayment.keyup(function () {
            if ($(this).val()) {
                if (parseFloat($(this).val()) >= 0 && parseFloat($(this).val()) < 100) {
                    $(this).closest($("div .requisite")).removeClass("has-error").addClass("has-success");
                    downPaymentValid = true;
                } else {
                    $(this).closest($("div .requisite")).removeClass("has-success").addClass("has-error");
                    // $(this).closest($("div .requisite")).append("<p>Down Payment input is >= 0 and < 100</p>");
                }
            }
        });

        // Make sure no negative number is inputted for insurance dollar amount
        insurance.keyup(function () {
            if ($(this).val()) {
                if (parseFloat($(this).val()) > 0) {
                    $(this).closest($("div .requisite")).removeClass("has-error").addClass("has-success");
                    insuranceValid = true;
                } else {
                    $(this).closest($("div .requisite")).removeClass("has-success").addClass("has-error");
                }
            }
        });

        // Make sure no negative number is inputted for taxes dollar amount
        taxes.keyup(function () {
            if ($(this).val()) {
                if (parseFloat($(this).val()) > 0) {
                    $(this).closest($("div .requisite")).removeClass("has-error").addClass("has-success");
                    taxesValid = true;
                } else {
                    $(this).closest($("div .requisite")).removeClass("has-success").addClass("has-error");
                }
            }
        });

        var emailHousePrice;
        var emailInterestRate;
        var emailYears;
        var emailDownPayment;
        var emailInsurance;
        var emailTaxes;
        var emailInsuranceMonthly;
        var emailTaxesMonthly;
        var emailFHA;
        var emailVA;
        var emailUSDA;
        var emailCONV;

        // Validate inputs and calculate if all is valid.
        $("button[name=calculate]").click(function () {

            if (!$("input:radio[name=years-radio]:checked").val()) {
                $("div#years-group").addClass("has-error");
                $("label[name=years-button-15]").addClass("btn-danger");
                $("label[name=years-button-30]").addClass("btn-danger");
            }
            $.each(inputs, function () {
                var input = $(this);
                if (!input.val()) {
                    input.closest($("div .requisite")).addClass("has-error");
                }
            });
            //yearsValid
            //alert(housePriceValid); alert(interestRateValid); alert(yearsValid); alert(downPaymentValid); alert(insuranceValid);alert(taxesValid);
            //&& insuranceValid && taxesValid
            if (housePriceValid && interestRateValid && yearsValid && downPaymentValid) {
                calculate();
                emailHousePrice = $("input[name=house-price]").val();
                emailInterestRate = $("input[name=interest-rate]").val();
                emailYears = $("input:radio[name=years-radio]:checked").val();
                emailDownPayment = $("input[name=down-payment]").val();
                // emailInsurance = $("input[name=insurance]").val();
                // emailTaxes = $("input[name=taxes]").val();
                emailInsuranceMonthly = $("input[name=insurance-monthly]").val();
                emailTaxesMonthly = $("input[name=taxes-monthly]").val();
                emailFHA = $("input[name=fha]").val();
                emailVA = $("input[name=va]").val();
                emailUSDA = $("input[name=usda]").val();
                emailCONV = $("input[name=conv]").val();
            }
        });

        // Handle all calculations given the values inputted.
        function calculate() {
            // alert("here");
            function getTotal(principle, payment) {
                return ((((principle * interestRateM) / (1 - Math.pow(1 + interestRateM, (-1 * months))) * 100) / 100) + insuranceMonthly + taxesMonthly + payment);
            }
            var numYears = parseInt(years.val());
            var principle = housePrice.val() - (housePrice.val() * (downPayment.val() / 100));
            var interestRateM = interestRate.val() / (100 * 12);
            var months = numYears * 12;
            var monthlyPayment = ((principle * interestRateM) / (1 - Math.pow(1 + interestRateM, -1 * months)) * 100) / 100;
            var insuranceMonthly = insurance.val() / 12;
            $("input[name=insurance-monthly]").val(insuranceMonthly.toFixed(2));
            var taxesMonthly = taxes.val() / 12;
            $("input[name=taxes-monthly]").val(taxesMonthly.toFixed(2));

            var fhaPrinciple = principle + (principle * 0.0175);
            var fhaPayment;
            if (numYears === 15) {
                if (downPayment.val() >= 10) {
                    fhaPayment = ((fhaPrinciple * 0.0025) / 12);
                } else if (downPayment.val() < 10) {
                    fhaPayment = ((fhaPrinciple * 0.0050) / 12);
                }
            } else if (numYears === 30) {
                if (downPayment.val() >= 5) {
                    fhaPayment = ((fhaPrinciple * 0.0055) / 12);
                } else if (downPayment.val() < 5) {
                    fhaPayment = ((fhaPrinciple * 0.0060) / 12);
                }
            }

            var fhaTotal;
            if (downPayment.val() >= 3.5) {
                fhaTotal = getTotal(fhaPrinciple, fhaPayment);
                $("input[name=fha]").val(fhaTotal.toFixed(2));
            } else {
                $("input[name=fha]").val("3.5% down required");
            }

            var vaPrinciple;
            if (downPayment.val() >= 10) {
                vaPrinciple = (principle * 1.0125);
            } else if (downPayment.val() >= 5 && downPayment.val() < 10) {
                vaPrinciple = (principle * 1.015);
            } else {
                vaPrinciple = (principle * 1.0215);
            }
            var vaTotal = getTotal(vaPrinciple, 0);
            $("input[name=va]").val(vaTotal.toFixed(2));

            var usdaPrinciple = principle + (principle * 0.01);
            var usdaPayment = (usdaPrinciple * 0.0035) / 12;
            var usdaTotal = getTotal(usdaPrinciple, usdaPayment);
            $("input[name=usda]").val(usdaTotal.toFixed(2));

            var convPayment;
            if (numYears == 15) {
                if (downPayment.val() >= 3 && downPayment.val() < 5) {
                    convPayment = (principle * 0.0033) / 12;
                } else if (downPayment.val() >= 5 && downPayment.val() < 10) {
                    convPayment = (principle * 0.0028) / 12;
                } else if (downPayment.val() >= 10 && downPayment.val() < 15) {
                    convPayment = (principle * 0.0021) / 12;
                } else if (downPayment.val() >= 15 && downPayment.val() < 20) {
                    convPayment = (principle * 0.0018) / 12;
                } else if (downPayment.val() >= 20 && downPayment.val() <= 100) {
                    convPayment = 0;
                }
            } else if (numYears == 30) {
                if (downPayment.val() >= 3 && downPayment.val() < 5) {
                    convPayment = (principle * 0.0062) / 12;
                } else if (downPayment.val() >= 5 && downPayment.val() < 10) {
                    convPayment = (principle * 0.0052) / 12;
                } else if (downPayment.val() >= 10 && downPayment.val() < 15) {
                    convPayment = (principle * 0.0030) / 12;
                } else if (downPayment.val() >= 15 && downPayment.val() < 20) {
                    convPayment = (principle * 0.0019) / 12;
                } else if (downPayment.val() >= 20 && downPayment.val() <= 100) {
                    convPayment = 0;
                }
            }

            var convTotal;
            if (downPayment.val() >= 3) {
                convTotal = monthlyPayment + convPayment;
                var convInterestOnly = principle * interestRateM;
                var convPrincipalOnly = monthlyPayment - convInterestOnly;

                $("input[name=conv]").val(convTotal.toFixed(2));
                $("input[name=conv-interest]").val(convInterestOnly.toFixed(2));
                $("input[name=conv-principal]").val(convPrincipalOnly.toFixed(2));
            } else {
                $("input[name=conv]").val("3% down payment required");
                $("input[name=conv-interest]").val("");
                $("input[name=conv-principal]").val("");
            }
        }

        $("form").submit(function (event) {
            event.preventDefault();
            event.stopImmediatePropagation()
            $("#email-group").removeClass("has-error");
            $(".help-block").remove();
            $(".alert-success").remove();
            var email = $("input[name=email]").val();

            var formData = {
                "housePrice": emailHousePrice,
                "interestRate": emailInterestRate,
                "years": emailYears,
                "downPayment": emailDownPayment,
                "insurance": emailInsurance,
                "taxes": emailTaxes,
                "insuranceMonthly": emailInsuranceMonthly,
                "taxesMonthly": emailTaxesMonthly,
                "fha": emailFHA,
                "va": emailVA,
                "usda": emailUSDA,
                "conv": emailCONV,
                "email": email
            };

            $.ajax({
                type: "post",
                url: "emailMortgage.php",
                data: formData,
                dataType: "json",
                encode: true
            })
                .done(function (response) {
                    if (!response.success) {
                        if (!response.wp_mail) {
                            if (response.errors.email) {
                                $("#email-group").addClass("has-error");
                                $("#email-group").append("<div class='help-block'>" + response.errors.email + "</div>");
                            }

                            $("button[name=submit-email]").prop("disabled", false);
                        } else if (response.wp_mail) {
                            $("div[name=response]").append("<div class='alert alert-danger text-center'>" + response.wp_mail + "</div>");
                        }
                    } else {
                        $("div[name=response]").append("<div class='alert alert-success text-center'>" + response.action + "</div>");
                        $("button[name=submit-email]").prop("disabled", true);
                    }
                });
        });
    });

</script>