<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 9px; color: #333; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #1a3a5a; padding-bottom: 10px; }
        .title { font-size: 16px; font-weight: bold; color: #1a3a5a; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th { background-color: #f8fafc; color: #1a3a5a; border: 1px solid #cbd5e1; padding: 6px; text-transform: uppercase; font-size: 7.5px; }
        td { padding: 6px; border: 1px solid #e2e8f0; vertical-align: top; word-wrap: break-word; }

        .status-badge { font-weight: bold; font-size: 8px; }
        .text-danger { color: #dc2626; } /* Rojo para Dañado/Extraviado */
        .text-success { color: #16a34a; } /* Verde para Disponible */
        .text-warning { color: #ca8a04; } /* Naranja para Activo */

        .obs-box { background-color: #f9fafb; font-style: italic; color: #4b5563; padding: 4px; margin-top: 4px; border-left: 2px solid #d1d5db; }
        .item-row { border-bottom: 1px solid #f1f5f9; padding: 2px 0; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Historial de Movimientos y Estado de Devolución</div>
        <div style="font-size: 10px; margin-top: 5px;">Carrera de Mecánica Automotriz - UMSA | {{ $date }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="15%">Responsable / Materia</th>
                <th width="35%">Detalle de Ítems y Estados</th>
                <th width="10%">F. Salida</th>
                <th width="10%">F. Retorno</th>
                <th width="30%">Observación Final de Recepción</th>
            </tr>
        </thead>
        <tbody>
            @foreach($history as $loan)
            <tr>
                <td>
                    <strong>{{ $loan['responsable'] }}</strong><br>
                    <small style="color: #1a3a5a;">{{ $loan['materia'] }}</small>
                </td>
                <td>
                    @foreach($loan['items'] as $item)
                        <div class="item-row">
                            • {{ $item['nombre'] }}
                            <span style="font-size: 7px; color: #666;">({{ $item['tipo'] }})</span>
                            <span class="status-badge {{ in_array($item['estado_dev'], ['Dañado', 'Extraviado', 'Incompleto']) ? 'text-danger' : 'text-success' }}">
                                [{{ $item['estado_dev'] }}]
                            </span>
                        </div>
                    @endforeach
                </td>
                <td align="center">{{ $loan['salida'] }}</td>
                <td align="center">
                    <span class="{{ $loan['retorno'] == 'PENDIENTE' ? 'text-warning' : '' }}">
                        {{ $loan['retorno'] }}
                    </span>
                </td>
                <td>
                    <div class="obs-box">
                        {{ $loan['observacion'] }}
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
