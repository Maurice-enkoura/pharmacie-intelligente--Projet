<!-- resources/views/pdf/facture.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Facture {{ $vente->numero_facture }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #4ade80;
        }
        .header h1 {
            color: #166534;
            margin: 0;
        }
        .header p {
            color: #666;
            margin: 5px 0 0;
        }
        .info {
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
        }
        .info-box {
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
            width: 48%;
        }
        .info-box h3 {
            margin: 0 0 10px;
            color: #166534;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #4ade80;
            color: white;
        }
        .total {
            text-align: right;
            margin-top: 20px;
        }
        .footer {
            text-align: center;
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏥 Pharmacie Intelligente</h1>
        <p>Dakar, Sénégal - Tel: +221 77 000 00 00</p>
    </div>

    <div class="info">
        <div class="info-box">
            <h3>📄 Facture N° {{ $vente->numero_facture }}</h3>
            <p>Date: {{ date('d/m/Y H:i', strtotime($vente->created_at)) }}</p>
            <p>Caissier: {{ $vente->user->name }}</p>
        </div>
        <div class="info-box">
            <h3>👤 Client</h3>
            <p><strong>{{ $vente->client->prenom }} {{ $vente->client->nom }}</strong></p>
            <p>Tel: {{ $vente->client->telephone }}</p>
            @if($vente->client->email)
                <p>Email: {{ $vente->client->email }}</p>
            @endif
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Médicament</th>
                <th>Dosage</th>
                <th class="text-center">Qté</th>
                <th class="text-right">Prix unitaire</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($vente->ligneVentes as $ligne)
            <tr>
                <td>{{ $ligne->medicament->nom }}</td>
                <td>{{ $ligne->medicament->dosage }}</td>
                <td class="text-center">{{ $ligne->quantite }}</td>
                <td class="text-right">{{ number_format($ligne->prix_unitaire, 0, ',', ' ') }} FCFA</td>
                <td class="text-right">{{ number_format($ligne->sous_total, 0, ',', ' ') }} FCFA</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-right"><strong>Total TTC :</strong></td>
                <td class="text-right"><strong>{{ number_format($vente->montant_total, 0, ',', ' ') }} FCFA</strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="info">
        <div class="info-box">
            <h3>💳 Paiement</h3>
            <p>Montant payé: {{ number_format($vente->montant_paye, 0, ',', ' ') }} FCFA</p>
            <p>Monnaie rendue: {{ number_format($vente->monnaie_rendue, 0, ',', ' ') }} FCFA</p>
            <p>Mode: {{ $vente->mode_paiement == 'especes' ? 'Espèces' : ($vente->mode_paiement == 'orange_money' ? 'Orange Money' : ($vente->mode_paiement == 'wave' ? 'Wave' : 'Carte')) }}</p>
        </div>
        <div class="info-box">
            <h3>📋 Informations</h3>
            <p>Type: {{ $vente->type_vente == 'avec_ordonnance' ? 'Avec ordonnance' : 'Sans ordonnance' }}</p>
            @if($vente->ordonnance_ref)
                <p>Ordonnance: {{ $vente->ordonnance_ref }}</p>
            @endif
        </div>
    </div>

    <div class="footer">
        <p>Merci de votre confiance ! À bientôt à la Pharmacie Intelligente</p>
        <p>Horaires : Lun-Sam 8h-21h | Dimanche 9h-13h</p>
        <p>© {{ date('Y') }} Pharmacie Intelligente - Tous droits réservés</p>
    </div>
</body>
</html>