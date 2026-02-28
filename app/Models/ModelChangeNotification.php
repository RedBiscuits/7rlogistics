<?php

namespace App\Models;

use App\Enums\ModelChangeSource;
use App\Enums\ModelChangeType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
    ];

    protected $casts = [
        'change_type' => ModelChangeType::class,
        'source' => ModelChangeSource::class,
    ];

    public function reads(): HasMany
    {
        return $this->hasMany(ModelChangeNotificationRead::class, 'model_change_notification_id');
    }

    public function isRead(?\Illuminate\Contracts\Auth\Authenticatable $user = null): bool
    {
        $user = $user ?? auth()->user();
        if (! $user) {
            return false;
        }

        return $this->reads()->where('user_id', $user->getAuthIdentifier())->exists();
    }

    public function markAsRead(?\Illuminate\Contracts\Auth\Authenticatable $user = null): void
    {
        $user = $user ?? auth()->user();
        if (! $user) {
            return;
        }

        $this->reads()->updateOrCreate(
            ['user_id' => $user->getAuthIdentifier()],
            ['read_at' => now()]
        );
    }

    public function scopeUnread($query, ?\Illuminate\Contracts\Auth\Authenticatable $user = null)
    {
        $user = $user ?? auth()->user();
        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereDoesntHave('reads', fn ($q) => $q->where('user_id', $user->getAuthIdentifier()));
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
