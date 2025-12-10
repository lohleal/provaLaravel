<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Relatório de Produtos - Sistema Aula </title>
    <style>

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
            margin: 1cm 0.5cm;
            color: #000;
        }
        .texto-marca-dagua {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 7em;
            color: #888;
            opacity: 0.3;
            pointer-events: none;
            white-space: nowrap;
            z-index: 0;
        }
        .texto-restrito-cima {
            position: absolute;
            top: 0px;
            left: 130px;
            border: 1px solid #999;
            color: #999;
            font-size: 18px;
            font-weight: bolder;
            text-align: center;
            padding: 1px 8px;
        }
        .texto-restrito-baixo {
            position: absolute;
            bottom: 0px;
            left: 130px;
            border: 1px solid #999;
            color: #999;
            font-size: 20px;
            font-weight: bolder;
            text-align: center;
            padding: 1px 8px;
        }

        .header {
            text-align: center;
            line-height: 1.2;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }
        .header .main-title { font-size: 11pt; }

        .info-table, .identification-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid black;
            margin-top: 0.5rem;
            margin-bottom: 1rem;
            width: 100%;
        }
        .info-table td, .identification-table td {
            border: 1px solid black;
            padding: 3px 6px;
            vertical-align: top;
        }
        .info-table .label {
            font-weight: bold;
            width: 150px;
            text-transform: uppercase;
        }
        .identification-header {
            border: 1px solid black;
            text-align: center;
            font-weight: bold;
            padding: 5px;
            margin-bottom: -9px;
            text-transform: uppercase;
            margin-top: 10px;
        }
        .identification-table .photo-cell {
            width: 130px;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
            text-transform: uppercase;
        }
        .identification-table .inner-table {
            width: 100%;
        }
        .identification-table .inner-table td {
            border: none;
            padding: 1px 4px;
        }
        .identification-table .inner-table .label {
            font-weight: bold;
            width: 110px;
            text-transform: uppercase;
        }
        .block-label {
            font-weight: bold;
            text-transform: uppercase;
            border-left: 1px solid black;
            border-right: 1px solid black;
            border-top: 1px solid black;
            text-align: center;
        }
        .block-content {
            border-left: 1px solid black;
            border-right: 1px solid black;
            border-bottom: 1px solid black;
            padding: 6px;
            min-height: 100px;
            white-space: pre-wrap;
        }

    </style>
</head>
<body>
    <hr>
    <table style="margin: 0px auto; width: 100%">
        <tbody>
            <tr>
                <td style="width: 75px; text-align: left;">
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('/assets/img/logo_ifpr.png'))) }}" width="78" height="78" style="border-radius: 25%;">
                </td>
                <td style="width: 1fr; text-align: center;">
                    <div style="font-size: 18px;">Relatório Mensal de Vendas</div>
                    <div style="font-size: 18px; font-weight: bold;">LOMI COFFEE</div>
                </td>
                <td style="width: 75px; text-align: right;">
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('/assets/img/logo_ifpr.png'))) }}" width="90" height="90" style="border-radius: 25%;">
                </td>
            </tr>
        </tbody>
    </table>
    <div style="width: 100%; text-align: center; margin-top: 7px;">
        <span style="font-size: 18px; font-weight: bold; font-style: italic;"></span>
    </div>
    <hr>

    <div class="identification-header">PRODUTOS</div>
    @foreach($produtos as $produto)
        <table class="info-table identification-section">
            <tbody>
                <tr>
                    <td class="photo-cell" >
                        @if($produto->foto)
                            <img src="{{ public_path('storage/' . $produto->foto) }}" style="width: 120px; height: auto;">
                        @else
                            FOTO
                        @endif
                    </td>
                    <td>
                       <table class="inner-table">
    <tr>
        <td class="label table-label">NOME:</td>
        <td style="width: 305px;">{{ $produto->nome }}</td>
    </tr>
    <tr>
        <td class="label">CATEGORIA:</td>
        <td>{{ $produto->curso->nome ?? '-' }}</td>
    </tr>
    <tr>
        <td class="label">VALOR UNITÁRIO:</td>
        <td>R$ {{ number_format($produto->valor, 2, ',', '.') }}</td>
    </tr>
 <tr>
    <td class="label">QUANT. VENDIDA:</td>
    <td>{{ $produto->quantidade_vendida ?? 0 }}</td>
</tr>
<tr>
    <td class="label">VALOR TOTAL:</td>
    <td>R$ {{ number_format($produto->valor_total ?? 0, 2, ',', '.') }}</td>
</tr>


</table>

                    </td>
                </tr>
            </tbody>
        </table>
    @endforeach

</body>
</html>