<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Etudiant;
use App\Models\Professeur;
use App\Models\Classe;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Module Administration - Développé par SADOU MBALLO
     * Tableau de bord et supervision générale
     */

    public function dashboard()
    {
        $stats = [
            'total_etudiants' => Etudiant::count(),
            'total_professeurs' => Professeur::count(),
            'total_classes' => Classe::count(),
            'total_utilisateurs' => User::count(),
        ];

        $activites_recentes = $this->getActivitesRecentes();

        return view('admin.dashboard', compact('stats', 'activites_recentes'));
    }

    public function parametres()
    {
        return view('admin.parametres');
    }

    public function updateParametres(Request $request)
    {
        // Logique de mise à jour des paramètres système
        $parametres = [
            'nom_ecole' => $request->nom_ecole,
            'adresse_ecole' => $request->adresse_ecole,
            'telephone_ecole' => $request->telephone_ecole,
            'email_ecole' => $request->email_ecole,
            'annee_scolaire' => $request->annee_scolaire,
        ];

        // Sauvegarder en base ou dans un fichier de config
        foreach ($parametres as $key => $value) {
            config(["app.{$key}" => $value]);
        }

        return back()->with('success', 'Paramètres mis à jour avec succès');
    }

    public function rapportGeneral()
    {
        $rapport = [
            'etudiants_par_classe' => $this->getEtudiantsParClasse(),
            'notes_moyennes' => $this->getNotesMoyennes(),
            'presence_globale' => $this->getPresenceGlobale(),
        ];

        return view('admin.rapport-general', compact('rapport'));
    }

    private function getActivitesRecentes()
    {
        // Récupérer les 10 dernières activités
        return [
            ['action' => 'Nouvel étudiant inscrit', 'utilisateur' => 'Admin', 'date' => now()],
            ['action' => 'Note ajoutée', 'utilisateur' => 'Prof. Diop', 'date' => now()->subHours(2)],
            ['action' => 'Classe créée', 'utilisateur' => 'Admin', 'date' => now()->subHours(5)],
        ];
    }

    private function getEtudiantsParClasse()
    {
        return Classe::withCount('etudiants')->get();
    }

    private function getNotesMoyennes()
    {
        // Calcul des moyennes générales
        return 14.5; // Exemple
    }

    private function getPresenceGlobale()
    {
        // Calcul du taux de présence
        return 87.3; // Exemple
    }
}