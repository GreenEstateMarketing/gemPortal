<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 26px 40px; }
        body { font-family: Helvetica, Arial, sans-serif; font-size: 10.5px; color: #222222; }
        .logo { width: 120px; margin-bottom: 6px; }
        h1.title {
            color: #1e6b3a;
            font-size: 17px;
            letter-spacing: 0.5px;
            margin: 0 0 10px;
        }
        h2.section {
            font-size: 12px;
            margin: 12px 0 5px;
            border-bottom: 1px solid #dddddd;
            padding-bottom: 3px;
        }
        p { line-height: 1.45; margin: 0 0 5px; }
        table.field-row { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        table.field-row td.field-label { width: 130px; font-weight: bold; vertical-align: top; padding: 2px 0; }
        table.field-row td.field-value { border-bottom: 1px solid #999999; padding: 2px 0; }
        ul.terms { margin: 0 0 5px; padding-left: 16px; }
        ul.terms li { margin-bottom: 3px; line-height: 1.4; }
        table.agent-inline { width: 100%; border-collapse: collapse; margin: 6px 0; }
        table.agent-inline td.field-label { width: 90px; font-weight: bold; vertical-align: top; padding: 4px 0; }
        table.agent-inline td.field-value { border-bottom: 1px solid #999999; padding: 4px 0; }
        table.agent-inline img.signature { max-height: 28px; max-width: 140px; }
        table.declaration { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        table.declaration td { width: 50%; vertical-align: top; padding-top: 6px; }
        table.declaration .party-heading { font-weight: bold; font-size: 12px; margin-bottom: 5px; }
        table.declaration .sig-row { margin-bottom: 6px; }
        table.declaration .sig-label { font-weight: bold; display: block; margin-bottom: 1px; }
        table.declaration .sig-line { border-bottom: 1px solid #999999; min-height: 28px; display: block; }
        table.declaration img.signature { max-height: 26px; max-width: 150px; }
    </style>
</head>
<body>
    @if ($logoDataUri)
        <img class="logo" src="{{ $logoDataUri }}" alt="GEM">
    @endif

    <h1 class="title">GEM &ndash; SELLER AND AGENT AGREEMENT</h1>

    <p>This Digital Agreement is made between GEM and the Seller/Lessor for the listing and advertisement of the property on the GEM Portal.</p>

    <h2 class="section">1. Parties</h2>

    <table class="field-row">
        <tr>
            <td class="field-label">Seller / Lessor:</td>
            <td class="field-value">{{ $sellerName ?? '—' }}</td>
        </tr>
        <tr>
            <td class="field-label">CNIC / ID:</td>
            <td class="field-value"></td>
        </tr>
        <tr>
            <td class="field-label">Contact:</td>
            <td class="field-value">{{ $sellerContact ?? '—' }}</td>
        </tr>
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

    <h2 class="section">2. Advertisement &amp; Property Declaration</h2>

    <ul class="terms">
        <li>The Seller/Lessor confirms that the information, images, documents, and other advertisement content submitted for the above-mentioned property are accurate and genuine to the best of their knowledge.</li>
        <li>The Seller/Lessor confirms that they have the legal authority/right to list the property for advertisement through the GEM Portal.</li>
        <li>Where applicable, the Seller/Lessor agrees to provide valid ownership/title and other required property documents for verification.</li>
    </ul>

    <h2 class="section">3. GEM Advertisement Service</h2>

    <ul class="terms">
        <li>GEM agrees to process and publish the property's advertisement on its Portal after completion of the required content, accuracy, genuineness, and/or title verification process.</li>
        <li>The Seller/Lessor agrees to proceed with the applicable advertisement payment after the required verification and completion of the Property Addition Workflow.</li>
    </ul>

    <h2 class="section">4. Digital Signature &amp; Agreement</h2>

    <p>This Agreement shall be digitally signed by the Seller/Lessor through the GEM Portal using one of the approved digital-signing methods:</p>

    <ul class="terms">
        <li>Uploading a signed image/photo, with automatic background removal and cropping; or</li>
        <li>Drawing the signature directly in the browser using the provided signature tool.</li>
    </ul>

    <p>The Seller/Lessor's digital signature shall be securely maintained by GEM for future verification and authorized signing purposes.</p>

    <p>The authorized GEM Agent's digital signature shall automatically appear below as:</p>

    <table class="agent-inline">
        <tr>
            <td class="field-label">Agent Name:</td>
            <td class="field-value" style="width: 40%;">{{ $agentName ?? '—' }}</td>
            <td class="field-label" style="width: 70px;">Signature:</td>
            <td class="field-value">
                @if ($agentSignatureDataUri)
                    <img class="signature" src="{{ $agentSignatureDataUri }}" alt="Agent signature">
                @endif
            </td>
        </tr>
    </table>

    <p>For and on behalf of GEM</p>

    <h2 class="section">5. Confirmation</h2>

    <p>By signing this Agreement, the Seller/Lessor confirms that they have read, understood, and accepted the terms of this Digital Agreement and authorize GEM to proceed with the applicable advertisement workflow.</p>

    <p><strong>Agreement Date:</strong> {{ $agreementDate }}</p>

    <table class="declaration">
        <tr>
            <td>
                <div class="party-heading">Seller / Lessor</div>
                <div class="sig-row">
                    <span class="sig-label">Name:</span>
                    {{ $sellerName ?? '—' }}
                </div>
                <div class="sig-row">
                    <span class="sig-label">Digital Signature:</span>
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
                <div class="party-heading">For &amp; on behalf of GEM</div>
                <div class="sig-row">
                    <span class="sig-label">Agent Name:</span>
                    {{ $agentName ?? '—' }}
                </div>
                <div class="sig-row">
                    <span class="sig-label">Digital Signature:</span>
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
