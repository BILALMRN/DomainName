<?php

namespace Paymenter\Extensions\Others\DomainName\Admin\Resources;

use App\Admin\Resources\UserResource;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Hidden;
use Paymenter\Extensions\Others\DomainName\Admin\Resources\DomainNameResource\Pages;
use Paymenter\Extensions\Others\DomainName\Models\Domain;

class DomainNameResource extends Resource
{
    protected static ?string $model = Domain::class;

    protected static ?string $navigationLabel = 'DomainName';

    protected static ?string $navigationIcon = 'ri-archive-stack-line';

    protected static ?string $navigationGroup = 'Administration';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('domain')->visibleOn('edit'),
                Forms\Components\TextInput::make('ns1')
                    ->label('Nameserver 1'),
                Forms\Components\TextInput::make('ns2')
                    ->requiredIfAccepted('ns1')
                    ->label('Nameserver 2'),
                Forms\Components\TextInput::make('ns3')
                    ->label('Nameserver 3'),
                Forms\Components\TextInput::make('ns4')
                    ->label('Nameserver 4'),
                Forms\Components\Select::make('user_id')
                    ->label('User')
                    ->relationship('user', 'id')
                    ->searchable()
                    ->preload()
                    ->hint(fn($get) => $get('user_id') ? new HtmlString('<a href="' . UserResource::getUrl('edit', ['record' => $get('user_id')]) . '" target="_blank">Go to User</a>') : null)
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->name)
                    ->getSearchResultsUsing(fn(string $search): array => User::where('first_name', 'like', "%$search%")->orWhere('last_name', 'like', "%$search%")->limit(50)->pluck('name', 'id')->toArray())
                    ->live()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('domain')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('register_name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('user.name')->label('User'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')
                    ->options(function () {
                        return \App\Models\User::all()->mapWithKeys(function ($user) {
                            return [$user->id => $user->first_name . ' ' . $user->last_name . ' (' . $user->email . ')'];
                        });
                    })
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDomains::route('/'),
            'edit' => Pages\EditDomain::route('/{record}/edit'),
        ];
    }
}
