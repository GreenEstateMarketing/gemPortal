<table width="100%">
    <tbody>
    <tr>
        <td class="wrapper" width="700" align="center">
            <table class="section" cellpadding="0" cellspacing="0" width="700" bgcolor="#f8f8f8">
                <tr>
                    <td class="column" align="left">
                        <table>
                            <tbody>
                            <tr>
                                <td align="left" style="padding: 20px 50px;">
                                    <p style="font-size: 15px; color: #333;"><strong>Hello {{ name }},</strong></p>

                                    <p style="font-size: 15px; color: #333;">
                                        Your property titled <a href="{{ property_url }}" style="color: #3366cc;">{{ title }}</a> has bee expired. You have no more credits available in your account.
                                    </p>

                                    <p style="font-size: 15px; color: #333;">
                                        You can purchase credits from:
                                        <a href="{{ credits_url }}" style="color: #3366cc;">Dashboard</a>.
                                    </p>

                                    <p style="font-size: 15px; color: #333;">
                                        Thank you,<br>The GEM Listing Team
                                    </p>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    </tbody>
</table>
