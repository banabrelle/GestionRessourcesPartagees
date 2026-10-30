<?php
namespace App\Services;
use App\Models\HistoriqueEmprunt;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class BonEmpruntService
{
    public function genererPdf(HistoriqueEmprunt $bon): string
    {
        $bon->load('reservation.personnel.user','reservation.ressource','reservation.personnel.division');
        $pdf  = Pdf::loadView('bons.pdf',['bon'=>$bon])->setPaper('A4');
        $path = 'bons/'.$bon->numero_bon.'.pdf';
        Storage::disk('public')->put($path,$pdf->output());
        $bon->update(['fichier_pdf'=>$path]);
        return $path;
    }
}
