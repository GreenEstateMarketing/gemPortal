<div class="container-fluid mortage-calculator">
    <div class="row">
        <form class="form-horizontal well" id="mortgage-calculator" method="POST" onsubmit="return false;">
            <div class="col-xs-12 text-center">
                <p>Enter the <i>house price</i>, <i>interest rate</i>, <i>down payment</i> and <i>loan duration</i> to
                    see your estimated loan amount, monthly payment, and total cost of the loan:</p>
            </div>
            <div class="row align-items-end">
                <div class="col-md-3">
                    <div class="form-group">
                        <div id="house-price-group" class="input-spacer requisite">
                            <label class="control-label">House Price<span> PKR</span></label>
                            <div class="input-group">
                                <input disabled type="number" class="form-control" placeholder="Amount"
                                    name="house-price" tabindex="1" value="{{ $property->price }}">
                            </div>
                            <span class="error-text error-house-price"></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <div id="interest-rate-group" class="input-spacer requisite">
                            <label class="control-label">Interest Rate <span> %</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" placeholder="e.g. 8.5" name="interest-rate"
                                    tabindex="2" step="0.001" min="0" max="99.999">
                            </div>
                            <span class="error-text error-interest-rate"></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <div id="down-payment-group" class="input-spacer requisite">
                            <label class="control-label">Down Payment <span> %</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" placeholder="e.g. 20" name="down-payment"
                                    tabindex="3" step="0.001" min="0" max="99.999">
                            </div>
                            <span class="error-text error-down-payment"></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <div id="years-group" class="input-spacer requisite">
                            <label class="control-label">Loan Duration <span> years</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" placeholder="e.g. 20" name="years"
                                    tabindex="4" step="1" min="1" max="50" value="20">
                            </div>
                            <div class="btn-group btn-group-justified btn-year mt-1" data-toggle="buttons">
                                <label class="btn btn-default btn-xs" data-years="15">15</label>
                                <label class="btn btn-default btn-xs" data-years="20">20</label>
                                <label class="btn btn-default btn-xs" data-years="25">25</label>
                                <label class="btn btn-default btn-xs" data-years="30">30</label>
                            </div>
                            <span class="error-text error-years"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-xs-12 col-md-4 offset-md-4">
                    <button type="button" class="btn btn-primary btn-lg btn-block" name="calculate"
                        tabindex="5">Calculate</button>
                </div>
            </div>

            <h3 class="text-center">Results</h3>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label" for="loan-amount">Loan Amount</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="loan-amount" id="loan-amount" readonly>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label" for="monthly-payment">Monthly Payment</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="monthly-payment" id="monthly-payment"
                                readonly>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label" for="total-payment">Total Payment</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="total-payment" id="total-payment" readonly>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label" for="total-interest">Total Interest</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="total-interest" id="total-interest"
                                readonly>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<style>
    .mortage-calculator .error-text {
        display: block;
        color: #dc3545;
        font-size: 12px;
        margin-top: 4px;
        min-height: 16px;
    }

    .mortage-calculator .btn-year .btn {
        margin: 0 2px;
    }
</style>
<script>
    $(document).ready(function () {
        var form = $("#mortgage-calculator");

        var housePrice = form.find("input[name=house-price]");
        var interestRate = form.find("input[name=interest-rate]");
        var downPayment = form.find("input[name=down-payment]");
        var years = form.find("input[name=years]");

        function numberFormat(value) {
            if (isNaN(value) || !isFinite(value)) {
                return "-";
            }

            return Number(value).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        // Validate a single field and return { valid, value, message }
        function validateField(name) {
            switch (name) {
                case "house-price": {
                    var price = parseFloat(housePrice.val());
                    if (!housePrice.val() || isNaN(price) || price <= 0) {
                        return { valid: false, message: "Enter a valid house price." };
                    }
                    return { valid: true, value: price };
                }
                case "interest-rate": {
                    var rate = parseFloat(interestRate.val());
                    if (interestRate.val() === "" || isNaN(rate) || rate < 0 || rate >= 100) {
                        return { valid: false, message: "Enter a rate between 0 and 100." };
                    }
                    return { valid: true, value: rate };
                }
                case "down-payment": {
                    var down = parseFloat(downPayment.val());
                    if (downPayment.val() === "" || isNaN(down) || down < 0 || down >= 100) {
                        return { valid: false, message: "Enter a down payment between 0 and 100." };
                    }
                    return { valid: true, value: down };
                }
                case "years": {
                    var numYears = parseInt(years.val(), 10);
                    if (!years.val() || isNaN(numYears) || numYears <= 0 || numYears > 50) {
                        return { valid: false, message: "Enter a duration between 1 and 50 years." };
                    }
                    return { valid: true, value: numYears };
                }
            }
        }

        function showFieldState(name, result) {
            var group = form.find("#" + name + "-group");
            var errorEl = form.find(".error-" + name);

            if (result.valid) {
                group.removeClass("has-error").addClass("has-success");
                errorEl.text("");
            } else {
                group.removeClass("has-success").addClass("has-error");
                errorEl.text(result.message);
            }
        }

        function validateAll() {
            var fields = ["house-price", "interest-rate", "down-payment", "years"];
            var results = {};
            var allValid = true;

            fields.forEach(function (name) {
                var result = validateField(name);
                results[name] = result;
                showFieldState(name, result);
                if (!result.valid) {
                    allValid = false;
                }
            });

            return allValid ? results : null;
        }

        function clearResults() {
            form.find("#loan-amount, #monthly-payment, #total-payment, #total-interest").val("");
        }

        function calculate() {
            var results = validateAll();

            if (!results) {
                clearResults();
                return;
            }

            var price = results["house-price"].value;
            var ratePct = results["interest-rate"].value;
            var downPct = results["down-payment"].value;
            var numYears = results["years"].value;

            var loanAmount = price - (price * (downPct / 100));
            var months = numYears * 12;
            var monthlyRate = ratePct / 100 / 12;

            var monthlyPayment;
            if (monthlyRate === 0) {
                monthlyPayment = loanAmount / months;
            } else {
                monthlyPayment = (loanAmount * monthlyRate) / (1 - Math.pow(1 + monthlyRate, -1 * months));
            }

            var totalPayment = monthlyPayment * months;
            var totalInterest = totalPayment - loanAmount;

            form.find("#loan-amount").val(numberFormat(loanAmount));
            form.find("#monthly-payment").val(numberFormat(monthlyPayment));
            form.find("#total-payment").val(numberFormat(totalPayment));
            form.find("#total-interest").val(numberFormat(totalInterest));
        }

        // Live-validate on any input change (typing, pasting, spinner clicks, programmatic changes)
        form.find("input[name=interest-rate], input[name=down-payment], input[name=years]").on("input", function () {
            var name = $(this).attr("name");
            showFieldState(name, validateField(name));
        });

        // Quick-select duration buttons
        form.find(".btn-year .btn").on("click", function () {
            form.find(".btn-year .btn").removeClass("btn-success");
            $(this).addClass("btn-success");
            years.val($(this).data("years")).trigger("input");
        });

        form.find("button[name=calculate]").on("click", function () {
            calculate();
        });

        // Validate the house price (prefilled, disabled) once on load so it shows correctly
        showFieldState("house-price", validateField("house-price"));
    });
</script>
