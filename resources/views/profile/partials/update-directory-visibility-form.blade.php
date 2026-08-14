<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Directory Visibility') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            {{ __('Control whether other members can see you in your organization\'s member directory.') }}
        </p>
    </header>
    <form method="post" action="{{ route('members.visibility') }}" class="mt-6 space-y-6">
        @csrf
        <div class="flex items-center gap-3">
            <input type="hidden" name="is_listed" value="0">
            <input type="checkbox" id="is_listed" name="is_listed" value="1" {{ optional($membership)->is_listed ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
            <label for="is_listed" class="text-sm text-gray-700">{{ __('Show me in the member directory') }}</label>
        </div>
        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>
            @if (session('status') === 'directory-visibility-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
