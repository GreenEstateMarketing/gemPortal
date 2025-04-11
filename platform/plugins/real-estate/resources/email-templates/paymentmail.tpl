{{$header}}

<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f8f8f8; font-family: Arial, sans-serif;">
    <tr>
        <td align="center">
            <table width="700" cellpadding="0" cellspacing="0" style="background-color:#ffffff;">
                <tr>
                    <td style="padding: 20px 40px; text-align: left;">
                        <p style="font-size: 16px; color: #333;"><strong>Hello {{ $name }}</strong>,</p>
                        <p style="font-size: 15px; color: #333;">
                            Your payment for the property <a href="{{ $property_url }}" style="color: #3366cc;">{{ $title }}</a> is currently pending.
                        </p>
                        <p style="font-size: 15px; color: #333;">
                            You can easily purchase credits by clicking <a href="{{ $credits_url }}" style="color: #3366cc;">here</a>.
                        </p>
                        <p style="font-size: 15px; color: #333;">Thanks,<br/>The GEM Team</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

{{ $footer }}
