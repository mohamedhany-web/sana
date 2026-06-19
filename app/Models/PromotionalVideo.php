<?php

namespace App\Models;

use App\Support\YouTubeUrl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PromotionalVideo extends Model
{
    protected $fillable = [
        'title',
        'youtube_url',
        'description',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderByDesc('id');
    }

    public function youtubeVideoId(): ?string
    {
        return YouTubeUrl::extractVideoId($this->youtube_url);
    }

    public function embedUrl(): ?string
    {
        return YouTubeUrl::embedUrl($this->youtube_url);
    }

    public function thumbnailUrl(): ?string
    {
        return YouTubeUrl::thumbnailUrl($this->youtube_url);
    }
}
