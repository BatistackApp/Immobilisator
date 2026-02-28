<?php

namespace App\Filament\Resources\Providers\Pages;

use App\Actions\Fortify\CreateNewUser;
use App\Filament\Resources\Providers\ProviderResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CreateProvider extends CreateRecord
{
    protected static string $resource = ProviderResource::class;

    protected static ?string $title = "Création d'un Tier";

    protected static ?string $breadcrumb = 'Création';

    protected function handleRecordCreation(array $data): Model
    {
        if (isset($data['email'])) {
            $tempPassword = Str::random(10);
            $user = app(CreateNewUser::class)->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $tempPassword,
                'password_confirmation' => $tempPassword,
            ]);
        }

        $data['user_id'] = $user->id ?? null;

        return self::getModel()::create($data);
    }
}
