<?php

namespace App\Filament\Resources\Leasings;

use App\Filament\Resources\Leasings\Pages\CreateLeasing;
use App\Filament\Resources\Leasings\Pages\EditLeasing;
use App\Filament\Resources\Leasings\Pages\ListLeasings;
use App\Filament\Resources\Leasings\Pages\ViewLeasing;
use App\Filament\Resources\Leasings\Schemas\LeasingForm;
use App\Filament\Resources\Leasings\Schemas\LeasingInfolist;
use App\Filament\Resources\Leasings\Tables\LeasingsTable;
use App\Models\Leasing;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class LeasingResource extends Resource
{
    protected static ?string $model = Leasing::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static string|UnitEnum|null $navigationGroup = 'Gestion des Actifs';

    protected static ?string $navigationLabel = 'Locations';

    protected static ?string $breadcrumb = 'Locations';

    public static function form(Schema $schema): Schema
    {
        return LeasingForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LeasingInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LeasingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLeasings::route('/'),
            'create' => CreateLeasing::route('/create'),
            'view' => ViewLeasing::route('/{record}'),
            'edit' => EditLeasing::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
