<x-mail::message>
    # {{ __('open_doors.email_reminder_greeting', ['name' => $registration->tutor_name]) }}

    {{ __('open_doors.email_reminder_intro', ['days' => now()->diffInDays($session->session_date)]) }}

    <x-mail::panel>
        **{{ __('open_doors.email_session_details') }}**

        📅 **{{ __('open_doors.session_date') }}:** {{ $session->session_date->format('d/m/Y') }}

        🕐 **{{ __('open_doors.email_time') }}:** {{ substr($session->start_time, 0, 5) }} -
        {{ substr($session->end_time, 0, 5) }}

        📍 **{{ __('open_doors.email_location') }}:** Centre d'Estudis Jaume Balmes

        👤 **{{ __('open_doors.email_student') }}:** {{ $registration->student_full_name }}
    </x-mail::panel>

    {{ __('open_doors.email_reminder_cancel_text') }}

    <x-mail::button :url="$cancelUrl" color="gray">
        {{ __('open_doors.email_cancel_button') }}
    </x-mail::button>

    {{ __('open_doors.email_thanks') }}

    **Centre d'Estudis Jaume Balmes**
</x-mail::message>
