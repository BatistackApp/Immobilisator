<?php

namespace App\Filament\Pages;

use App\Models\CompanySettings;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;

class ManageCompanySettings extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string|null|\UnitEnum $navigationGroup = 'Configuration';

    protected static ?string $navigationLabel = 'Paramètres Société';

    protected static ?string $title = 'Configuration de l\'Entreprise';

    protected string $view = 'filament.pages.manage-company-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = CompanySettings::first() ?? new CompanySettings;
        $this->form->fill($settings->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identité de l\'entreprise')
                    ->description('Ces informations apparaîtront sur vos rapports fiscaux et exports PDF.')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('company_name')
                                ->label('Nom de la société')
                                ->required(),
                            TextInput::make('vat_number')
                                ->label('Numéro de TVA Intracommunautaire'),
                            TextInput::make('siret')
                                ->label('SIRET / Identifiant fiscal'),
                            TextInput::make('currency')
                                ->label('Devise')
                                ->default('EUR')
                                ->required(),
                        ]),
                    ]),

                Section::make('Coordonnées de la société')
                    ->description('Ces informations apparaîtront sur vos rapports fiscaux et exports PDF.')
                    ->schema([
                        Textarea::make('address')
                            ->label('Adresse Postal')
                            ->required(),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('postal_code')
                                    ->label('Code Postal')
                                    ->numeric()
                                    ->required(),

                                TextInput::make('city')
                                    ->label('Ville')
                                    ->required(),

                                TextInput::make('country')
                                    ->label('Pays')
                                    ->required(),
                            ]),
                    ]),

                Section::make('Configuration Fiscale')
                    ->description('Paramètres critiques pour le calcul du prorata temporis.')
                    ->schema([
                        Select::make('fiscal_year_start_month')
                            ->label('Mois de début d\'exercice')
                            ->options([
                                1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
                                5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
                                9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre',
                            ])
                            ->required(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $settings = CompanySettings::first() ?? new CompanySettings;
        $settings->fill($this->form->getState());
        $settings->save();

        Notification::make()
            ->title('Configuration mise à jour')
            ->success()
            ->send();
    }
}
