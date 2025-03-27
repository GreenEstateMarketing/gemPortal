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
                                    <p><strong>Hello {{ name }}</strong></p>
                                    <p>This property <a href="{{ property_url }}">{{ title }}</a> has been {{ action }} by {{ by }}.</p>
                                    <p>Thanks</p>
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
