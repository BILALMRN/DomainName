<?php

namespace Paymenter\Extensions\Others\DomainName\Admin\Resources\DomainNameResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Paymenter\Extensions\Others\DomainName\Admin\Resources\DomainNameResource;

class ListDomains extends ListRecords
{
    protected static string $resource = DomainNameResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
