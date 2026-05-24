<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Medicament;
use App\Models\Categorie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet as PhpSpreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as PhpSpreadsheetWriter;
use PhpOffice\PhpSpreadsheet\IOFactory;

class MedicamentController extends Controller
{
    // Liste des médicaments avec filtres
    public function index(Request $request)
    {
        $query = Medicament::with('categorie');
        
        if ($request->has('categorie_id') && $request->categorie_id) {
            $query->where('categorie_id', $request->categorie_id);
        }
        
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('nom', 'like', '%' . $request->search . '%')
                  ->orWhere('dci', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->has('ordonnance_requise') && $request->ordonnance_requise !== null) {
            $query->where('ordonnance_requise', $request->ordonnance_requise);
        }
        
        $medicaments = $query->paginate(15);
        
        return response()->json($medicaments);
    }
    
    // Détail d'un médicament
    public function show($id)
    {
        $medicament = Medicament::with(['categorie', 'stockLots' => function($q) {
            $q->orderBy('date_peremption', 'asc');
        }])->findOrFail($id);
        
        return response()->json($medicament);
    }
    
    // Créer un médicament
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nom' => 'required|string|max:255',
                'dci' => 'required|string|max:255',
                'forme' => 'required|string|max:255',
                'dosage' => 'required|string|max:255',
                'categorie_id' => 'required|exists:categories,id',
                'prix_achat' => 'required|numeric|min:0',
                'prix_vente' => 'required|numeric|min:0',
                'seuil_alerte' => 'nullable|integer|min:0',
                'stock_actuel' => 'nullable|integer|min:0',
                'ordonnance_requise' => 'nullable|boolean'
            ]);
            
            $medicament = Medicament::create([
                'nom' => $validated['nom'],
                'dci' => $validated['dci'],
                'forme' => $validated['forme'],
                'dosage' => $validated['dosage'],
                'categorie_id' => $validated['categorie_id'],
                'prix_achat' => $validated['prix_achat'],
                'prix_vente' => $validated['prix_vente'],
                'seuil_alerte' => $validated['seuil_alerte'] ?? 10,
                'stock_actuel' => $validated['stock_actuel'] ?? 0,
                'ordonnance_requise' => $validated['ordonnance_requise'] ?? false
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Médicament créé avec succès',
                'data' => $medicament
            ], 201);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // Modifier un médicament
    public function update(Request $request, $id)
    {
        try {
            $medicament = Medicament::findOrFail($id);
            
            $validated = $request->validate([
                'nom' => 'sometimes|string|max:255',
                'dci' => 'sometimes|string|max:255',
                'forme' => 'sometimes|string|max:255',
                'dosage' => 'sometimes|string|max:255',
                'categorie_id' => 'sometimes|exists:categories,id',
                'prix_achat' => 'sometimes|numeric|min:0',
                'prix_vente' => 'sometimes|numeric|min:0',
                'seuil_alerte' => 'nullable|integer|min:0',
                'ordonnance_requise' => 'nullable|boolean'
            ]);
            
            $medicament->update($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Médicament modifié avec succès',
                'data' => $medicament
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la modification: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // Supprimer (soft delete)
    public function destroy($id)
    {
        try {
            $medicament = Medicament::findOrFail($id);
            $medicament->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Médicament archivé avec succès'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // Restaurer un médicament
    public function restore($id)
    {
        try {
            $medicament = Medicament::withTrashed()->findOrFail($id);
            $medicament->restore();
            
            return response()->json([
                'success' => true,
                'message' => 'Médicament restauré avec succès'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la restauration: ' . $e->getMessage()
            ], 500);
        }
    }






public function export()
{
    try {
        $medicaments = Medicament::with('categorie')->get();
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // En-têtes
        $headers = ['Nom', 'DCI', 'Forme', 'Dosage', 'Catégorie', 'Prix Achat', 'Prix Vente', 'Seuil Alerte', 'Stock Initial', 'Ordonnance Requise'];
        foreach ($headers as $index => $header) {
            $sheet->setCellValue(chr(65 + $index) . '1', $header);
        }
        
        // Données
        $row = 2;
        foreach ($medicaments as $med) {
            $sheet->setCellValue('A' . $row, $med->nom);
            $sheet->setCellValue('B' . $row, $med->dci);
            $sheet->setCellValue('C' . $row, $med->forme);
            $sheet->setCellValue('D' . $row, $med->dosage);
            $sheet->setCellValue('E' . $row, $med->categorie->nom ?? '');
            $sheet->setCellValue('F' . $row, $med->prix_achat);
            $sheet->setCellValue('G' . $row, $med->prix_vente);
            $sheet->setCellValue('H' . $row, $med->seuil_alerte);
            $sheet->setCellValue('I' . $row, $med->stock_actuel);
            $sheet->setCellValue('J' . $row, $med->ordonnance_requise ? 'Oui' : 'Non');
            $row++;
        }
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'medicaments_' . date('Y-m-d') . '.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($tempFile);
        
        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
        
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Erreur lors de l\'export: ' . $e->getMessage()
        ], 500);
    }
}

public function downloadTemplate()
{
    try {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $headers = ['Nom', 'DCI', 'Forme', 'Dosage', 'Catégorie', 'Prix Achat', 'Prix Vente', 'Seuil Alerte', 'Stock Initial', 'Ordonnance Requise'];
        foreach ($headers as $index => $header) {
            $sheet->setCellValue(chr(65 + $index) . '1', $header);
        }
        
        // Exemple
        $sheet->setCellValue('A2', 'Paracétamol');
        $sheet->setCellValue('B2', 'Paracétamol');
        $sheet->setCellValue('C2', 'Comprimé');
        $sheet->setCellValue('D2', '500mg');
        $sheet->setCellValue('E2', 'Antalgique');
        $sheet->setCellValue('F2', '500');
        $sheet->setCellValue('G2', '1000');
        $sheet->setCellValue('H2', '50');
        $sheet->setCellValue('I2', '100');
        $sheet->setCellValue('J2', 'Non');
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'template_import_medicaments.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($tempFile);
        
        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
        
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Erreur lors du téléchargement du template: ' . $e->getMessage()
        ], 500);
    }
}

public function import(Request $request)
{
    $request->validate([
        'file' => 'required|file|mimes:xlsx,xls,csv'
    ]);
    
    try {
        $file = $request->file('file');
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();
        
        array_shift($rows); // Enlever l'en-tête
        
        $imported = 0;
        $errors = [];
        $duplicates = [];
        
        foreach ($rows as $index => $row) {
            try {
                $nom = trim($row[0] ?? '');
                $dci = trim($row[1] ?? '');
                
                if (empty($nom) || empty($dci)) {
                    $errors[] = "Ligne " . ($index + 2) . ": Nom et DCI obligatoires";
                    continue;
                }
                
                // Vérifier doublon
                $existing = Medicament::where('nom', $nom)->where('dci', $dci)->first();
                if ($existing) {
                    $duplicates[] = "$nom ($dci) - Ligne " . ($index + 2);
                    continue;
                }
                
                $categorie = Categorie::firstOrCreate(
                    ['nom' => trim($row[4] ?? 'Autre')],
                    ['description' => 'Importé depuis fichier']
                );
                
                Medicament::create([
                    'nom' => $nom,
                    'dci' => $dci,
                    'forme' => $row[2] ?? 'Non spécifié',
                    'dosage' => $row[3] ?? 'Non spécifié',
                    'categorie_id' => $categorie->id,
                    'prix_achat' => floatval($row[5] ?? 0),
                    'prix_vente' => floatval($row[6] ?? 0),
                    'seuil_alerte' => intval($row[7] ?? 10),
                    'stock_actuel' => intval($row[8] ?? 0),
                    'ordonnance_requise' => strtolower($row[9] ?? 'non') === 'oui'
                ]);
                
                $imported++;
                
            } catch (\Exception $e) {
                $errors[] = "Ligne " . ($index + 2) . ": " . $e->getMessage();
            }
        }
        
        return response()->json([
            'success' => true,
            'imported' => $imported,
            'duplicates' => $duplicates,
            'errors' => $errors,
            'totalLignes' => count($rows)
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de l\'import: ' . $e->getMessage()
        ], 500);
    }
}




}