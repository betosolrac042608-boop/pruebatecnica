<?php

namespace App\Filament\Pages;

use App\Models\PlanTrabajoDiario;
use App\Services\ChatBotService;
use Filament\Pages\Page;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Facades\Auth;

class ChatBotGuardIAno extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static string $view = 'filament.pages.chat-bot-guardiano';
    protected static ?string $navigationLabel = 'Chat con GuardIAno';
    protected static ?string $title = 'Chat con GuardIAno';
    protected static ?string $navigationGroup = 'Gestión de Activos';
    protected static ?int $navigationSort = 10;

    public $planTrabajoId = null;
    public $mensaje = '';
    public $conversaciones = [];
    public $planSeleccionado = null;

    protected $listeners = ['mensajeEnviado' => 'cargarConversaciones'];

    public function mount(): void
    {
        $this->verificarPermisos();
    }

    protected function verificarPermisos(): void
    {
        $user = Auth::user();
        if (!$user || !$user->rol) {
            abort(403, 'No tienes permisos para acceder a esta página.');
        }

        $rolNombre = $user->rol->nombre;
        if (!in_array($rolNombre, ['Supervisor', 'Operador'])) {
            abort(403, 'Solo Supervisores y Operadores pueden acceder al chat.');
        }
    }

    public function updatedPlanTrabajoId($value): void
    {
        if ($value) {
            $this->planSeleccionado = PlanTrabajoDiario::with(['predio', 'usuario', 'tareas.zona'])
                ->find($value);
            $this->cargarConversaciones();
        } else {
            $this->planSeleccionado = null;
            $this->conversaciones = [];
        }
    }

    public function cargarConversaciones(): void
    {
        if (!$this->planTrabajoId) {
            $this->conversaciones = [];
            return;
        }

        $plan = PlanTrabajoDiario::find($this->planTrabajoId);
        if (!$plan) {
            $this->conversaciones = [];
            return;
        }

        $this->conversaciones = $plan->conversaciones()
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($conv) {
                return [
                    'id' => $conv->id,
                    'rol' => $conv->rol,
                    'mensaje' => $conv->mensaje,
                    'fecha' => $conv->created_at->format('d/m/Y H:i'),
                    'usuario' => $conv->usuario->name ?? 'Usuario',
                ];
            })
            ->toArray();
    }

    public function enviarMensaje(): void
    {
        $this->validate([
            'planTrabajoId' => 'required|exists:plan_trabajo_diarios,id',
            'mensaje' => 'required|string|min:1|max:1000',
        ]);

        if (empty(trim($this->mensaje))) {
            return;
        }

        $plan = PlanTrabajoDiario::find($this->planTrabajoId);
        if (!$plan) {
            $this->dispatch('error', 'Plan de trabajo no encontrado.');
            return;
        }

        $chatBotService = app(ChatBotService::class);
        $respuesta = $chatBotService->enviarMensaje($plan, Auth::user(), trim($this->mensaje));

        $this->mensaje = '';
        $this->cargarConversaciones();

        $this->dispatch('mensajeEnviado');
    }

    public static function canAccess(): bool
    {
        // Ocultar esta página ya que ahora está integrada en el Dashboard
        return false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        // No mostrar en el menú de navegación
        return false;
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        Select::make('planTrabajoId')
                            ->label('Seleccionar Plan de Trabajo')
                            ->options(function () {
                                $user = Auth::user();
                                return PlanTrabajoDiario::query()
                                    ->where('usuario_id', $user->id)
                                    ->with(['predio', 'usuario'])
                                    ->orderBy('fecha', 'desc')
                                    ->get()
                                    ->mapWithKeys(function ($plan) {
                                        $fecha = $plan->fecha->format('d/m/Y');
                                        $predio = $plan->predio->nombre ?? 'N/A';
                                        return [$plan->id => "{$predio} - {$fecha}"];
                                    });
                            })
                            ->searchable()
                            ->preload()
                            ->placeholder('Selecciona un plan de trabajo')
                            ->reactive()
                            ->afterStateUpdated(function ($state) {
                                $this->planTrabajoId = $state;
                                $this->updatedPlanTrabajoId($state);
                            }),
                    ])
                    ->statePath('data'),
            ),
        ];
    }
}

