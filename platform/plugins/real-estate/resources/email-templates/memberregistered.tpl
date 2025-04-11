{!! $header !!}

<table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f8f8f8; font-family: Arial, sans-serif;">
    <tr>
        <td align="center">
            <table width="700" cellpadding="0" cellspacing="0" style="background-color: #ffffff;">
                <tr>
                    <td style="padding: 20px 50px; text-align: left;">
                        <p style="font-size: 16px; color: #333333;">
                            <strong>Hello {{ $member_name }},</strong>
                        </p>

                        <p style="font-size: 15px; color: #333333;">
                            Welcome to <strong>GEM</strong>! Your account has been successfully created.
                        </p>

                        <p style="font-size: 15px; color: #333333;">
                            You can log in to your account using the following link:<br/>
                            <a href="{{ $login_url }}" style="color: #3366cc;">Access your account</a>
                        </p>

                        <p style="font-size: 15px; color: #333333;">
                            If you didn’t sign up for this account, please ignore this email.
                        </p>

                        <p style="font-size: 15px; color: #333333;">
                            Best regards,<br/>
                            The GEM Team
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

{!! $footer !!}
