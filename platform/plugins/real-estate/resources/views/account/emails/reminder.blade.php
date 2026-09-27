{{--
    Password-reset email, shared by both Account::sendPasswordResetNotification()
    and Member::sendPasswordResetNotification() via ResetPasswordNotification.
    Rewritten to match the visual style already established for the wizard/
    contract emails (see resources/email-templates/*.tpl): dark navy header
    band, gold accent pill CTA, Georgia headline, light footer disclaimer.
    Self-contained inline CSS (no EmailHandler {{ header }}/{{ footer }} tokens).
--}}
<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f6f2ea; font-family: Arial, Helvetica, sans-serif;">
    <tr>
        <td align="center" style="padding: 40px 20px;">
            <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius: 12px; overflow: hidden;">
                <tr>
                    <td style="background-color:#1a1d24; padding: 32px 40px;">
                        <span style="display:inline-block; color:#e0a63e; font-size: 12px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; border: 1px solid #e0a63e; border-radius: 999px; padding: 4px 12px;">PASSWORD RESET</span>
                        <h1 style="color:#ffffff; font-size: 21px; margin: 16px 0 0; font-family: Georgia, 'Times New Roman', serif; font-weight: normal;">Reset your password</h1>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 36px 40px 8px;">
                        <p style="font-size:15px; color:#1a1d24; margin: 0 0 18px;">Hello,</p>
                        <p style="font-size:15px; color:#333333; line-height:1.7; margin: 0 0 28px;">
                            We received a request to reset the password for your GEMlisting account. Click the button below to choose a new password. This link will expire in {{ config('auth.passwords.members.expire', 60) }} minutes.
                        </p>
                        <table cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="border-radius: 999px; background-color:#e0a63e;">
                                    <a href="{{ $link }}" style="display:inline-block; padding:13px 30px; color:#ffffff; font-size:14px; font-weight:600; text-decoration:none; font-family: Arial, Helvetica, sans-serif;">Reset Password</a>
                                </td>
                            </tr>
                        </table>
                        <p style="font-size:13px; color:#767676; line-height:1.6; margin: 24px 0 0;">
                            If you didn't request a password reset, you can safely ignore this email &mdash; your password will not be changed.
                        </p>
                        <p style="font-size:12px; color:#9a9a9a; line-height:1.6; margin: 16px 0 0;">
                            If the button above doesn't work, copy and paste this URL into your browser:<br>
                            <a href="{{ $link }}" style="color:#9a9a9a;">{{ $link }}</a>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 28px 40px 32px;">
                        <p style="font-size:13px; color:#333333; margin:0;">Best regards,<br/>The GEMlisting Team</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 18px 40px; border-top:1px solid #e6e2d8; background-color:#faf8f4;">
                        <p style="font-size:12px; color:#6b7280; margin:0;">This is an automated message from GEMlisting regarding your account security.</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
