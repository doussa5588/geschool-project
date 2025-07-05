<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Department;

class SubjectSeeder extends Seeder
{
    public function run()
    {
        $teachers = Teacher::all();
        $departments = Department::all();

        $subjects = [
            // Informatique et Réseaux
            [
                'name' => 'Programmation Orientée Objet',
                'code' => 'POO101',
                'description' => 'Introduction aux concepts de la programmation orientée objet',
                'credits' => 4,
                'department_code' => 'INFO',
            ],
            [
                'name' => 'Base de Données',
                'code' => 'BD201',
                'description' => 'Conception et gestion des bases de données relationnelles',
                'credits' => 3,
                'department_code' => 'INFO',
            ],
            [
                'name' => 'Réseaux Informatiques',
                'code' => 'RES301',
                'description' => 'Fondamentaux des réseaux et protocoles de communication',
                'credits' => 4,
                'department_code' => 'INFO',
            ],
            [
                'name' => 'Sécurité Informatique',
                'code' => 'SEC401',
                'description' => 'Principes et techniques de sécurité des systèmes informatiques',
                'credits' => 3,
                'department_code' => 'INFO',
            ],

            // Génie Logiciel
            [
                'name' => 'Analyse et Conception',
                'code' => 'AC201',
                'description' => 'Méthodes d\'analyse et de conception de logiciels',
                'credits' => 4,
                'department_code' => 'GL',
            ],
            [
                'name' => 'Gestion de Projet',
                'code' => 'GP301',
                'description' => 'Méthodologies de gestion de projets informatiques',
                'credits' => 3,
                'department_code' => 'GL',
            ],
            [
                'name' => 'Tests et Qualité Logiciel',
                'code' => 'TQL401',
                'description' => 'Techniques de test et assurance qualité des logiciels',
                'credits' => 3,
                'department_code' => 'GL',
            ],

            // Intelligence Artificielle
            [
                'name' => 'Machine Learning',
                'code' => 'ML501',
                'description' => 'Algorithmes d\'apprentissage automatique',
                'credits' => 4,
                'department_code' => 'IA',
            ],
            [
                'name' => 'Réseaux de Neurones',
                'code' => 'RN601',
                'description' => 'Deep Learning et réseaux de neurones artificiels',
                'credits' => 4,
                'department_code' => 'IA',
            ],
            [
                'name' => 'Traitement du Langage Naturel',
                'code' => 'NLP701',
                'description' => 'Techniques de traitement automatique des langues',
                'credits' => 3,
                'department_code' => 'IA',
            ],

            // Télécommunications
            [
                'name' => 'Systèmes de Communication',
                'code' => 'COM201',
                'description' => 'Principes des systèmes de communication numériques',
                'credits' => 4,
                'department_code' => 'TELECOM',
            ],
            [
                'name' => 'Antennes et Propagation',
                'code' => 'ANT301',
                'description' => 'Théorie des antennes et propagation des ondes',
                'credits' => 3,
                'department_code' => 'TELECOM',
            ],

            // Systèmes d'Information
            [
                'name' => 'Systèmes d\'Information',
                'code' => 'SI201',
                'description' => 'Architecture et gestion des systèmes d\'information',
                'credits' => 3,
                'department_code' => 'SI',
            ],
            [
                'name' => 'ERP et CRM',
                'code' => 'ERP301',
                'description' => 'Systèmes de gestion intégrée et relation client',
                'credits' => 3,
                'department_code' => 'SI',
            ],

            // Cybersécurité
            [
                'name' => 'Cryptographie',
                'code' => 'CRYPT401',
                'description' => 'Algorithmes et protocoles cryptographiques',
                'credits' => 4,
                'department_code' => 'CYBER',
            ],
            [
                'name' => 'Sécurité des Réseaux',
                'code' => 'SECNET501',
                'description' => 'Sécurisation des infrastructures réseau',
                'credits' => 3,
                'department_code' => 'CYBER',
            ],

            // Matières transversales
            [
                'name' => 'Mathématiques pour l\'Informatique',
                'code' => 'MATH101',
                'description' => 'Mathématiques appliquées à l\'informatique',
                'credits' => 4,
                'department_code' => 'INFO',
            ],
            [
                'name' => 'Algorithmes et Structures de Données',
                'code' => 'ALGO201',
                'description' => 'Algorithmes fondamentaux et structures de données',
                'credits' => 4,
                'department_code' => 'INFO',
            ],
            [
                'name' => 'Systèmes d\'Exploitation',
                'code' => 'SE301',
                'description' => 'Fonctionnement et administration des systèmes d\'exploitation',
                'credits' => 3,
                'department_code' => 'INFO',
            ],
            [
                'name' => 'Développement Web',
                'code' => 'WEB201',
                'description' => 'Technologies et frameworks de développement web',
                'credits' => 3,
                'department_code' => 'GL',
            ],
        ];

        foreach ($subjects as $subjectData) {
            $department = Department::where('code', $subjectData['department_code'])->first();
            
            // Assigner un enseignant aléatoire du département
            $teacher = Teacher::where('department_id', $department->id)->inRandomOrder()->first();
            
            // Si aucun enseignant dans le département, prendre le premier enseignant disponible
            if (!$teacher) {
                $teacher = Teacher::first();
            }

            Subject::create([
                'name' => $subjectData['name'],
                'code' => $subjectData['code'],
                'description' => $subjectData['description'],
                'credits' => $subjectData['credits'],
                'teacher_id' => $teacher->id,
                'department_id' => $department->id,
                'is_active' => true,
            ]);
        }
    }
}