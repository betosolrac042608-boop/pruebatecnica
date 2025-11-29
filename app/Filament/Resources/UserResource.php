<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Concerns\HideFromOperarioSupervisor;
use App\Models\User;
use App\Models\Rol;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;

class UserResource extends Resource
{
    use HideFromOperarioSupervisor;
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Usuarios';
    protected static ?string $modelLabel = 'Usuario';
    protected static ?string $pluralModelLabel = 'Usuarios';
    protected static ?string $navigationGroup = 'Administración';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Personal')
                    ->description('Datos básicos del usuario')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre Completo')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ej: Juan Pérez')
                            ->columnSpan(2),
                        
                        Forms\Components\TextInput::make('username')
                            ->label('Nombre de Usuario')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->placeholder('Ej: jperez')
                            ->alphaDash()
                            ->validationMessages([
                                'unique' => 'Este nombre de usuario ya está en uso.',
                                'required' => 'El nombre de usuario es obligatorio.',
                            ]),
                        
                        Forms\Components\TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->placeholder('correo@ejemplo.com')
                            ->validationMessages([
                                'email' => 'El formato del correo no es válido.',
                                'unique' => 'Este correo ya está registrado.',
                            ]),
                        
                        Forms\Components\TextInput::make('telefono')
                            ->label('Teléfono')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('Ej: +52 123 456 7890'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Credenciales y Acceso')
                    ->description('Configuración de acceso al sistema')
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->label('Contraseña')
                            ->password()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->minLength(8)
                            ->placeholder('Mínimo 8 caracteres')
                            ->validationMessages([
                                'min' => 'La contraseña debe tener al menos 8 caracteres.',
                                'required' => 'La contraseña es obligatoria.',
                            ]),
                        
                        Forms\Components\TextInput::make('password_confirmation')
                            ->label('Confirmar Contraseña')
                            ->password()
                            ->same('password')
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(false)
                            ->validationMessages([
                                'same' => 'Las contraseñas no coinciden.',
                            ]),
                        
                        Forms\Components\Select::make('rol_id')
                            ->label('Rol')
                            ->relationship('rol', 'nombre')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->placeholder('Seleccione un rol')
                            ->validationMessages([
                                'required' => 'Debe seleccionar un rol.',
                            ]),
                        
                        Forms\Components\Toggle::make('active')
                            ->label('Usuario Activo')
                            ->default(true)
                            ->inline(false)
                            ->helperText('Desactivar un usuario impedirá su acceso al sistema'),
                        
                        Forms\Components\DateTimePicker::make('email_verified_at')
                            ->label('Email Verificado')
                            ->placeholder('Sin verificar')
                            ->helperText('Fecha en que se verificó el correo electrónico'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Información Adicional')
                    ->description('Datos de seguimiento del sistema')
                    ->schema([
                        Forms\Components\Placeholder::make('created_at')
                            ->label('Creado')
                            ->content(fn (User $record): ?string => $record->created_at?->diffForHumans()),
                        
                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Última Modificación')
                            ->content(fn (User $record): ?string => $record->updated_at?->diffForHumans()),
                        
                        Forms\Components\Placeholder::make('last_login')
                            ->label('Último Acceso')
                            ->content(fn (User $record): ?string => $record->last_login?->diffForHumans() ?? 'Nunca'),
                    ])->columns(3)
                    ->hidden(fn (string $operation): bool => $operation === 'create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('username')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Usuario copiado')
                    ->badge()
                    ->color('info'),
                
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->icon('heroicon-m-envelope'),
                
                Tables\Columns\TextColumn::make('telefono')
                    ->label('Teléfono')
                    ->searchable()
                    ->icon('heroicon-m-phone')
                    ->placeholder('Sin teléfono'),
                
                Tables\Columns\TextColumn::make('rol.nombre')
                    ->label('Rol')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Administrador' => 'danger',
                        'Supervisor' => 'warning',
                        'Operario' => 'success',
                        default => 'gray',
                    }),
                
                Tables\Columns\IconColumn::make('active')
                    ->label('Activo')
                    ->boolean()
                    ->sortable()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                
                Tables\Columns\TextColumn::make('last_login')
                    ->label('Último Acceso')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('Nunca')
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('rol_id')
                    ->label('Rol')
                    ->relationship('rol', 'nombre')
                    ->multiple()
                    ->preload(),
                
                Tables\Filters\TernaryFilter::make('active')
                    ->label('Estado')
                    ->placeholder('Todos los usuarios')
                    ->trueLabel('Solo activos')
                    ->falseLabel('Solo inactivos'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Editar')
                    ->color('warning'),
                Tables\Actions\DeleteAction::make()
                    ->label('Eliminar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activar seleccionados')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each->update(['active' => true]);
                            Notification::make()
                                ->success()
                                ->title('Usuarios activados')
                                ->body('Los usuarios seleccionados han sido activados.')
                                ->send();
                        }),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Desactivar seleccionados')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each->update(['active' => false]);
                            Notification::make()
                                ->success()
                                ->title('Usuarios desactivados')
                                ->body('Los usuarios seleccionados han sido desactivados.')
                                ->send();
                        }),
                ]),
            ])
            ->emptyStateHeading('No hay usuarios registrados')
            ->emptyStateDescription('Comienza creando tu primer usuario.')
            ->emptyStateIcon('heroicon-o-users');
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}

