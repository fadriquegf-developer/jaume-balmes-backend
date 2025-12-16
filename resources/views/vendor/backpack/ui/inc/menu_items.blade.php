{{-- This file is used for menu items by any Backpack v7 theme --}}
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i>
        {{ trans('backpack::base.dashboard') }}</a></li>

<x-backpack::menu-dropdown title="{{ __('open_doors.sessions') }}" icon="la la-door-open">
    <x-backpack::menu-dropdown-item title="{{ __('open_doors.sessions') }}" icon="la la-calendar" :link="backpack_url('open-door-session')" />
    <x-backpack::menu-dropdown-item title="{{ __('open_doors.registrations') }}" icon="la la-users" :link="backpack_url('open-door-registration')" />
</x-backpack::menu-dropdown>
