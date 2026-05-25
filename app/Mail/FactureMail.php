<?php

namespace App\Mail;

use App\Models\Vente;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class FactureMail extends Mailable
{
    use Queueable, SerializesModels;

    public $vente;
    public $pdf;

    public function __construct(Vente $vente)
    {
        $this->vente = $vente;
        
        // Générer le PDF
        $pdfContent = Pdf::loadView('pdf.facture', compact('vente'))->output();
        $this->pdf = $pdfContent;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Facture ' . $this->vente->numero_facture . ' - Pharmacie Intelligente',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.facture',
            with: [
                'vente' => $this->vente,
                'client' => $this->vente->client
            ]
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->pdf, 'facture_' . $this->vente->numero_facture . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}