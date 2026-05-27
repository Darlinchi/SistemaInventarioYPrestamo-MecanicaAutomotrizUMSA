<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante de Devolución #{{ $loan->id }}</title>
    <style>
        @page { margin: 1.5cm; }
        body { font-family: 'Helvetica', sans-serif; font-size: 9pt; color: #333; line-height: 1.4; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .table td, .table th { border: 1px solid #000; padding: 6px; }
        .header-bg { background-color: #f8fafc; font-weight: bold; color: #1a3a5a; }
        .title { font-size: 14pt; font-weight: bold; text-align: center; text-transform: uppercase; color: #1a3a5a; }
        .section-title { background-color: #1a3a5a; color: white; font-weight: bold; padding: 5px; text-align: center; text-transform: uppercase; font-size: 9pt; letter-spacing: 0.5px; }
        .footer { margin-top: 60px; width: 100%; }
        .text-blue { color: #1a3a5a; font-weight: bold; }
        .badge { padding: 2px 5px; border-radius: 4px; font-size: 8pt; font-weight: bold; border: 1px solid #000; background-color: #f1f5f9; }

        /* Estilos de las cajas de firmas estructurales */
        .signatures-table { width: 100%; border-collapse: collapse; border: none; margin-top: 50px; }
        .signatures-table td { border: none; text-align: center; vertical-align: top; font-size: 8pt; width: 33.33%; padding: 10px; }
        .signature-line { border-top: 1px solid #000; padding-top: 5px; margin-top: 40px; }
    </style>
</head>
<body>

    <table class="table" style="border: none; margin-bottom: 25px;">
        <tr>
            <td style="border: none; width: 15%; vertical-align: middle;">
                <img src="{{ public_path('images/logo-carrera.png') }}" style="width: 55px;">
            </td>
            <td style="border: none; text-align: center; width: 65%; vertical-align: middle;">
                <div class="title" style="letter-spacing: 0.5px;">Comprobante de Devolución</div>
                <div style="font-weight: bold; color: #475569; font-size: 10pt; margin-top: 2px;">Taller de Mecánica Automotriz - UMSA</div>
            </td>
            <td style="border: none; width: 20%; text-align: right; vertical-align: middle; font-size: 8pt; color: #475569;">
                <div style="font-weight: bold; color: #1a3a5a; font-size: 10pt; margin-bottom: 2px;">Nº Folio: #{{ str_pad($loan->id, 5, '0', STR_PAD_LEFT) }}</div>
                <div>Emisión: {{ date('d/m/Y') }}</div>
            </td>
        </tr>
    </table>

    <table class="table">
        <tr><td colspan="4" class="section-title">INFORMACIÓN DEL PRÉSTAMO Y AUDITORÍA</td></tr>
        <tr>
            <td class="header-bg" style="width: 20%;">RESPONSABLE:</td>
            <td style="width: 30%;">{{ $loan->borrower->teacher?->titulo }} {{ $loan->borrower->apellidoPaterno }} {{ $loan->borrower->apellidoMaterno }} {{ $loan->borrower->nombres }}</td>
            <td class="header-bg" style="width: 20%;">C.I.:</td>
            <td style="width: 30%;">{{ $loan->borrower->cedula_identidad }}</td>
        </tr>
        <tr>
            <td class="header-bg">VÍNCULO:</td>
            <td>
                {{
                    $loan->borrower->teacher
                        ? 'DOCENTE'
                        : ($loan->borrower->assistant ? 'AUXILIAR' : 'ESTUDIANTE')
                }}
            </td>
            <td class="header-bg">MATERIA / SIGLA:</td>
            <td>{{ $loan->subject->nombre_materia ?? 'Uso General' }} @if($loan->subject) ({{ $loan->subject->sigla }}) @endif</td>
        </tr>
        <tr>
            <td class="header-bg">ENTREGADO POR:</td>
            <td class="text-blue">{{ $loan->user->name ?? 'Sistema' }}</td>
            <td class="header-bg">RECIBIDO POR:</td>
            <td class="text-blue">{{ $loan->loanReturns->user->name ?? 'Pendiente' }}</td>
        </tr>
    </table>

    <table class="table">
        <tr><td colspan="3" class="section-title">DETALLE CRONOLÓGICO DE TIEMPOS</td></tr>
        <tr class="header-bg" style="text-align: center; font-size: 8pt;">
            <td style="width: 33.33%;">REGISTRO DE SALIDA</td>
            <td style="width: 33.33%;">RETORNO PREVISTO</td>
            <td style="width: 33.33%;">RETORNO REAL DE RECEPCIÓN</td>
        </tr>
        <tr style="text-align: center; font-size: 9pt;">
            <td>
                {{ \Carbon\Carbon::parse($loan->fecha_salida)->format('d/m/Y') }}<br>
                <span class="text-blue" style="font-size: 8.5pt;">{{ $loan->hora_inicio }}</span>
            </td>
            <td>
                {{ \Carbon\Carbon::parse($loan->fecha_retorno_prevista)->format('d/m/Y') }}<br>
                <span style="color: #c05621; font-size: 8.5pt;">{{ $loan->hora_fin_prevista }}</span>
            </td>
            <td>
                @if($loan->loanReturns)
                    {{ \Carbon\Carbon::parse($loan->loanReturns->fecha_retorno)->format('d/m/Y') }}<br>
                    <span style="color: #2f855a; font-weight: bold; font-size: 8.5pt;">{{ $loan->loanReturns->hora_fin }}</span>
                @else
                    <span style="color: #dc2626; font-weight: bold;">Sin registro de retorno</span>
                @endif
            </td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr><td colspan="3" class="section-title">EQUIPOS Y HERRAMIENTAS VERIFICADOS</td></tr>
            <tr class="header-bg" style="font-size: 8pt;">
                <th style="width: 55%; text-align: left;">Descripción del Activo</th>
                <th style="width: 20%; text-align: center;">Código / QR</th>
                <th style="width: 25%; text-align: center;">Estado de Recepción</th>
            </tr>
        </thead>
        <tbody>
            @php $return = $loan->loanReturns; @endphp
            @if($return && $return->returnDetails->isNotEmpty())
                @foreach($return->returnDetails as $detail)
                <tr>
                    <td style="vertical-align: middle;">
                        <strong>
                            {{ str_contains($detail->returnable_type, 'Equipment') ? $detail->returnable->nombre_equipo : $detail->returnable->nombre_herramienta }}
                        </strong>
                        <span style="color: #64748b; font-size: 7.5pt; font-weight: bold;">
                            ({{ str_contains($detail->returnable_type, 'Equipment') ? 'EQ' : 'HER' }})
                        </span>

                        @if($detail->returnDetailAccessories && $detail->returnDetailAccessories->isNotEmpty())
                            <div style="margin-left: 12px; font-size: 7.5pt; color: #475569; margin-top: 3px; border-left: 1px dashed #cbd5e1; padding-pl: 6px;">
                                @foreach($detail->returnDetailAccessories as $rda)
                                    <div>• {{ $rda->accessory->nombre_accesorio }} <span style="font-size: 7px; font-weight: bold; color: #1a3a5a;">[{{ $rda->estado_accesorio }}]</span></div>
                                @endforeach
                            </div>
                        @endif
                    </td>
                    <td style="text-align: center; vertical-align: middle; font-family: monospace; font-size: 8.5pt;">
                        {{ $detail->returnable->codigo_qr ?? 'N/A' }}
                    </td>
                    <td style="text-align: center; vertical-align: middle;">
                        <span class="badge" style="color: {{ in_array($detail->estado_devolucion, ['Dañado', 'Extraviado', 'Incompleto']) ? '#dc2626' : '#16a34a' }};">
                            {{ strtoupper($detail->estado_devolucion) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="3" style="text-align: center; color: #64748b; font-style: italic; padding: 15px;">
                        No existen registros técnicos de devolución asociados a este folio.
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    <table class="table">
        <tr><td class="section-title" style="text-align: left; padding-left: 8px;">NOTAS DE RECEPCIÓN FINAL Y OBSERVACIONES</td></tr>
        <tr>
            <td style="height: 50px; vertical-align: top; font-style: italic; color: #475569; bg-color: #f9fafb;">
                {{ $loan->loanReturns->observacion ?? 'El préstamo fue devuelto en su totalidad sin observaciones técnico-operativas adicionales.' }}
            </td>
        </tr>
    </table>

    <table class="signatures-table">
        <tr>
            <td>
                <div class="signature-line">
                    <strong>{{ $loan->borrower->teacher?->titulo }} {{ $loan->borrower->apellidoPaterno }} {{ $loan->borrower->apellidoMaterno }} {{ $loan->borrower->nombres }}</strong><br>
                    <span>C.I. {{ $loan->borrower->cedula_identidad }}</span><br>
                    <span style="color: #64748b; font-weight: bold;">PRESTATARIO RESPONSABLE</span>
                </div>
            </td>
            <td>
                <div class="signature-line">
                    <strong>{{ $loan->user->name ?? '—' }}</strong><br>
                    <span style="color: #64748b;">Operador del Pañol</span><br>
                    <span style="color: #1a3a5a; font-weight: bold;">ENCARGADO QUE ENTREGÓ</span>
                </div>
            </td>
            <td>
                <div class="signature-line">
                    <strong>{{ $loan->loanReturns?->user?->name ?? '—' }}</strong><br>
                    <span style="color: #64748b;">Operador de Recepción</span><br>
                    <span style="color: #1a3a5a; font-weight: bold;">ENCARGADO QUE RECIBIÓ</span>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
