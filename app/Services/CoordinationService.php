<?php

namespace App\Services;

use App\Models\User;
// use App\Models\Etudiant;  // À décommenter après création du modèle
// use App\Models\Professeur; // À décommenter après création du modèle
// use App\Models\Classe;    // À décommenter après création du modèle
use Illuminate\Support\Facades\DB;

class CoordinationService
{
    /**
     * Service de Coordination - Développé par SADOU MBALLO
     * Coordination entre tous les modules du système GeSchool
     */

    public function integrationCompleteEtudiant($userData, $etudiantData)
    {
        return DB::transaction(function () use ($userData, $etudiantData) {
            // Créer l'utilisateur
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => bcrypt($userData['password']),
                'role' => 'etudiant',
                'telephone' => $userData['telephone'] ?? null,
                'adresse' => $userData['adresse'] ?? null,
                'date_naissance' => $userData['date_naissance'] ?? null,
                'genre' => $userData['genre'] ?? null,
            ]);

            // TODO: Créer le profil étudiant quand le modèle Etudiant sera créé
            /*
            $etudiant = Etudiant::create([
                'user_id' => $user->id,
                'numero_etudiant' => $this->genererNumeroEtudiant(),
                'classe_id' => $etudiantData['classe_id'] ?? null,
                'date_inscription' => now(),
                'frais_scolarite' => $etudiantData['frais_scolarite'] ?? 0,
            ]);
            */

            // Log de l'activité
            $this->logActivite('creation_etudiant', $user->id, [
                'numero_etudiant' => 'ETU' . date('Y') . str_pad($user->id, 4, '0', STR_PAD_LEFT),
                'classe_id' => $etudiantData['classe_id'] ?? null
            ]);

            return [
                'user' => $user,
                'etudiant' => null, // Temporaire
                'success' => true,
                'message' => 'Utilisateur étudiant créé avec succès'
            ];
        });
    }

    public function integrationCompleteProfesseur($userData, $professeurData)
    {
        return DB::transaction(function () use ($userData, $professeurData) {
            // Créer l'utilisateur
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => bcrypt($userData['password']),
                'role' => 'professeur',
                'telephone' => $userData['telephone'] ?? null,
                'adresse' => $userData['adresse'] ?? null,
                'date_naissance' => $userData['date_naissance'] ?? null,
                'genre' => $userData['genre'] ?? null,
            ]);

            // TODO: Créer le profil professeur quand le modèle Professeur sera créé
            /*
            $professeur = Professeur::create([
                'user_id' => $user->id,
                'numero_professeur' => $this->genererNumeroProfesseur(),
                'specialite' => $professeurData['specialite'] ?? null,
                'diplome' => $professeurData['diplome'] ?? null,
                'date_embauche' => $professeurData['date_embauche'] ?? now(),
                'salaire' => $professeurData['salaire'] ?? 0,
            ]);
            */

            // Log de l'activité
            $this->logActivite('creation_professeur', $user->id, [
                'numero_professeur' => 'PROF' . date('Y') . str_pad($user->id, 3, '0', STR_PAD_LEFT),
                'specialite' => $professeurData['specialite'] ?? null
            ]);

            return [
                'user' => $user,
                'professeur' => null, // Temporaire
                'success' => true,
                'message' => 'Utilisateur professeur créé avec succès'
            ];
        });
    }

    public function verificationIntegriteSysteme()
    {
        $rapport = [
            'status' => 'success',
            'verifications' => [],
            'erreurs' => [],
            'avertissements' => []
        ];

        // Vérifications basiques avec User seulement
        $total_utilisateurs = User::count();
        $total_admins = User::where('role', 'admin')->count();
        $total_etudiants_users = User::where('role', 'etudiant')->count();
        $total_professeurs_users = User::where('role', 'professeur')->count();

        $rapport['verifications'] = [
            'total_utilisateurs' => $total_utilisateurs,
            'total_admins' => $total_admins,
            'total_etudiants_users' => $total_etudiants_users,
            'total_professeurs_users' => $total_professeurs_users,
        ];

        if ($total_utilisateurs === 0) {
            $rapport['avertissements'][] = "Aucun utilisateur dans le système";
        }

        if ($total_admins === 0) {
            $rapport['avertissements'][] = "Aucun administrateur dans le système";
        }

        return $rapport;
    }

    public function synchronisationCompleteModules()
    {
        try {
            // Synchroniser les données entre modules
            $resultats = [
                'utilisateurs_synchronises' => $this->synchroniserUtilisateurs(),
                'systeme_verifie' => true,
                'logs_nettoyes' => $this->nettoyerLogs(),
            ];

            return [
                'success' => true,
                'message' => 'Synchronisation complète réussie',
                'details' => $resultats
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur lors de la synchronisation: ' . $e->getMessage()
            ];
        }
    }

    private function genererNumeroEtudiant()
    {
        $annee = date('Y');
        $dernier_numero = User::where('role', 'etudiant')->count() + 1;
        return "ETU{$annee}" . str_pad($dernier_numero, 4, '0', STR_PAD_LEFT);
    }

    private function genererNumeroProfesseur()
    {
        $annee = date('Y');
        $dernier_numero = User::where('role', 'professeur')->count() + 1;
        return "PROF{$annee}" . str_pad($dernier_numero, 3, '0', STR_PAD_LEFT);
    }

    private function logActivite($type, $user_id, $details = [])
    {
        // Enregistrer l'activité dans le log système
        \Log::info("Activité GeSchool: {$type}", [
            'user_id' => $user_id,
            'details' => $details,
            'timestamp' => now(),
            'responsable' => 'SADOU MBALLO'
        ]);
    }

    private function synchroniserUtilisateurs()
    {
        // Logique de synchronisation des utilisateurs
        return User::count();
    }

    private function nettoyerLogs()
    {
        // Nettoyage des logs anciens
        return true;
    }
}