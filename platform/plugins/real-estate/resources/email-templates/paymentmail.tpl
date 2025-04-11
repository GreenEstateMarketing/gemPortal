{{ header }}

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
                                    <p style="font-size: 15px; color: #333333;"><strong>Hello {{ name }},</strong></p>

                                    <p style="font-size: 15px; color: #333333;">
                                        A payment is pending for the property <a href="{{ property_url }}" style="color: #3366cc;">{{ title }}</a>.
                                    </p>

                                    <p style="font-size: 15px; color: #333333;">
                                        You can top up your account balance using the link below:
                                        <br>
                                        <a href="{{ credits_url }}" style="color: #3366cc;">View credit options</a>
                                    </p>

                                    <p style="font-size: 15px; color: #333333;">Thank you,<br>The GEM Listing Team</p>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    {{ footer }}
