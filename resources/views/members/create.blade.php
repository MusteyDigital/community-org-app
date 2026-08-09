<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display font-semibold text-xl text-ink">Add Member</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-sand-200 p-6">
                <form method="POST" action="{{ route('members.store') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-ink mb-1">Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="w-full border-sand-200 rounded-lg focus:border-teal-700 focus:ring-teal-700" required>
                    </div>
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-ink mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="w-full border-sand-200 rounded-lg focus:border-teal-700 focus:ring-teal-700" required>
                    </div>
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-ink mb-1">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="w-full border-sand-200 rounded-lg focus:border-teal-700 focus:ring-teal-700">
                    </div>
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-ink mb-1">Role</label>
                        <select name="role" class="w-full border-sand-200 rounded-lg focus:border-teal-700 focus:ring-teal-700">
                            <option value="member">Member</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="mb-6">
                        <label class="block font-medium text-sm text-ink mb-1">Join Date</label>
                        <input type="date" name="join_date" value="{{ old('join_date') }}" class="w-full border-sand-200 rounded-lg focus:border-teal-700 focus:ring-teal-700">
                    </div>
                    @error('name')<p class="text-clay-600 text-sm mb-2">{{ $message }}</p>@enderror
                    @error('email')<p class="text-clay-600 text-sm mb-2">{{ $message }}</p>@enderror
                    <div class="flex gap-3">
                        <button type="submit" class="px-4 py-2 bg-gold-600 hover:bg-gold-700 text-white text-sm font-medium rounded-lg">Save</button>
                        <a href="{{ route('members.index') }}" class="px-4 py-2 text-teal-800 text-sm font-medium">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
