<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('resources.show', $resource) }}" class="text-gray-400 hover:text-gray-600">← Back</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Resource</h2>
        </div>
    </x-slot>
    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('resources.update', $resource) }}" enctype="multipart/form-data" class="bg-white shadow-sm rounded-lg p-6 space-y-4">
                @method('PUT')
                @include('resources._form')
                <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                    <a href="{{ route('resources.show', $resource) }}" class="text-sm px-4 py-2 text-gray-600">Cancel</a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded-md">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
