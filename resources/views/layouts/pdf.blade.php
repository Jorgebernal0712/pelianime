{{-- MAESTRA PARA REPORTES PDF (DomPDF solo admite CSS sencillo) --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>@yield('title')</title>
    <style>
        @page { margin: 90px 40px 60px 40px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1f1b2e; }
        header { position: fixed; top: -70px; left: 0; right: 0; height: 55px; border-bottom: 3px solid #8b5cf6; }
        header .marca { font-size: 20px; font-weight: bold; color: #8b5cf6; }
        header .sub { color: #666; font-size: 10px; }
        footer { position: fixed; bottom: -40px; left: 0; right: 0; height: 25px; color: #888; font-size: 9px; border-top: 1px solid #ddd; padding-top: 5px; }
        footer .pagina:after { content: "Página " counter(page); }
        h1 { font-size: 16px; margin: 0 0 6px; }
        .resumen { margin-bottom: 12px; color: #444; }
        .resumen span { display: inline-block; background: #f3f0ff; border: 1px solid #ddd6fe; padding: 4px 10px; border-radius: 4px; margin-right: 6px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #8b5cf6; color: #fff; text-align: left; padding: 6px; font-size: 9px; text-transform: uppercase; }
        td { padding: 6px; border-bottom: 1px solid #e5e5e5; }
        tr:nth-child(even) td { background: #faf9ff; }
        .num { text-align: center; }
        .estrella { color: #d97706; font-weight: bold; }
    </style>
</head>
<body>
    <header>
        <table style="width:100%"><tr>
            <td style="border:0; padding:0; background:none"><div class="marca">PeliAnime</div><div class="sub">@yield('title')</div></td>
            <td style="border:0; padding:0; background:none; text-align:right" class="sub">
                Generado: {{ $fecha->format('d/m/Y H:i') }}<br>Por: {{ $usuario->name }}
            </td>
        </tr></table>
    </header>
    <footer>
        <span>{{ config('app.name') }} · {{ config('app.url') }}</span>
        <span class="pagina" style="float:right"></span>
    </footer>

    @yield('content')
</body>
</html>
