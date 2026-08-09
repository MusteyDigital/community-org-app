<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display font-semibold text-xl text-ink">Add Event</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-sand-200 p-6">
                <form method="POST" action="{{ route('events.store') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-ink mb-1">Title</label>
                        <input type="text" name="title" value="{{ old('title') }}" class="w-full border-sand-200 rounded-lg focus:border-teal-700 focus:ring-teal-700" required>
                    </div>
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-ink mb-1">Description</label>
                        <textarea name="description" class="w-full border-sand-200 rounded-lg focus:border-teal-700 focus:ring-teal-700">{{ old('description') }}</textarea>
                    </div>
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-ink mb-1">Date</label>
                        <input type="date" name="event_date" value="{{ old('event_date') }}" class="w-full border-sand-200 rounded-lg focus:border-teal-700 focus:ring-teal-700" required>
                    </div>
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-ink mb-1">Time</label>
                        <input type="time" name="event_time" value="{{ old('event_time') }}" class="w-full border-sand-200 rounded-lg focus:border-teal-700 focus:ring-teal-700">
                    </div>
                    <div class="mb-6">
                        <label class="block font-medium text-sm text-ink mb-1">Location</label>
                        <input type="text" name="location" value="{{ old('location') }}" class="w-full border-sand-200 rounded-lg focus:border-teal-700 focus:ring-teal-700">
                    </div>
                    @error('title')<p class="text-clay-600 text-sm mb-2">{{ $message }}</p>@enderror
                    @error('event_date')<p class="text-clay-600 text-sm mb-2">{{ $message }}</p>@enderror
                    <div class="flex gap-3">
                        <button type="submit" class="px-4 py-2 bg-gold-600 hover:bg-gold-700 text-white text-sm font-medium rounded-lg">Save</button>
                        <a href="{{ route('events.index') }}" class="px-4 py-2 text-teal-800 text-sm font-medium">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
