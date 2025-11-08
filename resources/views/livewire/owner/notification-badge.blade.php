{{-- 📁 resources/views/livewire/owner/notification-badge.blade.php --}}

<div wire:poll.30s class="position-relative">
    <a href="{{ route('owner.notifications.index') }}" class="btn btn-light position-relative">
        <i class="bi bi-bell"></i>
        
        @if($unreadCount > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill 
                {{ $criticalCount > 0 ? 'bg-danger' : 'bg-warning' }}">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                <span class="visually-hidden">notifikasi belum dibaca</span>
            </span>
        @endif
    </a>
</div>
