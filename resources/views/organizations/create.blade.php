<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display font-semibold text-xl text-ink">Register Organization</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-sand-200 p-6">

                <form method="POST" action="{{ route('organizations.store') }}">
                    @csrf

                    <div>
                        <x-input-label for="name" value="Organization Name" />
                        <x-text-input id="name" name="name" type="text" class="block mt-1 w-full" :value="old('name')" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="type" value="Type" />
                        <select id="type" name="type" class="block mt-1 w-full border-sand-200 text-ink focus:border-teal-700 focus:ring-teal-700 rounded-md shadow-sm" required>
                            <option value="church" {{ old('type') === 'church' ? 'selected' : '' }}>Church</option>
                            <option value="mosque" {{ old('type') === 'mosque' ? 'selected' : '' }}>Mosque</option>
                            <option value="community" {{ old('type') === 'community' ? 'selected' : '' }}>Community</option>
                        </select>
                        <x-input-error :messages="$errors->get('type')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="address" value="Address" />
                        <x-text-input id="address" name="address" type="text" class="block mt-1 w-full" :value="old('address')" />
                        <x-input-error :messages="$errors->get('address')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="description" value="Description" />
                        <textarea id="description" name="description" rows="4" class="block mt-1 w-full border-sand-200 text-ink focus:border-teal-700 focus:ring-teal-700 rounded-md shadow-sm">{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <x-primary-button>Register Organization</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
