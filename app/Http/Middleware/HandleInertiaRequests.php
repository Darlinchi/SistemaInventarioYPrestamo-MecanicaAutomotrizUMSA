<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Models\Loan;
use Carbon\Carbon;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');

        return [
            ...parent::share($request),
            'name' => 'SISTEMA WEB DE GESTIÓN DE INVENTARIOS Y CONTROL DE PRÉSTAMOS DE EQUIPOS', // El nombre de tu sistema
            'quote' => ['message' => 'Carrera de Mecánica Automotriz - UMSA'],
            //'name' => config('app.name'),
            //'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $request->user() ? [
                    'id'       => $request->user()->id,
                    'name'     => $request->user()->name,
                    'username' => $request->user()->username,
                    // Esta es la parte clave: enviamos los nombres de los roles a Vue
                    'roles'    => $request->user()->getRoleNames(),
                ] : null,
            ],
            // CONFIGURACION PARA LOS MENSAJES FLASH
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error'   => fn () => $request->session()->get('error'),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];

        $now = Carbon::now();

        return array_merge(parent::share($request), [
            'notifications' => [
                'vencidos_count' => Loan::where('estado_prestamo', '!=', 'Devuelto')
                    ->where(function ($query) use ($now) {
                        $query->where('fecha_retorno_prevista', '<', $now->toDateString())
                            ->orWhere(function ($q) use ($now) {
                                $q->where('fecha_retorno_prevista', '=', $now->toDateString())
                                    ->where('hora_fin_prevista', '<', $now->toTimeString());
                            });
                    })->count(),
            ],
        ]);
        }
}
