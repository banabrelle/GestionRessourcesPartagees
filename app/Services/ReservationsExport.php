<?php
namespace App\Exports;
use App\Models\Reservation;
use Maatwebsite\Excel\Concerns\{FromCollection,WithHeadings,WithStyles,WithTitle,WithEvents};
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\{Fill,Alignment};

class ReservationsExport implements FromCollection,WithHeadings,WithStyles,WithTitle,WithEvents
{
    public function __construct(private array $filters=[]){}

    public function collection()
    {
        return Reservation::with(['personnel.user','ressource'])
            ->orderByDesc('created_at')->get()
            ->map(fn($r)=>[
                $r->id, $r->personnel->user->name??'', $r->personnel->user->matricule??'',
                $r->personnel->division->code??'', $r->ressource->nom??'',
                ucfirst($r->ressource->type_ressource??''),
                $r->date_debut->format('d/m/Y H:i'), $r->date_fin->format('d/m/Y H:i'),
                $r->duree_heures.'h', $r->statut_label, $r->motif,
            ]);
    }

    public function headings(): array
    {
        return ['#','Nom','Matricule','Division','Ressource','Type','Début','Fin','Durée','Statut','Motif'];
    }

    public function title(): string { return 'Réservations'; }

    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet): array
    {
        return [5=>['font'=>['bold'=>true],'fill'=>['fillType'=>Fill::FILL_SOLID,'color'=>['rgb'=>'1F3864']],'font'=>['color'=>['rgb'=>'FFFFFF'],'bold'=>true]]];
    }

    public function registerEvents(): array
    {
        return [AfterSheet::class => function(AfterSheet $e){
            // Insérer 4 lignes d'en-tête CENADI
            $e->sheet->getDelegate()->insertNewRowBefore(1,4);
            $e->sheet->mergeCells('A1:K1');
            $e->sheet->mergeCells('A2:K2');
            $e->sheet->mergeCells('A3:K3');
            $e->sheet->setCellValue('A1','REPUBLIQUE DU CAMEROUN / MINFI / CENADI — GERES-CENADI');
            $e->sheet->setCellValue('A2','HISTORIQUE DES RÉSERVATIONS DE RESSOURCES PARTAGÉES');
            $e->sheet->setCellValue('A3','Généré le: '.now()->format('d/m/Y H:i').' | Par: '.auth()->user()->name);
            $e->sheet->getStyle('A1:K3')->applyFromArray([
                'font'=>['bold'=>true,'size'=>11],
                'alignment'=>['horizontal'=>Alignment::HORIZONTAL_CENTER],
                'fill'=>['fillType'=>Fill::FILL_SOLID,'color'=>['rgb'=>'EBF5FB']],
            ]);
            foreach(range('A','K') as $col) $e->sheet->getColumnDimension($col)->setAutoSize(true);
        }];
    }
}
