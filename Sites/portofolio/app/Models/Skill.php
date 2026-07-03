<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    protected $fillable = ['skill_category_id', 'name', 'icon_upload', 'icon_url', 'order'];

    public function category()
    {
        return $this->belongsTo(SkillCategory::class, 'skill_category_id');
    }

    public function getIconAttribute(): ?string
    {
        if ($this->icon_upload) {
            return asset($this->icon_upload);
        }
        return $this->icon_url ?: null;
    }
}
