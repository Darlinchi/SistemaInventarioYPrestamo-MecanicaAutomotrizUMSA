<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Mantenimiento - {{ $equipment->nombre_equipo }}</title>
    <style>
        @page {
            margin: 1.5cm 1.8cm 1.5cm 1.8cm;
            size: letter landscape;
        }
        body { font-family: 'Helvetica', sans-serif; font-size: 9pt; line-height: 1.3; color: #333; }

        .table { width: 100%; border-collapse: collapse; margin-bottom: 8px; table-layout: fixed; }
        .table td { border: 1px solid #000; padding: 5px 7px; vertical-align: middle; word-wrap: break-word; }
        .header-bg { background-color: #f2f2f2; font-weight: bold; text-align: center; }
        .label { background-color: #f9f9f9; font-weight: bold; text-transform: uppercase; font-size: 8pt; }
        .section-title { background-color: #1a3a5a; color: white; font-weight: bold; text-align: center; text-transform: uppercase; padding: 5px; font-size: 9pt; }
        .title { font-size: 11pt; font-weight: bold; text-align: center; }

        .maint-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; table-layout: fixed; }
        .maint-table th { background-color: #1a3a5a; color: white; font-weight: bold; font-size: 8pt; text-align: center; padding: 5px 6px; border: 1px solid #000; text-transform: uppercase; }
        .maint-table td { border: 1px solid #555; padding: 5px 6px; font-size: 8pt; vertical-align: top; word-wrap: break-word; }
        .maint-table tbody tr:nth-child(even) td { background-color: #f7f9fc; }
        .maint-table tbody tr.empty-row td { height: 20px; background-color: #fff; }

        .badge { display: inline-block; padding: 1px 5px; border-radius: 3px; font-size: 7.5pt; font-weight: bold; }
        .badge-completado { background-color: #d1fae5; color: #065f46; }
        .badge-proceso    { background-color: #fef3c7; color: #92400e; }
        .badge-preventivo { background-color: #dbeafe; color: #1e40af; }
        .badge-correctivo { background-color: #fee2e2; color: #991b1b; }
        .resumen-box { border: 1px solid #ccc; border-radius: 3px; padding: 3px 10px; margin-right: 6px; background: #f8fafc; display: inline-block; font-size: 8pt; }
        .resumen-label { font-weight: bold; color: #1a3a5a; }
    </style>
</head>
<body>

    {{-- CABECERA --}}
    <table class="table">
        <tr>
            <td rowspan="2" style="width: 13%; text-align: center; vertical-align: middle;">
                <img src="{{ public_path('images/logo-carrera.png') }}" style="width: 55px;">
            </td>
            <td class="title" style="width: 55%;">HISTORIAL DE MANTENIMIENTO</td>
            <td class="header-bg" style="width: 16%;">CÓDIGO:</td>
            <td style="width: 16%;">{{ $equipment->codigo_qr ?? 'S/C' }}</td>
        </tr>
        <tr>
            <td style="text-align: center; font-weight: bold; font-size: 8pt;">CARRERA DE MECÁNICA AUTOMOTRIZ - UMSA</td>
            <td class="header-bg">FECHA:</td>
            <td>{{ date('d/m/Y') }}</td>
        </tr>
    </table>

    {{-- DATOS DEL EQUIPO --}}
    <table class="table">
        <tr><td colspan="6" class="section-title">IDENTIFICACIÓN DEL EQUIPO</td></tr>
        <tr>
            <td class="label" style="width: 12%;">EQUIPO:</td>
            <td style="width: 30%; font-weight: bold; text-transform: uppercase;">{{ $equipment->nombre_equipo }}</td>
            <td class="label" style="width: 10%;">MARCA:</td>
            <td style="width: 15%;">{{ $equipment->marca ?? '—' }}</td>
            <td class="label" style="width: 10%;">MODELO:</td>
            <td style="width: 23%;">{{ $equipment->modelo ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">SERIE:</td>
            <td>{{ $equipment->serie ?? '—' }}</td>
            <td class="label">UBICACIÓN:</td>
            <td>{{ $equipment->ubicacion_equipo ?? '—' }}</td>
            <td class="label">ESTADO ACTUAL:</td>
            <td><strong>{{ $equipment->estado_equipo }}</strong></td>
        </tr>
        <tr>
            <td class="label">RUBRO:</td>
            <td>{{ $equipment->rubro ?? '—' }}</td>
            <td class="label">FECHA ADQ.:</td>
            <td>{{ $equipment->fecha_adquisicion ? \Carbon\Carbon::parse($equipment->fecha_adquisicion)->format('d/m/Y') : '—' }}</td>
            <td class="label">TOTAL MANT.:</td>
            <td><strong>{{ $maintenances->count() }}</strong> registros</td>
        </tr>
    </table>

    {{-- TABLA DE MANTENIMIENTOS --}}
    <table class="maint-table">
        <thead>
            <tr>
                <th style="width: 4%;">#</th>
                <th style="width: 9%;">Fecha</th>
                <th style="width: 8%;">Tipo</th>
                <th style="width: 13%;">Empresa Técnica</th>
                <th style="width: 27%;">Actividades Predominantes</th>
                <th style="width: 9%;">Estado Final</th>
                <th style="width: 10%;">Próx. Mantenimiento</th>
                <th style="width: 10%;">Registrado por</th>
                <th style="width: 10%;">Fecha Retorno</th>
            </tr>
        </thead>
        <tbody>
            @forelse($maintenances as $i => $maint)
            <tr>
                <td style="text-align: center; font-weight: bold; color: #1a3a5a;">{{ $i + 1 }}</td>

                <td style="text-align: center;">
                    {{ \Carbon\Carbon::parse($maint->fecha_mantenimiento)->format('d/m/Y') }}
                    @if($maint->hora_inicio)
                        <br><span style="color:#555; font-size:7.5pt;">{{ substr($maint->hora_inicio, 0, 5) }}</span>
                    @endif
                </td>

                <td style="text-align: center;">
                    <span class="badge {{ $maint->tipo_mantenimiento === 'Preventivo' ? 'badge-preventivo' : 'badge-correctivo' }}">
                        {{ $maint->tipo_mantenimiento }}
                    </span>
                </td>

                <td>
                    @if($maint->companies && $maint->companies->count() > 0)
                        @foreach($maint->companies as $company)
                            {{ $company->nombre_empresa }}@if(!$loop->last)<br>@endif
                        @endforeach
                    @else
                        <span style="color:#aaa;">—</span>
                    @endif
                </td>

                <td>{{ $maint->actividad ?? 'Sin descripción registrada.' }}</td>

                <td style="text-align: center;">
                    @if($maint->estado_mantenimiento === 'Completado')
                        <span class="badge badge-completado">{{ $maint->estado_final_equipo ?? 'Disponible' }}</span>
                    @else
                        <span class="badge badge-proceso">En Proceso</span>
                    @endif
                </td>

                <td style="text-align: center;">
                    @if($maint->fecha_proximo_mantenimiento)
                        {{ \Carbon\Carbon::parse($maint->fecha_proximo_mantenimiento)->format('d/m/Y') }}
                    @else
                        <span style="color:#aaa;">—</span>
                    @endif
                </td>

                <td style="text-align: center; font-size: 7.5pt;">
                    @if(isset($maint->user) && $maint->user)
                        {{ $maint->user->name }}
                    @else
                        <span style="color:#aaa;">—</span>
                    @endif
                </td>

                <td style="text-align: center;">
                    @if($maint->fecha_retorno)
                        {{ \Carbon\Carbon::parse($maint->fecha_retorno)->format('d/m/Y') }}
                        @if($maint->hora_fin)
                            <br><span style="color:#555; font-size:7.5pt;">{{ substr($maint->hora_fin, 0, 5) }}</span>
                        @endif
                    @else
                        <span style="color:#aaa;">—</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align: center; color: #aaa; padding: 20px; font-style: italic;">
                    Sin registros de mantenimiento para este equipo.
                </td>
            </tr>
            @endforelse

            {{-- Filas vacías hasta 10 mínimo (igual que la hoja física) --}}
            <tr>
                <td colspan="9" style="height: 8px; background-color: #f0f4f8; border-top: 2px solid #1a3a5a;"></td>
            </tr>
        </tbody>
    </table>

    {{-- RESUMEN --}}
    <table class="table" style="margin-bottom: 0;">
        <tr>
            <td style="border: 1px solid #ccc; background: #f8fafc; padding: 5px 10px; width: 65%;">
                <span class="resumen-box"><span class="resumen-label">Total: </span>{{ $maintenances->count() }}</span>
                <span class="resumen-box"><span class="resumen-label">Preventivos: </span>{{ $maintenances->where('tipo_mantenimiento', 'Preventivo')->count() }}</span>
                <span class="resumen-box"><span class="resumen-label">Correctivos: </span>{{ $maintenances->where('tipo_mantenimiento', 'Correctivo')->count() }}</span>
                <span class="resumen-box"><span class="resumen-label">Completados: </span>{{ $maintenances->where('estado_mantenimiento', 'Completado')->count() }}</span>
                <span class="resumen-box"><span class="resumen-label">En Proceso: </span>{{ $maintenances->where('estado_mantenimiento', 'En Proceso')->count() }}</span>
            </td>
            <td style="border: 1px solid #ccc; text-align: right; font-size: 7.5pt; color: #666; background: #f8fafc; padding: 5px 10px;">
                Generado: {{ date('d/m/Y H:i') }}<br>
                Por: {{ auth()->user()->name ?? 'Sistema' }}
            </td>
        </tr>
    </table>

    {{-- FIRMAS (igual que inventory-ficha) --}}
    <table style="width: 100%; margin-top: 40px; border-collapse: collapse;">
        <tr>
            <td style="text-align: center; width: 30%; border-top: 1px solid #000; padding-top: 5px; border-left: none; border-right: none; border-bottom: none;">
                <strong>ELABORADO POR</strong><br>
                {{ auth()->user()->name ?? '—' }}
            </td>
            <td style="width: 5%;"></td>
            <td style="text-align: center; width: 30%; border-top: 1px solid #000; padding-top: 5px; border-left: none; border-right: none; border-bottom: none;">
                <strong>ENCARGADO DE TALLER</strong><br>&nbsp;
            </td>
            <td style="width: 5%;"></td>
            <td style="text-align: center; width: 30%; border-top: 1px solid #000; padding-top: 5px; border-left: none; border-right: none; border-bottom: none;">
                <strong>V°B° DIRECTOR DE CARRERA</strong><br>&nbsp;
            </td>
        </tr>
    </table>

    <div style="margin-top: 15px; font-size: 7.5pt; text-align: center; color: #888; font-style: italic;">
        Sistema de Gestión de Inventarios — Carrera de Mecánica Automotriz · UMSA
    </div>

</body>
</html>