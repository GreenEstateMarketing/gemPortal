<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f6f2ea; font-family: Arial, Helvetica, sans-serif;">
    <tr>
        <td align="center" style="padding: 40px 20px;">
            <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius: 12px; overflow: hidden;">
                <tr>
                    <td style="background-color:#1a1d24; padding: 32px 40px;">
                        <span style="display:inline-block; color:#e0a63e; font-size: 12px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; border: 1px solid #e0a63e; border-radius: 999px; padding: 4px 12px;">SUBMITTED</span>
                        <h1 style="color:#ffffff; font-size: 21px; margin: 16px 0 0; font-family: Georgia, 'Times New Roman', serif; font-weight: normal;">Your property has been submitted</h1>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 36px 40px 8px;">
                        <p style="font-size:15px; color:#1a1d24; margin: 0 0 18px;">Hello {{ recipient_name }},</p>
                        <p style="font-size:15px; color:#333333; line-height:1.7; margin: 0 0 28px;">
                            Great news &mdash; your property listing, <strong>{{ property_title }}</strong>, has been successfully submitted to GEM Listing. Our team will review it, and once an agent is assigned they'll help take it from here. You can track its progress any time from your dashboard.
                        </p>
                        <table cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="border-radius: 999px; background-color:#e0a63e;">
                                    <a href="{{ property_url }}" style="display:inline-block; padding:13px 30px; color:#ffffff; font-size:14px; font-weight:600; text-decoration:none; font-family: Arial, Helvetica, sans-serif;">View Your Listing</a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 28px 40px 32px;">
                        <p style="font-size:13px; color:#333333; margin:0;">Best regards,<br/>The GEM Listing Team</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 18px 40px; border-top:1px solid #e6e2d8; background-color:#faf8f4;">
                        <p style="font-size:12px; color:#6b7280; margin:0;">This is an automated message from GEM Listing regarding property "{{ property_title }}".</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
