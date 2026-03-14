<?php

namespace App\Filament\Infolists\Components;

use App\Service\FinancingService;
use Filament\Infolists\Components\Entry;

class LoanScheduleEntry extends Entry
{
    protected string $view = 'filament.infolists.components.loan-schedule-entry';

    public function getViewData(): array
    {
        $record = $this->getRecord();

        return [
            'loan' => $record,
            'schedule' => app(FinancingService::class)->calculateLoanInstallments($record),
        ];
    }
}
