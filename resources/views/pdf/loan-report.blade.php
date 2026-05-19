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
        .header-bg { background-color: #f2f2f2; font-weight: bold; }
        .title { font-size: 14pt; font-weight: bold; text-align: center; text-transform: uppercase; }
        .section-title { background-color: #1a3a5a; color: white; font-weight: bold; padding: 5px; text-align: center; }
        .footer { margin-top: 50px; text-align: center; }
        .signature-box { width: 45%; display: inline-block; border-top: 1px solid #000; margin: 0 2%; padding-top: 5px; }
        .text-blue { color: #1a3a5a; font-weight: bold; }
        .badge { padding: 2px 5px; border-radius: 4px; font-size: 8pt; font-weight: bold; border: 1px solid #ccc; }
    </style>
</head>
<body>

    <!-- ENCABEZADO -->
    <table class="table" style="border: none;">
        <tr>
            <td style="border: none; width: 20%;">
                <img src="{{ public_path('images/logo-carrera.png') }}" style="width: 60px;">
            </td>
            <td style="border: none; text-align: center; width: 60%;">
                <div class="title">Comprobante de Devolución</div>
                <div style="font-weight: bold;">Taller de Mecánica Automotriz - UMSA</div>
            </td>
            <td style="border: none; width: 20%; text-align: right;">
                <!--
                <div style="font-weight: bold;">Folio: #{{ str_pad($loan->id, 5, '0', STR_PAD_LEFT) }}</div>-->
                <div>Fecha: {{ date('d/m/Y') }}</div>
            </td>
        </tr>
    </table>

    <!-- DATOS DEL RESPONSABLE -->
    <table class="table">
        <tr><td colspan="4" class="section-title">INFORMACIÓN DEL PRÉSTAMO</td></tr>
        <tr>
            <td class="header-bg" style="width: 20%;">RESPONSABLE:</td>
            <td style="width: 30%;"> {{ $loan->borrower->teacher?->titulo }}{{ $loan->borrower->apellidoPaterno }} {{ $loan->borrower->apellidoMaterno }} {{ $loan->borrower->nombres }}</td>
            <td class="header-bg" style="width: 20%;">C.I.:</td>
            <td style="width: 30%;">{{ $loan->borrower->cedula_identidad }}</td>
        </tr>
        <tr>
            <td class="header-bg">TIPO:</td>
            <td>
            {{
            $loan->borrower->teacher
                ? 'DOCENTE'
                : ($loan->borrower->assistant ? 'AUXILIAR' : 'ESTUDIANTE')
            }}</td>
            <td class="header-bg">MATERIA:</td>
            <td>{{ $loan->subject->nombre_materia }} ({{ $loan->subject->sigla }})</td>
        </tr>
    </table>

    <!-- CRONOLOGÍA -->
    <table class="table">
        <tr><td colspan="3" class="section-title">DETALLE DE TIEMPOS</td></tr>
        <tr class="header-bg" style="text-align: center;">
            <td>REGISTRO DE SALIDA</td>
            <td>RETORNO PREVISTO</td>
            <td>RETORNO REAL</td>
        </tr>
        <tr style="text-align: center;">
            <td>
                {{ $loan->fecha_salida }}<br>
                <span class="text-blue">{{ $loan->hora_inicio }}</span>
            </td>
            <td>
                {{ $loan->fecha_retorno_prevista }}<br>
                <span style="color: #c05621;">{{ $loan->hora_fin_prevista }}</span>
            </td>
            <td>
                @if($loan->loanReturns)
                    {{ $loan->loanReturns->fecha_retorno->format('d/m/Y') }}<br>
                    <span style="color: #2f855a; font-weight: bold;">{{ $loan->loanReturns->hora_fin }}</span>
                @else
                    <span style="color: #666;">Sin registro</span>
                @endif
            </td>
        </tr>
    </table>

    <!-- EQUIPOS Y HERRAMIENTAS -->
    <table class="table">
        <thead>
            <tr class="section-title"><td colspan="3">EQUIPOS Y HERRAMIENTAS DEVUELTOS</td></tr>
            <tr class="header-bg">
                <th style="width: 50%;">Descripción del Item</th>
                <th style="width: 25%;">Código / QR</th>
                <th style="width: 25%;">Estado Recepción</th>
            </tr>
        </thead>
        <tbody>
            @php $return = $loan->loanReturns; @endphp
            @if($return)
                @foreach($return->returnDetails as $detail)
                <tr>
                    <td>
                        <strong>
                            {{ str_contains($detail->returnable_type, 'Equipment') ? $detail->returnable->nombre_equipo : $detail->returnable->nombre_herramienta }}
                        </strong><br>
                        <small style="color: #666;">
                            ({{ str_contains($detail->returnable_type, 'Equipment') ? 'EQUIPO' : 'HERRAMIENTA' }})
                        </small>

                        @if($detail->returnDetailAccessories->isNotEmpty())
                            <div style="margin-left: 10px; font-size: 8pt; margin-top: 4px;">
                                @foreach($detail->returnDetailAccessories as $rda)
                                    <span>• {{ $rda->accessory->nombre_accesorio }} ({{ $rda->estado_accesorio }})</span><br>
                                @endforeach
                            </div>
                        @endif
                    </td>
                    <td style="text-align: center;">{{ $detail->returnable->codigo_qr ?? 'N/A' }}</td>
                    <td style="text-align: center;">
                        <span class="badge">{{ strtoupper($detail->estado_devolucion) }}</span>
                    </td>
                </tr>
                @endforeach
            @endif
        </tbody>
    </table>

    <!-- OBSERVACIONES -->
    <table class="table">
    <div style="font-weight: bold; margin-bottom: 5px; text-transform: uppercase;">Notas de recepción final:</div>
        <tr>
            <td style="height: 60px; vertical-align: top; font-style: italic;">
                {{ $loan->loanReturns->observacion ?? 'El préstamo fue devuelto sin observaciones adicionales.' }}
            </td>
        </tr>
    </table>

    <!-- FIRMAS -->
    <div class="footer">
        <div class="signature-box">
            Firma Prestatario<br>
            <strong>
                {{ $loan->borrower->teacher?->titulo }}
                {{ $loan->borrower->apellidoPaterno }}
                {{ $loan->borrower->apellidoMaterno }}
                {{ $loan->borrower->nombres }}
            </strong><br>
            C.I. {{ $loan->borrower->cedula_identidad }}
        </div>
        <div class="signature-box">
            Encargado que Entregó<br>
            <strong>{{ $loan->user->name ?? '—' }}</strong><br>
            <span style="font-size:8pt; color:#666;">Registró el préstamo</span>
        </div>
        <div class="signature-box">
            Encargado que Recibió<br>
            <strong>{{ $loan->loanReturns?->user?->name ?? '—' }}</strong><br>
            <span style="font-size:8pt; color:#666;">Recepcionó la devolución</span>
        </div>
    </div>

    <div style="margin-top: 30px; font-size: 8pt; text-align: center; color: #666; font-style: italic;">
        Sistema de Gestión de Inventarios - Carrera de Mecánica Automotriz
    </div>
</body>
</html>
