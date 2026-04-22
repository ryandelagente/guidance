<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Test</h2>
            <a href="{{ route('psych-tests.show', $psychTest) }}" class="text-sm text-gray-500 hover:text-gray-700">← Back</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">

                @if($errors->any())
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('psych-tests.update', $psychTest) }}" class="space-y-5">
                    @csrf @method('PATCH')

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Test Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $psychTest->name) }}" required maxlength="200"
                               class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Test Type <span class="text-red-500">*</span></label>
                            <select name="test_type" required class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                                @foreach(['iq','personality','career_aptitude','interest','mental_health','other'] as $t)
                                    <option value="{{ $t }}" @selected(old('test_type',$psychTest->test_type) === $t)>
                                        {{ ucwords(str_replace('_',' ',$t)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <input type="text" name="category" value="{{ old('category', $psychTest->category) }}" maxlength="100"
                                   class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Total Items</label>
                            <input type="number" name="total_items" value="{{ old('total_items', $psychTest->total_items) }}"
                                   min="1" max="9999" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Publisher</label>
                            <input type="text" name="publisher" value="{{ old('publisher', $psychTest->publisher) }}" maxlength="200"
                                   class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Edition Year</label>
                            <input type="number" name="edition_year" value="{{ old('edition_year', $psychTest->edition_year) }}"
                                   min="1900" max="{{ date('Y') + 1 }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="3" maxlength="2000"
                                  class="w-full border-gray-300 rounded-md shadow-sm text-sm">{{ old('description', $psychTest->description) }}</textarea>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" id="is_active" value="1"
                               {{ old('is_active', $psychTest->is_active) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600">
                        <label for="is_active" class="text-sm font-medium text-gray-700">Active</label>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded-lg">
                            Save Changes
                        </button>
                        <a href="{{ route('psych-tests.show', $psychTest) }}"
                           class="text-sm text-gray-600 px-4 py-2 border border-gray-300 rounded-lg">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
