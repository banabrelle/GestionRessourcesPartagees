<?php
namespace App\Http\Controllers;
use App\Models\HistoriqueEmprunt;
use App\Services\BonEmpruntService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class BonEmpruntController extends Controller
{
    public function index()
    {
        $bons = HistoriqueEmprunt::with(['reservation.personnel.user','reservation.ressource'])
            ->orderByDesc('created_at')->paginate(15);
        return view('bons.index',compact('bons'));
    }

    public function telecharger(HistoriqueEmprunt $bon)
    {
        $bon->load('reservation.personnel.user','reservation.ressource',
                   'reservation.validePar.user','reservation.valideParAdmin.user',
                   'reservation.personnel.division');

        $pdf = Pdf::loadView('bons.pdf',['bon'=>$bon])->setPaper('A4');
        return $pdf->download('bon-'.$bon->numero_bon.'.pdf');
    }

    public function apercu(HistoriqueEmprunt $bon)
    {
        $bon->load('reservation.personnel.user','reservation.ressource',
                   'reservation.personnel.division');
        return view('bons.pdf',compact('bon'));
    }

    public function enregistrerRetour(Request $request, HistoriqueEmprunt $bon)
    {
        $request->validate([
            'etat_retour' => 'required|in:rendu_bon_etat,rendu_endommage,perdu',
            'notes'       => 'nullable|string|max:500',
        ]);
        $bon->update([
            'date_retour_relle' => now(),
            'etat_retour'       => $request->etat_retour,
            'notes'             => $request->notes,
        ]);
        // Libérer la ressource
        $bon->reservation->ressource->update(['etat'=>'disponible']);
        $bon->reservation->update(['statut'=>'termine']);
        return back()->with('success','Retour enregistré. Ressource disponible.');
    }
}


