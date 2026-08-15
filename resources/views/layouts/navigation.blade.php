<nav x-data="{ open: false }" class="bg-teal-900 border-b border-teal-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center gap-2">
                    <a href="{{ route('dashboard') }}" class="font-display font-bold text-lg text-sand-50 tracking-tight">Community Org</a>
                </div>
                <div class="hidden space-x-1 sm:ms-10 sm:flex sm:items-center">
                    <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-teal-800 text-gold-500' : 'text-sand-100 hover:bg-teal-800' }}">Dashboard</a>
                    @if(Auth::user()->is_super_admin)
                        <a href="{{ route('admin.organizations.index') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('admin.*') ? 'bg-teal-800 text-gold-500' : 'text-sand-100 hover:bg-teal-800' }}">Admin</a>
                    @endif
                    @if(Auth::user()->hasOrganization())
                        <a href="{{ optional(Auth::user()->approvedMembership)->role === 'admin' ? route('members.index') : route('members.directory') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('members.*') ? 'bg-teal-800 text-gold-500' : 'text-sand-100 hover:bg-teal-800' }}">Members</a>
                        <a href="{{ route('events.index') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('events.*') ? 'bg-teal-800 text-gold-500' : 'text-sand-100 hover:bg-teal-800' }}">Events</a>
                        <a href="{{ route('announcements.index') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('announcements.*') ? 'bg-teal-800 text-gold-500' : 'text-sand-100 hover:bg-teal-800' }}">Announcements</a>
                        <a href="{{ route('contributions.index') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('contributions.*') ? 'bg-teal-800 text-gold-500' : 'text-sand-100 hover:bg-teal-800' }}">Contributions</a>
                    @else
                        <a href="{{ route('organizations.index') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('organizations.*') ? 'bg-teal-800 text-gold-500' : 'text-sand-100 hover:bg-teal-800' }}">Join an Organization</a>
                    @endif
                </div>
            </div>
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-2">
                @include('layouts.notification-bell')
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-sand-100 hover:bg-teal-800 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('organizations.index')">Browse Organizations</x-dropdown-link>
                        <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>
                        @if(Auth::user()->hasOrganization() && optional(Auth::user()->approvedMembership)->role === 'admin')
                            <x-dropdown-link :href="route('organizations.payout.edit')">Payout Settings</x-dropdown-link>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Log Out') }}</x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
            <div class="-me-2 flex items-center gap-1 sm:hidden">
                @include('layouts.notification-bell')
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-sand-100 hover:bg-teal-800 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-teal-900">
        <div class="pt-2 pb-3 space-y-1 px-2">
            <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('dashboard') ? 'bg-teal-800 text-gold-500' : 'text-sand-100' }}">Dashboard</a>
            @if(Auth::user()->is_super_admin)
                <a href="{{ route('admin.organizations.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('admin.*') ? 'bg-teal-800 text-gold-500' : 'text-sand-100' }}">Admin</a>
            @endif
            @if(Auth::user()->hasOrganization())
                <a href="{{ optional(Auth::user()->approvedMembership)->role === 'admin' ? route('members.index') : route('members.directory') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('members.*') ? 'bg-teal-800 text-gold-500' : 'text-sand-100' }}">Members</a>
                <a href="{{ route('events.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('events.*') ? 'bg-teal-800 text-gold-500' : 'text-sand-100' }}">Events</a>
                <a href="{{ route('announcements.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('announcements.*') ? 'bg-teal-800 text-gold-500' : 'text-sand-100' }}">Announcements</a>
                <a href="{{ route('contributions.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('contributions.*') ? 'bg-teal-800 text-gold-500' : 'text-sand-100' }}">Contributions</a>
            @else
                <a href="{{ route('organizations.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('organizations.*') ? 'bg-teal-800 text-gold-500' : 'text-sand-100' }}">Join an Organization</a>
            @endif
        </div>
        <div class="pt-4 pb-3 border-t border-teal-800 px-4">
            <div class="font-medium text-base text-sand-50">{{ Auth::user()->name }}</div>
            <div class="font-medium text-sm text-sand-200">{{ Auth::user()->email }}</div>
            <div class="mt-3 space-y-1">
                <a href="{{ route('organizations.index') }}" class="block text-sand-100">Browse Organizations</a>
                <a href="{{ route('profile.edit') }}" class="block text-sand-100">Profile</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="block text-sand-100">Log Out</a>
                </form>
            </div>
        </div>
    </div>
</nav>









