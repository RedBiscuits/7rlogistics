@php
    $unreadCount = $unreadCount ?? 0;
    $url = $url ?? '#';
@endphp
<x-filament::icon-button
    :badge="$unreadCount > 0 ? (string) $unreadCount : null"
    badge-color="danger"
    color="gray"
    icon="heroicon-o-bell-alert"
    icon-size="lg"
    :label="__('Notifications')"
    :href="$url"
    tag="a"
/>
