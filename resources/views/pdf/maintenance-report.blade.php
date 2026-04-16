<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe Técnico #{{ $maint->id }}</title>
    <style>
        @page { margin: 1.5cm; }
        body { font-family: 'Helvetica', sans-serif; font-size: 9pt; color: #333; line-height: 1.4; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .table td, .table th { border: 1px solid #000; padding: 8px; }
        .header-bg { background-color: #f2f2f2; font-weight: bold; width: 30%; }
        .title { font-size: 14pt; font-weight: bold; text-align: center; text-transform: uppercase; color: #1a3a5a; }
        .section-title { background-color: #1a3a5a; color: white; font-weight: bold; padding: 5px; text-align: center; text-transform: uppercase; }
        .info-box { border: 1px solid #000; padding: 10px; margin-bottom: 10px; min-height: 80px; }
        .status-badge { border: 1px solid #000; padding: 2px 8px; font-weight: bold; text-transform: uppercase; font-size: 8pt; }
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
                <div class="title">Informe de Servicio Técnico</div>
                <div style="font-weight: bold;">Taller de Mecánica Automotriz - UMSA</div>
            </td>
            <td style="border: none; width: 20%; text-align: right;">
                <div style="font-weight: bold;">N° Registro: {{ str_pad($maint->id, 5, '0', STR_PAD_LEFT) }}</div>
                <div>Emisión: {{ date('d/m/Y') }}</div>
            </td>
        </tr>
    </table>

    <!-- DATOS DEL EQUIPO -->
    <table class="table">
        <tr><td colspan="4" class="section-title">Información del Equipo Intervenido</td></tr>
        <tr>
            <td class="header-bg">EQUIPO:</td>
            <td style="width: 35%;">{{ $maint->equipment->nombre_equipo }}</td>
            <td class="header-bg">CÓDIGO QR:</td>
            <td style="width: 25%;">{{ $maint->equipment->codigo_qr }}</td>
        </tr>
        <tr>
            <td class="header-bg">MARCA/MODELO:</td>
            <td>{{ $maint->equipment->marca }} / {{ $maint->equipment->modelo }}</td>
            <td class="header-bg">N° SERIE:</td>
            <td>{{ $maint->equipment->serie }}</td>
        </tr>
    </table>

    <!-- DATOS DEL SERVICIO -->
    <table class="table">
        <tr><td colspan="4" class="section-title">Detalles del Mantenimiento</td></tr>
        <tr>
            <td class="header-bg">TIPO DE SERVICIO:</td>
            <td>Mantenimiento {{ $maint->tipo_mantenimiento }}</td>
            <td class="header-bg">EMPRESA RESP.:</td>
            <td>{{ $maint->companies->first()->nombre_empresa ?? 'Personal Interno' }}</td>
        </tr>
        <tr>
            <td class="header-bg">FECHA SALIDA:</td>
            <td>{{ $maint->fecha_mantenimiento }} ({{ $maint->hora_inicio }})</td>
            <td class="header-bg">FECHA RETORNO:</td>
            <td>{{ $maint->fecha_retorno ?? 'Pendiente' }} ({{ $maint->hora_fin ?? '--:--' }})</td>
        </tr>
        <tr>
            <td class="header-bg">ESTADO RESULTANTE:</td>
            <td colspan="3">
                <span class="status-badge">{{ $maint->estado_final_equipo ?? 'EN PROCESO' }}</span>
            </td>
        </tr>
    </table>

    <!-- DESCRIPCIÓN TÉCNICA -->
    <div style="font-weight: bold; margin-bottom: 5px; text-transform: uppercase;">Informe Técnico de Actividades:</div>
    <div class="info-box">
        {{ $maint->actividad ?? 'No se registraron detalles técnicos de la actividad realizada.' }}
    </div>

    <!-- FIRMAS -->
    <table style="width: 100%; margin-top: 60px; border: none;">
        <tr>
            <td style="text-align: center; width: 45%; border: none; border-top: 1px solid #000;">
                <br><strong>ENCARGADO DE TALLER</strong><br>
                {{ auth()->user()->name }}
            </td>
            <td style="width: 10%; border: none;"></td>
            <td style="text-align: center; width: 45%; border: none; border-top: 1px solid #000;">
                <br><strong>RESPONSABLE TÉCNICO</strong><br>
                Sello y Firma Empresa/Técnico
            </td>
        </tr>
    </table>

    <div style="margin-top: 30px; font-size: 8pt; text-align: center; color: #666; font-style: italic;">
        Sistema de Gestión de Inventarios - Carrera de Mecánica Automotriz
    </div>

</body>
</html>
