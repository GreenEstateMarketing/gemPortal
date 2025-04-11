{!! $header !!}

<table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f8f8f8; font-family: Arial, sans-serif;">
    <tr>
        <td align="center">
            <table width="700" cellpadding="0" cellspacing="0" style="background-color: #ffffff;">
                <tr>
                    <td style="padding: 20px 50px; text-align: left;">
                        <p style="font-size: 16px; color: #333333;">
                            <strong>Hello {{ $name }},</strong>
                        </p>
                        <p style="font-size: 15px; color: #333333;">
                            The property titled <a href="{{ $property_url }}" style="color: #3366cc;">{{ $title }}</a> has been <strong>{{ $action }}</strong> by <strong>{{ $by }}</strong>.
                        </p>
                        <p style="font-size: 15px; color: #333333;">
                            If you have any questions or need assistance, feel free to contact us.
                        </p>
                        <p style="font-size: 15px; color: #333333;">
                            Best regards,<br/>The GEM Listing Team
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

{!! $footer !!}
