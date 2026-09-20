<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 36px 40px; }
        body { font-family: Helvetica, Arial, sans-serif; font-size: 11px; color: #222222; }
        .logo { width: 140px; margin-bottom: 10px; }
        h1.title {
            color: #1e6b3a;
            font-size: 20px;
            letter-spacing: 0.5px;
            margin: 0 0 16px;
        }
        h2.section {
            font-size: 13px;
            margin: 18px 0 8px;
            border-bottom: 1px solid #dddddd;
            padding-bottom: 4px;
        }
        p { line-height: 1.6; margin: 0 0 8px; }
        table.parties, table.declaration { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table.parties td { padding: 4px 0; vertical-align: top; }
        table.parties td.field-label { width: 60px; font-weight: bold; }
        table.parties td.field-value { border-bottom: 1px solid #999999; padding-bottom: 2px; }
        table.field-row { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        table.field-row td.field-label { width: 110px; font-weight: bold; vertical-align: top; padding: 4px 0; }
        table.field-row td.field-value { border-bottom: 1px solid #999999; padding: 4px 0; }
        ul.terms { margin: 0 0 8px; padding-left: 16px; }
        ul.terms li { margin-bottom: 6px; line-height: 1.5; }
        table.declaration td { width: 50%; vertical-align: top; padding-top: 10px; }
        table.declaration .party-heading { font-weight: bold; font-size: 12px; margin-bottom: 8px; }
        table.declaration .sig-row { margin-bottom: 10px; }
        table.declaration .sig-label { font-weight: bold; display: block; margin-bottom: 2px; }
        table.declaration .sig-line { border-bottom: 1px solid #999999; min-height: 34px; display: block; }
        table.declaration img.signature { max-height: 32px; max-width: 160px; }
    </style>
</head>
<body>
    @if ($logoDataUri)
        <img class="logo" src="{{ $logoDataUri }}" alt="GEM">
    @endif

    <h1 class="title">SELLER &amp; BUYER PROPERTY AGREEMENT</h1>

    <p>This Agreement is made between the following parties:</p>

    <table class="parties">
        <tr>
            <td class="field-label">Seller:</td>
            <td class="field-value" style="width: 33%;">{{ $sellerName ?? '—' }}</td>
            <td class="field-label" style="width: 50px;">CNIC:</td>
            <td class="field-value" style="width: 20%;"></td>
            <td class="field-label" style="width: 60px;">Contact:</td>
            <td class="field-value">{{ $sellerContact ?? '—' }}</td>
        </tr>
        <tr><td colspan="6">&nbsp;</td></tr>
        <tr>
            <td class="field-label">Agent:</td>
            <td class="field-value">{{ $agentName ?? '—' }}</td>
            <td class="field-label">CNIC:</td>
            <td class="field-value"></td>
            <td class="field-label">Contact:</td>
            <td class="field-value">{{ $agentContact ?? '—' }}</td>
        </tr>
    </table>

    <h2 class="section">1. Property Details</h2>

    <table class="field-row">
        <tr>
            <td class="field-label">Property Address:</td>
            <td class="field-value">{{ $propertyAddress ?? '—' }}</td>
        </tr>
        <tr>
            <td class="field-label">Property Type:</td>
            <td class="field-value">{{ $propertyType ?? '—' }}</td>
        </tr>
        <tr>
            <td class="field-label">Property Size:</td>
            <td class="field-value">{{ $propertySize ?? '—' }}</td>
        </tr>
    </table>

    <h2 class="section">2. Sale Price</h2>

    <p>The Seller agrees to sell the above-mentioned property to the Buyer for a total agreed price of:</p>

    <table class="field-row">
        <tr>
            <td class="field-label">PKR</td>
            <td class="field-value">{{ $salePrice }}</td>
        </tr>
    </table>

    <p>The payment shall be made according to the mutually agreed terms between the Seller and Buyer.</p>

    <h2 class="section">3. Terms &amp; Conditions</h2>

    <ul class="terms">
        <li>The Seller confirms that the property belongs to the Seller and is available for sale.</li>
        <li>The Seller agrees to provide the necessary ownership and property documents to the Buyer.</li>
        <li>The Buyer agrees to pay the agreed purchase price according to the agreed payment schedule.</li>
        <li>Both parties agree to complete the required legal and registration formalities.</li>
        <li>Any outstanding dues or liabilities relating to the property shall be settled by the responsible party as mutually agreed.</li>
        <li>Any changes to this Agreement must be mutually agreed upon by both parties in writing.</li>
        <li>In case of any dispute, both parties shall first attempt to resolve the matter mutually and according to applicable law.</li>
    </ul>

    <h2 class="section">4. Declaration</h2>

    <p>Both Seller and Buyer confirm that they have read and understood the terms of this Agreement and voluntarily agree to them.</p>

    <p><strong>Agreement Date:</strong> {{ $agreementDate }}</p>

    <table class="declaration">
        <tr>
            <td>
                <div class="party-heading">Seller</div>
                <div class="sig-row">
                    <span class="sig-label">Name:</span>
                    {{ $sellerName ?? '—' }}
                </div>
                <div class="sig-row">
                    <span class="sig-label">Signature:</span>
                    <span class="sig-line">
                        @if ($sellerSignatureDataUri)
                            <img class="signature" src="{{ $sellerSignatureDataUri }}" alt="Seller signature">
                        @endif
                    </span>
                </div>
                <div class="sig-row">
                    <span class="sig-label">Date:</span>
                    {{ $sellerDate ?? '—' }}
                </div>
            </td>
            <td>
                <div class="party-heading">Agent</div>
                <div class="sig-row">
                    <span class="sig-label">Name:</span>
                    {{ $agentName ?? '—' }}
                </div>
                <div class="sig-row">
                    <span class="sig-label">Signature:</span>
                    <span class="sig-line">
                        @if ($agentSignatureDataUri)
                            <img class="signature" src="{{ $agentSignatureDataUri }}" alt="Agent signature">
                        @endif
                    </span>
                </div>
                <div class="sig-row">
                    <span class="sig-label">Date:</span>
                    {{ $agentDate ?? '—' }}
                </div>
            </td>
        </tr>
    </table>
</body>
</html>
