<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    protected $fillable = [
        'created_by', 'title', 'description', 'type', 'category',
        'url', 'file_path', 'contact_number', 'available_hours',
        'is_emergency', 'is_published', 'sort_order', 'view_count',
    ];

    protected function casts(): array
    {
        return [
            'is_emergency' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    public const CATEGORIES = [
        'crisis'        => '🚨 Crisis & Emergency',
        'mental_health' => '🧠 Mental Health',
        'academic'      => '📚 Academic Support',
        'career'        => '💼 Career & Future',
        'financial'     => '💰 Financial Aid',
        'relationships' => '❤️ Relationships',
        'self_care'     => '🌱 Self-Care',
        'other'         => '📌 Other',
    ];

    public const TYPES = [
        'article'  => '📄 Article',
        'video'    => '🎥 Video',
        'pdf'      => '📑 PDF Download',
        'link'     => '🔗 External Link',
        'hotline'  => '☎️ Hotline',
        'contact'  => '👤 Contact Person',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? ucwords(str_replace('_', ' ', $this->category));
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? ucfirst($this->type);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeEmergency($query)
    {
        return $query->where('is_emergency', true);
    }
}
