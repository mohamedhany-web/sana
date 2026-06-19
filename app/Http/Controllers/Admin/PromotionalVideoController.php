<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromotionalVideo;
use App\Support\YouTubeUrl;
use Illuminate\Http\Request;

class PromotionalVideoController extends Controller
{
    public function index(Request $request)
    {
        $videos = PromotionalVideo::query()
            ->ordered()
            ->paginate(15);

        return view('admin.marketing.promotional-videos.index', compact('videos'));
    }

    public function create()
    {
        return view('admin.marketing.promotional-videos.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateVideo($request);

        PromotionalVideo::create($validated);

        return redirect()->route('admin.promotional-videos.index')
            ->with('success', 'تم إضافة الفيديو الدعائي بنجاح');
    }

    public function edit(PromotionalVideo $promotionalVideo)
    {
        return view('admin.marketing.promotional-videos.edit', compact('promotionalVideo'));
    }

    public function update(Request $request, PromotionalVideo $promotionalVideo)
    {
        $validated = $this->validateVideo($request);

        $promotionalVideo->update($validated);

        return redirect()->route('admin.promotional-videos.index')
            ->with('success', 'تم تحديث الفيديو الدعائي بنجاح');
    }

    public function destroy(PromotionalVideo $promotionalVideo)
    {
        $promotionalVideo->delete();

        return redirect()->route('admin.promotional-videos.index')
            ->with('success', 'تم حذف الفيديو الدعائي بنجاح');
    }

    private function validateVideo(Request $request): array
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'youtube_url' => [
                'required',
                'string',
                'max:500',
                function ($attribute, $value, $fail) {
                    if (! YouTubeUrl::isValid($value)) {
                        $fail('يرجى إدخال رابط YouTube صالح (مثل: https://www.youtube.com/watch?v=... أو https://youtu.be/...).');
                    }
                },
            ],
            'description' => 'nullable|string|max:2000',
            'sort_order' => 'nullable|integer|min:0|max:9999',
            'is_active' => 'boolean',
        ]);

        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);
        $validated['is_active'] = $request->boolean('is_active', true);

        return $validated;
    }
}
