{!! EmailHandler::prepareData(str_replace('{{ verification_link }}', $link, '

{{ header }}

<strong>Welcome to GEM!</strong><br><br>

Thank you for creating your account.

Please verify your email address by clicking the button below.

<br><br>

<a href="{{ verification_link }}">Verify Email</a>

<br><br>

If you did not create this account, please ignore this email.

<br><br>

Regards,<br>

<strong>{{ site_title }}</strong>

<hr>

If you’re having trouble clicking the "Verify Email" button, copy and paste the URL below into your browser:

{{ verification_link }}

{{ footer }}

')) !!}