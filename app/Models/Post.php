<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Post extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected $fillable = [
        'title',
        'slug',
        'content',
        'image',
        'is_active',
        'views',
    ];

    protected $casts = [
        'title' => 'array',
        'slug' => 'array',
        'content' => 'array',
        'is_active' => 'boolean',
    ];

    public function hashtags()
    {
        return $this->belongsToMany(Hashtag::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')->singleFile();
        $this->addMediaCollection('blocks');
    }

    public function getLocalizedTitleAttribute()
    {
        $locale = app()->getLocale();
        $fallback = $locale === 'ar' ? 'en' : 'ar';
        return $this->title[$locale] ?? $this->title[$fallback] ?? null;
    }

    public function getLocalizedSlugAttribute()
    {
        $locale = app()->getLocale();
        $fallback = $locale === 'ar' ? 'en' : 'ar';
        return $this->slug[$locale] ?? $this->slug[$fallback] ?? null;
    }

    public function getLocalizedContentAttribute()
    {
        $locale = app()->getLocale();
        return app(\App\Services\PostRenderService::class)->getLocalizedContent($this->content, $locale);
    }

    public function getCoverImageUrlAttribute()
    {
        return $this->getFirstMediaUrl('cover') ?: ($this->image ? \Illuminate\Support\Facades\Storage::url($this->image) : null);
    }
}
