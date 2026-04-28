<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ResourceController extends Controller
{
    public function index(Request $request)
    {
        $isStaff = $request->user()->isStaff();

        $query = Resource::with('author')
            ->orderBy('sort_order')
            ->orderBy('title');

        if (!$isStaff) {
            $query->published();
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('q')) {
            $query->where(fn ($q) => $q
                ->where('title', 'like', '%' . $request->q . '%')
                ->orWhere('description', 'like', '%' . $request->q . '%'));
        }

        // Emergency resources always at top, even if filtered
        $emergency = Resource::published()->emergency()->orderBy('sort_order')->get();
        $resources = $query->paginate(20)->withQueryString();
        $grouped   = $resources->getCollection()->groupBy('category');

        return view('resources.index', compact('resources', 'grouped', 'emergency', 'isStaff'));
    }

    public function create()
    {
        $this->authorizeStaff();
        return view('resources.create');
    }

    public function store(Request $request)
    {
        $this->authorizeStaff();
        $data = $this->validateData($request);
        $data['created_by'] = $request->user()->id;

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('resources', 'public');
        }

        Resource::create($data);
        return redirect()->route('resources.index')->with('success', 'Resource added.');
    }

    public function show(Resource $resource)
    {
        $resource->increment('view_count');
        $resource->load('author');
        return view('resources.show', compact('resource'));
    }

    public function edit(Resource $resource)
    {
        $this->authorizeStaff();
        return view('resources.edit', compact('resource'));
    }

    public function update(Request $request, Resource $resource)
    {
        $this->authorizeStaff();
        $data = $this->validateData($request, $resource->id);

        if ($request->hasFile('file')) {
            if ($resource->file_path) Storage::disk('public')->delete($resource->file_path);
            $data['file_path'] = $request->file('file')->store('resources', 'public');
        }

        $resource->update($data);
        return redirect()->route('resources.index')->with('success', 'Resource updated.');
    }

    public function destroy(Resource $resource)
    {
        $this->authorizeStaff();
        if ($resource->file_path) Storage::disk('public')->delete($resource->file_path);
        $resource->delete();
        return redirect()->route('resources.index')->with('success', 'Resource deleted.');
    }

    private function authorizeStaff(): void
    {
        abort_unless(auth()->user()->isStaff(), 403);
    }

    private function validateData(Request $request, ?int $ignoreId = null): array
    {
        $rules = [
            'title'           => 'required|string|max:200',
            'description'     => 'nullable|string|max:2000',
            'type'            => 'required|in:article,video,hotline,pdf,link,contact',
            'category'        => 'required|in:' . implode(',', array_keys(Resource::CATEGORIES)),
            'url'             => 'nullable|url|max:500',
            'contact_number'  => 'nullable|string|max:80',
            'available_hours' => 'nullable|string|max:200',
            'is_emergency'    => 'sometimes|boolean',
            'is_published'    => 'sometimes|boolean',
            'sort_order'      => 'nullable|integer|min:0|max:9999',
            'file'            => 'nullable|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png',
        ];

        $data = $request->validate($rules);
        $data['is_emergency']  = $request->boolean('is_emergency');
        $data['is_published']  = $request->boolean('is_published', true);
        $data['sort_order']    = $data['sort_order'] ?? 100;

        return $data;
    }
}
