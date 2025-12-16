<?php

namespace Database\Seeders;

use App\Models\OpenDoorRegistration;
use App\Models\OpenDoorSession;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OpenDoorRegistrationSeeder extends Seeder
{
    private array $noms = [
        'Marc',
        'Laia',
        'Pol',
        'Martina',
        'Jan',
        'Júlia',
        'Arnau',
        'Emma',
        'Pau',
        'Carla',
        'Biel',
        'Aina',
        'Oriol',
        'Noa',
        'Guillem',
        'Marta',
        'Roger',
        'Clàudia',
        'Eric',
        'Laura',
        'Nil',
        'Ariadna',
        'Álex',
        'Sara',
        'Hugo',
        'Lucía',
        'Daniel',
        'Paula',
        'David',
        'Maria',
        'Adrià',
        'Núria',
        'Joel',
        'Anna',
        'Ivan',
        'Elena',
        'Gerard',
        'Mireia',
        'Xavier',
        'Cristina'
    ];

    private array $cognoms = [
        'García',
        'Martínez',
        'López',
        'Sánchez',
        'González',
        'Rodríguez',
        'Fernández',
        'Pérez',
        'Gómez',
        'Ruiz',
        'Díaz',
        'Hernández',
        'Álvarez',
        'Moreno',
        'Muñoz',
        'Romero',
        'Alonso',
        'Serra',
        'Puig',
        'Ferrer',
        'Vila',
        'Soler',
        'Roca',
        'Font',
        'Mas',
        'Sala',
        'Vidal',
        'Costa',
        'Torres',
        'Navarro',
        'Reyes',
        'Cruz',
        'Ortega',
        'Delgado',
        'Castro',
        'Valls',
        'Pujol',
        'Bosch',
        'Ribas',
        'Casals'
    ];

    private array $escoles = [
        'Escola Pompeu Fabra',
        'Institut Maragall',
        'Col·legi Sant Josep',
        'Escola Pia',
        'Institut Verdaguer',
        'Escola Mare de Déu',
        'Col·legi La Salle',
        'Institut Narcís Monturiol',
        'Escola El Carme',
        'Col·legi Sagrada Família',
        'Institut Joan Brossa',
        'Escola Montserrat',
        'Col·legi Jesús Maria',
        'Institut Lluís Vives',
        'Escola Sant Pau',
        null, // alguns sense escola actual
    ];

    private array $cursos = [
        '6è Primària',
        '1r ESO',
        '2n ESO',
        '3r ESO',
        '4t ESO',
        '1r Batxillerat',
        '2n Batxillerat',
        'CFGM - 1r curs',
        'CFGM - 2n curs',
        null,
    ];

    private array $ciclesInteres = [
        ['eso'],
        ['batxillerat'],
        ['cfgm'],
        ['cfgs'],
        ['eso', 'batxillerat'],
        ['cfgm', 'cfgs'],
        ['batxillerat', 'cfgs'],
        ['eso'],
        ['cfgm'],
        ['batxillerat'],
        null,
    ];

    private array $fonts = [
        'web',
        'social_media',
        'friends',
        'school',
        'press',
        'other',
        'web',
        'social_media',
        'friends',
        'web',
        null,
    ];

    private array $comentaris = [
        'Estem molt interessats en el programa d\'anglès reforçat.',
        'El meu fill té necessitats especials, m\'agradaria saber si oferiu suport.',
        'Venim recomanats per la família Puig, que estan molt contents.',
        'Ens interessa especialment el cicle d\'informàtica.',
        'Vivim a prop del centre i ens ve molt bé per localització.',
        'La meva filla vol estudiar medicina, quin batxillerat recomaneu?',
        'Tenim dos fills, un per ESO i un per cicles.',
        'Ens agradaria conèixer les activitats extraescolars.',
        'Venim d\'un altre país i necessitem informació sobre homologació.',
        'El nostre fill és molt esportista, teniu equips escolars?',
        'M\'interessa saber sobre les beques disponibles.',
        'Volem canviar de centre perquè no estem contents amb l\'actual.',
        null,
        null,
        null,
    ];

    public function run(): void
    {
        $sessions = OpenDoorSession::all();

        foreach ($sessions as $session) {
            $numRegistrations = $this->getRegistrationsCount($session);

            for ($i = 0; $i < $numRegistrations; $i++) {
                $this->createRegistration($session);
            }

            // Actualitzar comptador
            $session->update([
                'registered_count' => $session->registrations()->whereNotIn('status', ['cancelled'])->count()
            ]);
        }
    }

    private function getRegistrationsCount(OpenDoorSession $session): int
    {
        return match ($session->status) {
            'completed' => rand(15, (int)($session->capacity * 0.95)),
            'published' => rand(5, (int)($session->capacity * 0.7)),
            'cancelled' => rand(3, 10),
            'draft' => 0,
            default => rand(5, 15),
        };
    }

    private function createRegistration(OpenDoorSession $session): void
    {
        $nomAlumne = $this->noms[array_rand($this->noms)];
        $cognomAlumne1 = $this->cognoms[array_rand($this->cognoms)];
        $cognomAlumne2 = $this->cognoms[array_rand($this->cognoms)];

        $relationship = ['father', 'mother', 'tutor', 'other'][array_rand(['father', 'mother', 'tutor', 'other'])];

        // El tutor pot tenir el mateix cognom o no
        $tutorMateixCognom = rand(0, 1);
        $nomTutor = $this->noms[array_rand($this->noms)];
        $cognomTutor = $tutorMateixCognom ? $cognomAlumne1 : $this->cognoms[array_rand($this->cognoms)];

        $status = $this->getStatus($session);
        $createdAt = $this->getCreatedAt($session);

        $registration = OpenDoorRegistration::create([
            'open_door_session_id' => $session->id,
            'student_name' => $nomAlumne,
            'student_surname' => "$cognomAlumne1 $cognomAlumne2",
            'student_birthdate' => $this->getBirthdate(),
            'current_school' => $this->escoles[array_rand($this->escoles)],
            'current_grade' => $this->cursos[array_rand($this->cursos)],
            'tutor_name' => $nomTutor,
            'tutor_surname' => $cognomTutor,
            'tutor_email' => $this->generateEmail($nomTutor, $cognomTutor),
            'tutor_phone' => $this->generatePhone(),
            'tutor_relationship' => $relationship,
            'interested_grades' => $this->ciclesInteres[array_rand($this->ciclesInteres)],
            'how_did_you_know' => $this->fonts[array_rand($this->fonts)],
            'comments' => $this->comentaris[array_rand($this->comentaris)],
            'status' => $status,
            'confirmation_token' => Str::uuid(),
            'confirmed_at' => in_array($status, ['confirmed', 'attended']) ? $createdAt->copy()->addHours(rand(1, 48)) : null,
            'attended_at' => $status === 'attended' ? $session->session_date : null,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);
    }

    private function getStatus(OpenDoorSession $session): string
    {
        if ($session->status === 'completed') {
            // Sessions passades: majoritàriament attended o no_show
            $rand = rand(1, 100);
            if ($rand <= 60) return 'attended';
            if ($rand <= 75) return 'no_show';
            if ($rand <= 85) return 'confirmed';
            if ($rand <= 95) return 'cancelled';
            return 'pending';
        }

        if ($session->status === 'cancelled') {
            return 'cancelled';
        }

        // Sessions futures
        $rand = rand(1, 100);
        if ($rand <= 45) return 'confirmed';
        if ($rand <= 80) return 'pending';
        return 'cancelled';
    }

    private function getCreatedAt(OpenDoorSession $session): \Carbon\Carbon
    {
        $sessionDate = $session->session_date;

        if ($session->status === 'completed') {
            // Inscripcions entre 30 i 3 dies abans de la sessió
            $daysBeforeSession = rand(3, 30);
            return $sessionDate->copy()->subDays($daysBeforeSession)->setTime(rand(8, 22), rand(0, 59));
        }

        // Sessions futures: inscripcions en els últims 30 dies
        return now()->subDays(rand(0, 30))->setTime(rand(8, 22), rand(0, 59));
    }

    private function getBirthdate(): string
    {
        // Alumnes entre 10 i 20 anys
        $yearsAgo = rand(10, 20);
        return now()->subYears($yearsAgo)->subDays(rand(0, 365))->format('Y-m-d');
    }

    private function generateEmail(string $nom, string $cognom): string
    {
        $nom = Str::slug($nom, '');
        $cognom = Str::slug(explode(' ', $cognom)[0], '');
        $domains = ['gmail.com', 'hotmail.com', 'yahoo.es', 'outlook.com', 'icloud.com'];
        $domain = $domains[array_rand($domains)];

        $formats = [
            "{$nom}.{$cognom}@{$domain}",
            "{$nom}{$cognom}@{$domain}",
            "{$cognom}.{$nom}@{$domain}",
            "{$nom}.{$cognom}" . rand(1, 99) . "@{$domain}",
            "{$nom[0]}{$cognom}@{$domain}",
        ];

        return $formats[array_rand($formats)];
    }

    private function generatePhone(): string
    {
        $prefixes = ['6', '7'];
        $prefix = $prefixes[array_rand($prefixes)];
        return $prefix . rand(10000000, 99999999);
    }
}
