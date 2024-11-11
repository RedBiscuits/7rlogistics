<?php

namespace App\Filament\Finance\Resources\InvoicesResource;

use App\Filament\Finance\Resources\InvoicesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInvoices extends ListRecords
{
    protected static string $resource = InvoicesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
