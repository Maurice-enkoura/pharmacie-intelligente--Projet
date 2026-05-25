<!-- resources/views/emails/facture.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Facture {{ $vente->numero_facture }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 2px solid #4ade80;
        }
        .header h1 {
            color: #166534;
            margin: 0;
        }
        .content {
            padding: 20px 0;
        }
        .info {
            background: #f3f4f6;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        .info p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
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
            font-size: 18px;
            font-weight: bold;
            margin-top: 15px;
        }
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #666;
        }
        .btn {
            display: inline-block;
            background-color: #4ade80;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
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
    <div class="container">
        <div class="header">
            <h1>🏥 Pharmacie Intelligente</h1>
            <p>Dakar, Sénégal</p>
        </div>

        <div class="content">
            <h2>Bonjour {{ $vente->client->prenom }} {{ $vente->client->nom }},</h2>
            
            <p>Nous vous remercions pour votre achat. Voici le détail de votre facture :</p>

            <div class="info">
                <p><strong>📄 Facture N° :</strong> {{ $vente->numero_facture }}</p>
                <p><strong>📅 Date :</strong> {{ date('d/m/Y H:i', strtotime($vente->created_at)) }}</p>
                <p><strong>💰 Montant total :</strong> {{ number_format($vente->montant_total, 0, ',', ' ') }} FCFA</p>
                <p><strong>💳 Mode de paiement :</strong> 
                    {{ $vente->mode_paiement == 'especes' ? 'Espèces' : ($vente->mode_paiement == 'orange_money' ? 'Orange Money' : ($vente->mode_paiement == 'wave' ? 'Wave' : 'Carte')) }}
                </p>
            </div>

            <h3>Détail des produits :</h3>
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
                        <td colspan="4" class="text-right"><strong>Total :</strong></td>
                        <td class="text-right"><strong>{{ number_format($vente->montant_total, 0, ',', ' ') }} FCFA</strong></td>
                    </tr>
                </tfoot>
            </table>

            <p>Vous trouverez ci-joint votre facture en format PDF.</p>
            
            <p style="text-align: center;">
                <a href="{{ url('/') }}" class="btn">Visiter notre site</a>
            </p>
        </div>

        <div class="footer">
            <p>Merci de votre confiance ! À bientôt à la Pharmacie Intelligente</p>
            <p>Horaires : Lun-Sam 8h-21h | Dimanche 9h-13h</p>
            <p>© {{ date('Y') }} Pharmacie Intelligente - Tous droits réservés</p>
        </div>
    </div>
</body>
</html>