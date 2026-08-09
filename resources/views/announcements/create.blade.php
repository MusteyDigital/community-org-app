<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display font-semibold text-xl text-ink">Add Announcement</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-sand-200 p-6">
                <form method="POST" action="{{ route('announcements.store') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-ink mb-1">Title</label>
                        <input type="text" name="title" value="{{ old('title') }}" class="w-full border-sand-200 rounded-lg focus:border-teal-700 focus:ring-teal-700" required>
                    </div>
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-ink mb-1">Type</label>
                        <select name="type" class="w-full border-sand-200 rounded-lg focus:border-teal-700 focus:ring-teal-700" required>
                            <option value="general" {{ old('type') === 'general' ? 'selected' : '' }}>General Announcement</option>
                            <option value="burial" {{ old('type') === 'burial' ? 'selected' : '' }}>Burial Notice</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-ink mb-1">Body</label>
                        <textarea name="body" class="w-full border-sand-200 rounded-lg focus:border-teal-700 focus:ring-teal-700" rows="5" required>{{ old('body') }}</textarea>
                    </div>
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-ink mb-1">Publish Date</label>
                        <input type="datetime-local" name="published_at" value="{{ old('published_at') }}" class="w-full border-sand-200 rounded-lg focus:border-teal-700 focus:ring-teal-700">
                    </div>
                    <div class="mb-6 flex items-center">
                        <input type="checkbox" name="is_pinned" value="1" id="is_pinned" class="mr-2 rounded border-sand-300 text-gold-600 focus:ring-gold-600">
                        <label for="is_pinned" class="text-sm text-ink">Pin this announcement</label>
                    </div>
                    @error('title')<p class="text-clay-600 text-sm mb-2">{{ $message }}</p>@enderror
                    @error('type')<p class="text-clay-600 text-sm mb-2">{{ $message }}</p>@enderror
                    @error('body')<p class="text-clay-600 text-sm mb-2">{{ $message }}</p>@enderror
                    <div class="flex gap-3">
                        <button type="submit" class="px-4 py-2 bg-gold-600 hover:bg-gold-700 text-white text-sm font-medium rounded-lg">Save</button>
                        <a href="{{ route('announcements.index') }}" class="px-4 py-2 text-teal-800 text-sm font-medium">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
