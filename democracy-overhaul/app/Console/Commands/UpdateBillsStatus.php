<?php

namespace App\Console\Commands;

use App\Models\Bill;
use Illuminate\Console\Command;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;

#[Signature('bills:update-status')]
#[Description('Update bills status')]
class UpdateBillsStatus extends Command
{
    public function handle(): int
    {
        // 1. Переводим standby → open, если наступило время старта
        $standbyBills = Bill::where('status', Bill::STATUS_STANDBY)
                            ->whereNotNull('voting_start_at')
                            ->where('voting_start_at', '<=', now())
                            ->get();

        foreach ($standbyBills as $bill) {
            $bill->updateStatusToOpenIfStarted();
            $this->line("Законопроект #{$bill->id} переведён в статус open");
        }

        // 2. Финализируем открытые голосования, у которых истекло время
        $openBills = Bill::where('status', Bill::STATUS_OPEN)
                         ->whereNotNull('voting_end_at')
                         ->where('voting_end_at', '<=', now())
                         ->get();

        foreach ($openBills as $bill) {
            $bill->finalizeIfEnded();
            $this->line("Законопроект #{$bill->id} финализирован: статус = {$bill->status}");
        }

        $this->info('Обновление статусов завершено.');
        return 0;
    }
}