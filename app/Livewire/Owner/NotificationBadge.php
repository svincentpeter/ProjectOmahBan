<?php
// 📁 app/Http/Livewire/Owner/NotificationBadge.php

namespace App\Http\Livewire\Owner;

use Livewire\Component;
use App\Models\OwnerNotification;

class NotificationBadge extends Component
{
    public $unreadCount = 0;
    public $criticalCount = 0;
    
    protected $listeners = [
        'notificationReceived' => 'refresh',
        'notificationRead' => 'refresh'
    ];
    
    public function mount()
    {
        $this->refresh();
    }
    
    public function refresh()
    {
        $userId = auth()->id();
        
        $this->unreadCount = OwnerNotification::getUnreadCount($userId);
        
        $this->criticalCount = OwnerNotification::where('user_id', $userId)
            ->unread()
            ->where('severity', 'critical')
            ->count();
    }
    
    /**
     * Polling setiap 30 detik (fallback jika tidak pakai Pusher)
     */
    public function render()
    {
        // Auto-refresh setiap 30 detik
        $this->refresh();
        
        return view('livewire.owner.notification-badge');
    }
}
