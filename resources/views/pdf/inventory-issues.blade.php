<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 10px; color: #333; margin: 30px; }
        .header { border-left: 5px solid #b91c1c; padding-left: 15px; margin-bottom: 25px; }
        .title { font-size: 18px; font-weight: bold; color: #b91c1c; text-transform: uppercase; }
        .subtitle { font-size: 10px; color: #666; margin-top: 5px; }

        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #fef2f2; color: #b91c1c; border: 1px solid #fee2e2; padding: 10px; text-transform: uppercase; font-size: 8px; text-align: left; }
        td { padding: 10px; border: 1px solid #f3f4f6; vertical-align: top; }

        .item-info { font-weight: bold; color: #111827; }
        .qr-code { font-family: monospace; color: #b91c1c; font-size: 9px; }
        .status-badge { font-weight: bold; text-transform: uppercase; font-size: 8px; color: #b91c1c; }
        .obs-text { font-style: italic; color: #4b5563; font-size: 9px; line-height: 1.4; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 8px; color: #9ca3af; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Reporte de Incidencias y Activos Críticos</div>
        <div class="subtitle">Mecánica Automotriz - UMSA | Control de Inventario</div>
        <div class="subtitle">Generado el: {{ $date }}</div>
    </div>

    <div style="background-color: #fef2f2; padding: 10px; border-radius: 5px; color: #991b1b; font-size: 9px; font-weight: bold;">
        ATENCIÓN: Este documento lista únicamente los activos que se encuentran fuera de servicio, dañados o extraviados y requieren gestión de reposición o baja definitiva.
    </div>

    <table>
        <thead>
            <tr>
                <th width="30%">Ítem / Identificación</th>
                <th width="15%">Tipo</th>
                <th width="15%">Estado Actual</th>
                <th width="40%">Observación Técnica de la Incidencia</th>
            </tr>
        </thead>
        <tbody>
            @foreach($issues as $issue)
            <tr>
                <td>
                    <div class="item-info">{{ $issue['nombre'] }}</div>
                    <div class="qr-code">Cód: {{ $issue['codigo'] }}</div>
                </td>
                <td>{{ $issue['tipo'] }}</td>
                <td><span class="status-badge">{{ $issue['estado'] }}</span></td>
                <td class="obs-text">{{ $issue['observacion'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Sistema de Gestión de Inventario - Proyecto de Grado | Página 1
    </div>
</body>
</html>
