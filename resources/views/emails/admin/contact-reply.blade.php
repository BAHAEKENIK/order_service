@component('mail::message')
Hello {{ $recipientName }},

This is a reply regarding your inquiry:

{{ $content }}

Thanks,<br>
{{ config('app.name') }} Support
@endcomponent
