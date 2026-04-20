<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 10px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #1a3a5a; padding-bottom: 10px; }
        .title { font-size: 16px; font-weight: bold; color: #1a3a5a; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th { background-color: #1a3a5a; color: white; padding: 6px; text-transform: uppercase; font-size: 8px; text-align: left; }
        td { padding: 6px; border-bottom: 1px solid #eee; vertical-align: top; word-wrap: break-word; }
        .item-name { font-weight: bold; color: #1a3a5a; display: block; }
        .meta-text { color: #666; font-size: 8.5px; margin-top: 2px; }
        .acc-list { color: #2563eb; font-size: 8px; font-style: italic; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Inventario General - Mecánica Automotriz</div>
        <div style="font-size: 9px; margin-top: 5px;">Reporte: {{ $category }} | Generado: {{ $date }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="30%">Item / QR</th>
                <th width="20%">Marca / Modelo</th>
                <th width="30%">Detalles Técnicos / Accesorios</th>
                <th width="10%">Ubicación</th>
                <th width="10%">Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>
                    <span class="item-name">{{ $item['nombre'] }}</span>
                    <span class="meta-text">QR: {{ $item['codigo'] ?? 'S/N' }}</span>
                </td>
                <td>{{ $item['marca_modelo'] }}</td>
                <td>
                    @if($item['tipo'] === 'EQUIPO')
                        <div class="meta-text"><strong>F. Adq:</strong> {{ $item['fecha_adq'] }}</div>
                        @if(count($item['accesorios']) > 0)
                            <div class="acc-list"><strong>Acc:</strong> {{ implode(', ', $item['accesorios']) }}</div>
                        @else
                            <div class="meta-text" style="font-style: italic;">Sin accesorios</div>
                        @endif
                    @else
                        <div class="meta-text"><strong>Obs:</strong> {{ $item['observacion'] }}</div>
                    @endif
                </td>
                <td>{{ $item['ubicacion'] }}</td>
                <td style="font-weight: bold;">{{ $item['estado'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
