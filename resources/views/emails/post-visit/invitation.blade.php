<x-mail::message>
    # {{ __('post_visit.email_greeting', ['name' => $registration->tutor_name]) }}

    {{ __('post_visit.email_intro') }}

    **{{ __('post_visit.email_session_info') }}**
    - **{{ __('post_visit.email_date') }}:** {{ $session->session_date->format('d/m/Y') }}
    - **{{ __('post_visit.email_student') }}:** {{ $registration->student_full_name }}

    {{ __('post_visit.email_request') }}

    <x-mail::button :url="$surveyUrl" color="success">
        {{ __('post_visit.email_button') }}
    </x-mail::button>

    {{ __('post_visit.email_time_notice') }}

    {{ __('post_visit.email_thanks') }}

    {{ config('app.name') }}
</x-mail::message>
