{!! $header !!}

<table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f8f8f8; font-family: Arial, sans-serif;">
    <tr>
        <td align="center">
            <table width="700" cellpadding="0" cellspacing="0" style="background-color: #ffffff;">
                <tr>
                    <td style="padding: 20px 50px; text-align: left;">
                        <p style="font-size: 16px; color: #333333;">
                            <strong>Hello, you have received a new consultation request from {{ $site_title }}:</strong>
                        </p>

                        <p style="font-size: 15px; color: #333333;">
                            <img src="{{ $site_url }}/vendor/core/core/base/images/emails/person.png" alt="Name" width="16" style="vertical-align: middle; margin-right: 8px;" />
                            <strong>Name:</strong> {{ $consult_name }}
                        </p>

                        <p style="font-size: 15px; color: #333333;">
                            <img src="{{ $site_url }}/vendor/core/core/base/images/emails/email.png" alt="Email" width="16" style="vertical-align: middle; margin-right: 8px;" />
                            <strong>Email:</strong> {{ $consult_email }}
                        </p>

                        <p style="font-size: 15px; color: #333333;">
                            <img src="{{ $site_url }}/vendor/core/core/base/images/emails/phone.png" alt="Phone" width="16" style="vertical-align: middle; margin-right: 8px;" />
                            <strong>Phone:</strong> {{ $consult_phone }}
                        </p>

                        <p style="font-size: 15px; color: #333333;">
                            <img src="{{ $site_url }}/vendor/core/core/base/images/emails/phone.png" alt="Link" width="16" style="vertical-align: middle; margin-right: 8px;" />
                            <strong>Subject:</strong> <a href="{{ $consult_link }}" style="color: #3366cc;">{{ $consult_subject }}</a>
                        </p>

                        <p style="font-size: 15px; color: #333333;">
                            <img src="{{ $site_url }}/vendor/core/core/base/images/emails/message.png" alt="Message" width="16" style="vertical-align: middle; margin-right: 8px;" />
                            <strong>Message:</strong> {{ $consult_content }}
                        </p>

                        <p style="font-size: 14px; color: #666666;">
                            Please follow up with this contact at your earliest convenience.
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

{!! $footer !!}
