{{-- Dashboard General --}}
<x-backpack::menu-item title="{{ __('menu.dashboard') }}" icon="la la-home" :link="backpack_url('dashboard')" />

<x-backpack::menu-separator title="{{ __('menu.modules') }}" />

{{-- MÒDUL 1: Chatbots --}}
<x-backpack::menu-dropdown title="{{ __('menu.chatbots') }}" icon="la la-comments">
    <x-backpack::menu-dropdown-item title="{{ __('menu.chatbot_contexts') }}" icon="la la-file-alt" :link="backpack_url('chatbot-context')" />
    <x-backpack::menu-dropdown-item title="{{ __('menu.chatbot_config') }}" icon="la la-cog" :link="backpack_url('chatbot-config')" />
    <x-backpack::menu-dropdown-item title="{{ __('menu.chatbot_analytics') }}" icon="la la-chart-bar"
        :link="backpack_url('chatbot-analytics')" />
</x-backpack::menu-dropdown>

{{-- MÒDUL 2: Inscripcions / Portes Obertes --}}
<x-backpack::menu-dropdown title="{{ __('menu.inscriptions') }}" icon="la la-door-open">
    <x-backpack::menu-dropdown-item title="{{ __('menu.inscriptions_dashboard') }}" icon="la la-tachometer-alt"
        :link="backpack_url('open-doors/dashboard')" />
    <x-backpack::menu-dropdown-item title="{{ __('menu.sessions') }}" icon="la la-calendar" :link="backpack_url('open-door-session')" />
    <x-backpack::menu-dropdown-item title="{{ __('menu.registrations') }}" icon="la la-users" :link="backpack_url('open-door-registration')" />
    <x-backpack::menu-dropdown-item title="{{ __('menu.post_visit') }}" icon="la la-clipboard-check"
        :link="backpack_url('post-visit-survey')" />
    <x-backpack::menu-dropdown-item title="{{ __('menu.conversion_analysis') }}" icon="la la-chart-pie"
        :link="backpack_url('conversion-analysis')" />
</x-backpack::menu-dropdown>

{{-- MÒDUL 3: Substitucions --}}
<x-backpack::menu-dropdown title="{{ __('menu.substitutions') }}" icon="la la-user-clock">
    <x-backpack::menu-dropdown-item title="{{ __('menu.substitutions_dashboard') }}" icon="la la-tachometer-alt"
        :link="backpack_url('substitutions/dashboard')" />
    <x-backpack::menu-dropdown-item title="{{ __('menu.pending_absences') }}" icon="la la-exclamation-circle"
        :link="backpack_url('absence')" />
    <x-backpack::menu-dropdown-item title="{{ __('menu.confirmed_substitutions') }}" icon="la la-check-circle"
        :link="backpack_url('substitution')" />
    <x-backpack::menu-dropdown-item title="{{ __('menu.teachers') }}" icon="la la-chalkboard-teacher"
        :link="backpack_url('teacher')" />
</x-backpack::menu-dropdown>

{{-- MÒDUL 4: Satisfacció --}}
<x-backpack::menu-dropdown title="{{ __('menu.satisfaction') }}" icon="la la-smile">
    <x-backpack::menu-dropdown-item title="{{ __('menu.surveys') }}" icon="la la-poll" :link="backpack_url('survey')" />
    <x-backpack::menu-dropdown-item title="{{ __('menu.analysis_by_group') }}" icon="la la-users" :link="backpack_url('survey-analysis-group')" />
    <x-backpack::menu-dropdown-item title="{{ __('menu.global_analysis') }}" icon="la la-globe" :link="backpack_url('survey-analysis-global')" />
</x-backpack::menu-dropdown>

{{-- MÒDUL 5: Reclamacions --}}
<x-backpack::menu-dropdown title="{{ __('menu.complaints') }}" icon="la la-exclamation-triangle">
    <x-backpack::menu-dropdown-item title="{{ __('menu.complaints_dashboard') }}" icon="la la-tachometer-alt"
        :link="backpack_url('complaints/dashboard')" />
    <x-backpack::menu-dropdown-item title="{{ __('menu.pending_complaints') }}" icon="la la-clock"
        :link="backpack_url('complaint')" />
    <x-backpack::menu-dropdown-item title="{{ __('menu.my_cases') }}" icon="la la-user-tag" :link="backpack_url('my-complaints')" />
    <x-backpack::menu-dropdown-item title="{{ __('menu.quarterly_analysis') }}" icon="la la-chart-line"
        :link="backpack_url('complaint-analysis')" />
</x-backpack::menu-dropdown>

<x-backpack::menu-separator title="{{ __('menu.system') }}" />

{{-- Configuració --}}
<x-backpack::menu-dropdown title="{{ __('menu.settings') }}" icon="la la-cogs">
    <x-backpack::menu-dropdown-item title="{{ __('menu.users') }}" icon="la la-users-cog" :link="backpack_url('user')" />
    <x-backpack::menu-dropdown-item title="{{ __('menu.roles') }}" icon="la la-user-shield" :link="backpack_url('role')" />
    <x-backpack::menu-dropdown-item title="{{ __('menu.email_templates') }}" icon="la la-envelope"
        :link="backpack_url('email-template')" />
    <x-backpack::menu-dropdown-item title="{{ __('menu.notifications') }}" icon="la la-bell" :link="backpack_url('notification-setting')" />
    <x-backpack::menu-dropdown-item title="{{ __('menu.logs') }}" icon="la la-history" :link="backpack_url('log')" />
</x-backpack::menu-dropdown>

{{-- Informes --}}
<x-backpack::menu-item title="{{ __('menu.reports') }}" icon="la la-file-pdf" :link="backpack_url('reports')" />
