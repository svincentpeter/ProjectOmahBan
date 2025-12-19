<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test sending WhatsApp notification by simulating an event';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting notification test...');

        // 1. Get latest Sale and User
        $sale = \Modules\Sale\Entities\Sale::latest()->first();
        $user = \App\Models\User::first();

        if (!$sale) {
            $this->error('No sale data found to test.');
            return 1;
        }

        if (!$user) {
            $this->error('No user data found.');
            return 1;
        }

        $this->line("Using Sale: {$sale->reference}");
        $this->line("Using User: {$user->name}");

        // 2. Dummy Items
        $manualItems = [
            [
                'name' => 'Ban Test Terminal ' . now()->format('H:i'),
                'quantity' => 2,
                'price' => 150000
            ]
        ];

        // 3. Reset notified flag
        $this->info('Resetting is_manual_input_notified flag...');
        $sale->update(['is_manual_input_notified' => 0]);

        // 4. Trigger Event
        $this->info('Dispatching ManualInputCreated event...');
        event(new \App\Events\ManualInputCreated($sale, $manualItems, $user));

        $this->success('Event dispatched! Check your WhatsApp and Notification Recipients.');
        $this->line('Check whatsapp-service terminal for delivery logs.');
    }
}
