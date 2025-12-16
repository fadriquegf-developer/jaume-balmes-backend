<x-mail::message>
    # {{ __('open_doors.email_greeting', ['name' => $registration->tutor_name]) }}

    {{ __('open_doors.email_confirmation_intro') }}

    <x-mail::panel>
        **{{ __('open_doors.email_session_details') }}**

        📅 **{{ __('open_doors.session_date') }}:** {{ $session->session_date->format('d/m/Y') }}

        🕐 **{{ __('open_doors.email_time') }}:** {{ substr($session->start_time, 0, 5) }} -
        {{ substr($session->end_time, 0, 5) }}

        📍 **{{ __('open_doors.email_location') }}:** Centre d'Estudis Jaume Balmes

        👤 **{{ __('open_doors.email_student') }}:** {{ $registration->student_full_name }}
    </x-mail::panel>

    {{ __('open_doors.email_confirm_text') }}

    <x-mail::button :url="$confirmUrl" color="success">
        {{ __('open_doors.email_confirm_button') }}
    </x-mail::button>

    {{ __('open_doors.email_cancel_text') }}

    <x-mail::button :url="$cancelUrl" color="gray">
        {{ __('open_doors.email_cancel_button') }}
    </x-mail::button>

    ---

    **{{ __('open_doors.email_important') }}**

    - {{ __('open_doors.email_tip_arrive') }}
    - {{ __('open_doors.email_tip_parking') }}
    - {{ __('open_doors.email_tip_questions') }}

    {{ __('open_doors.email_thanks') }}

    **Centre d'Estudis Jaume Balmes**<br>
    {{ __('open_doors.email_contact') }}: info@jaumebalmes.com
</x-mail::message>
