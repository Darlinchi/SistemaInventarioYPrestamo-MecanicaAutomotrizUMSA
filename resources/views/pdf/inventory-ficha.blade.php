<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ficha Técnica - {{ $item->nombre_item }}</title>
    <style>
        @page { margin: 1cm; }
        body { font-family: 'Helvetica', sans-serif; font-size: 9pt; line-height: 1.3; color: #333; }

        /* Tablas */
        .table { width: 100%; border-collapse: collapse; margin-bottom: 10px; table-layout: fixed; }
        .table td { border: 1px solid #000; padding: 6px; vertical-align: middle; word-wrap: break-word; }

        /* Estilos de Celdas */
        .header-bg { background-color: #f2f2f2; font-weight: bold; text-align: center; }
        .label { background-color: #f9f9f9; font-weight: bold; text-transform: uppercase; font-size: 8pt; width: 18%; }
        .value { width: 32%; } /* Ajuste para que sumen 50% por par */

        .title { font-size: 11pt; font-weight: bold; text-align: center; }
        .logo { width: 70px; text-align: center; }

        /* Caja de Foto */
        .photo-box { width: 200px; text-align: center; }
        .photo-box img { max-width: 180px; max-height: 150px; border: 1px solid #ddd; }

        /* Secciones */
        .section-title { background-color: #1a3a5a; color: white; font-weight: bold; text-align: center; text-transform: uppercase; padding: 4px; font-size: 9pt; }
        .checkbox { border: 1px solid #000; width: 10px; height: 10px; display: inline-block; text-align: center; line-height: 10px; font-size: 7pt; margin-right: 3px; }
    </style>
</head>
<body>

    <!-- CABECERA -->
    <table class="table">
        <tr>
            <td rowspan="2" class="logo" style="width: 15%;">
                <img src="{{ public_path('images/logo-carrera.png') }}" style="width: 55px;">
            </td>
            <td class="title" style="width: 55%;">FICHA TÉCNICA DE {{ isset($item->equipment) ? 'EQUIPO' : 'HERRAMIENTA' }}</td>
            <td class="header-bg" style="width: 15%;">CÓDIGO:</td>
            <td style="width: 15%;">{{ $item->codigo_qr }}</td>
        </tr>
        <tr>
            <td style="text-align: center; font-weight: bold; font-size: 8pt;">CARRERA DE MECÁNICA AUTOMOTRIZ - UMSA</td>
            <td class="header-bg">FECHA:</td>
            <td>{{ date('d/m/Y') }}</td>
        </tr>
    </table>

    <!-- IDENTIFICACIÓN E IMAGEN -->
    <table class="table">
        <tr>
            <td style="width: 65%; padding: 0; border: none;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td class="label" style="width: 30%;">TIPO:</td>
                        <td>{{ isset($item->equipment) ? 'Equipo de Taller' : 'Herramienta de Mano' }}</td>
                    </tr>
                    <tr>
                        <td class="label">UBICACIÓN:</td>
                        <td>{{ $item->ubicacion_item }}</td>
                    </tr>
                    <tr>
                        <td class="label">ELEMENTO:</td>
                        <td style="font-weight: bold;">{{ strtoupper($item->nombre_item) }}</td>
                    </tr>
                    <tr>
                        <td class="label">ESTADO ACTUAL:</td>
                        <td>{{ $item->equipment->estado_equipo ?? $item->tool->estado_herramienta ?? 'N/A' }}</td>
                    </tr>
                </table>
            </td>
            <td class="photo-box" style="width: 35%;">
                @php
                    $foto = $item->foto_equipo ?? $item->foto_herramienta ?? null;
                @endphp
                @if($foto && file_exists(public_path('storage/' . $foto)))
                    <img src="{{ public_path('storage/' . $foto) }}">
                @else
                    <div style="color: #ccc; font-size: 8pt;">FOTOGRAFÍA NO DISPONIBLE</div>
                @endif
            </td>
        </tr>
    </table>

    <!-- ESPECIFICACIONES TÉCNICAS -->
    <table class="table">
        <tr><td colspan="6" class="section-title">ESPECIFICACIONES TÉCNICAS</td></tr>
        @if(isset($item->equipment))
            <tr>
                <td class="label">MARCA:</td><td class="value">{{ $item->equipment->marca }}</td>
                <td class="label">MODELO:</td><td class="value">{{ $item->equipment->modelo }}</td>
                <td class="label">SERIE:</td><td class="value">{{ $item->equipment->serie }}</td>
            </tr>
            <tr>
                <td class="label">RUBRO:</td><td class="value">{{ $item->equipment->rubro }}</td>
                <td class="label">COLOR:</td><td class="value">{{ $item->equipment->color }}</td>
                <td class="label">FECHA ADQ.:</td>
                <td class="value">
                    {{ $item->equipment->fecha_adquisicion ? \Carbon\Carbon::parse($item->equipment->fecha_adquisicion)->format('d/m/Y') : 'N/A' }}
                </td>
            </tr>
        @else
            <tr>
                <td class="label" style="width: 25%;">MARCA/MODELO:</td>
                <td colspan="5">{{ $item->tool->marca_modelo }}</td>
            </tr>
        @endif
    </table>

    <!-- TECNOLOGÍA (Solo Equipos) -->
    @if(isset($item->equipment))
    <table class="table">
        <tr>
            <td class="label" style="width: 25%;">TECNOLOGÍA PREDOMINANTE:</td>
            <td>
                <span class="checkbox">{{ $item->equipment->rubro == 'Electrónico' ? 'X' : '' }}</span> Electrónico &nbsp;
                <span class="checkbox">{{ $item->equipment->rubro == 'Eléctrico' ? 'X' : '' }}</span> Eléctrico &nbsp;
                <span class="checkbox"></span> Mecánico &nbsp;
                <span class="checkbox"></span> Hidráulico &nbsp;
                <span class="checkbox"></span> Neumático
            </td>
        </tr>
    </table>
    @endif

    <!-- ACCESORIOS -->
    @if($item->equipment && count($item->equipment->accessories) > 0)
    <table class="table">
        <tr><td class="section-title">ACCESORIOS QUE DISPONE EL EQUIPO</td></tr>
        @foreach($item->equipment->accessories as $acc)
        <tr>
            <td>• {{ $acc->nombre_accesorio }} (Estado: {{ $acc->estado_accesorio }})</td>
        </tr>
        @endforeach
    </table>
    @endif

    <!-- DESCRIPCIÓN Y OBSERVACIONES -->
    <table class="table">
        <tr><td colspan="2" class="section-title">DESCRIPCIÓN Y OBSERVACIONES</td></tr>
        <tr>
            <td class="label" style="height: 40px;">DESCRIPCIÓN:</td>
            <td style="vertical-align: top;">{{ $item->descripcion_item ?? 'Sin descripción.' }}</td>
        </tr>
        <tr>
            <td class="label" style="height: 40px;">OBSERVACIÓN:</td>
            <td style="vertical-align: top;">{{ $item->observacion_item ?? 'Ninguna.' }}</td>
        </tr>
    </table>

    <!-- FIRMAS -->
    <table style="width: 100%; margin-top: 60px; border: none;">
        <tr>
            <td style="text-align: center; width: 45%; border: none; border-top: 1px solid #000;">
                <br><strong>ELABORADO POR</strong><br>
                {{ auth()->user()->name }}
            </td>
            <td style="width: 10%;"></td>
            <td style="text-align: center; width: 45%; border: none; border-top: 1px solid #000;">
                <br><strong>ENCARGADO DE TALLER</strong><br>
                {{ auth()->user()->name }}
            </td>
            <td style="width: 10%; border: none;"></td>
        </tr>
    </table>

    <div style="margin-top: 30px; font-size: 8pt; text-align: center; color: #666; font-style: italic;">
        Sistema de Gestión de Inventarios - Carrera de Mecánica Automotriz
    </div>
</body>
</html>
