@component('mail::message')
# Hi {{ $name }},<br>

Here's your 6 digit pin to activate your account

{{ $pin }}<br>

Thanks,<br>
{{ config('app.name') }}
@endcomponent
