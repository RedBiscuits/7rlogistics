<?php

namespace App\Models;

use App\Enums\ModelChangeSource;
use App\Enums\ModelChangeType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ModelChangeNotification extends Model
{
    protected $fillable = [
        'subject_type',
        'subject_id',
        'change_type',
        'source',
        'actor_type',
        'actor_id',
        'message',
        'read_at',
    ];

    protected $casts = [
        'change_type' => ModelChangeType::class,
        'source' => ModelChangeSource::class,
        'read_at' => 'datetime',
    ];

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    public function markAsRead(): void
    {
        if (! $this->read_at) {
            $this->update(['read_at' => now()]);
        }
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function actor(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the Filament URL to view/edit the subject in the admin panel.
     */
    public function getSubjectViewUrl(?string $panel = null): ?string
    {
        $map = config('model_change_notifications.subject_resource_map', []);
        $subjectType = $this->subject_type;
        if (! isset($map[$subjectType])) {
            return null;
        }
        $entry = $map[$subjectType];
        $resourceClass = $entry['resource'] ?? null;
        $targetPanel = $panel ?? $entry['panel'] ?? null;
        if (! $resourceClass || ! class_exists($resourceClass)) {
            return null;
        }
        return $resourceClass::getUrl('edit', ['record' => $this->subject_id]);
    }
}
