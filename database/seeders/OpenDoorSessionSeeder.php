<?php

namespace Database\Seeders;

use App\Models\OpenDoorSession;
use Illuminate\Database\Seeder;

class OpenDoorSessionSeeder extends Seeder
{
    public function run(): void
    {
        $sessions = [
            // Sessions passades (amb assistències)
            [
                'title' => 'Jornada Portes Obertes - ESO i Batxillerat',
                'description' => 'Descobreix els nostres programes d\'ESO i Batxillerat. Visita les instal·lacions i coneix el professorat.',
                'session_date' => now()->subDays(45)->format('Y-m-d'),
                'start_time' => '10:00',
                'end_time' => '12:00',
                'capacity' => 30,
                'status' => 'completed',
                'is_active' => false,
            ],
            [
                'title' => 'Jornada Portes Obertes - Cicles Formatius',
                'description' => 'Presentació dels cicles formatius de grau mitjà i superior. Sessions informatives amb tutors.',
                'session_date' => now()->subDays(30)->format('Y-m-d'),
                'start_time' => '11:00',
                'end_time' => '13:00',
                'capacity' => 40,
                'status' => 'completed',
                'is_active' => false,
            ],
            [
                'title' => 'Sessió Informativa - CFGS Administració i Finances',
                'description' => 'Sessió específica per al cicle superior d\'Administració i Finances.',
                'session_date' => now()->subDays(15)->format('Y-m-d'),
                'start_time' => '17:00',
                'end_time' => '18:30',
                'capacity' => 25,
                'status' => 'completed',
                'is_active' => false,
            ],
            [
                'title' => 'Jornada Especial - Famílies Noves',
                'description' => 'Sessió dedicada a famílies que volen conèixer el centre per primera vegada.',
                'session_date' => now()->subDays(7)->format('Y-m-d'),
                'start_time' => '09:30',
                'end_time' => '11:30',
                'capacity' => 35,
                'status' => 'completed',
                'is_active' => false,
            ],

            // Sessions futures (actives)
            [
                'title' => 'Portes Obertes - Gener 2025',
                'description' => 'Gran jornada de portes obertes per a tots els nivells educatius.',
                'session_date' => now()->addDays(5)->format('Y-m-d'),
                'start_time' => '10:00',
                'end_time' => '13:00',
                'capacity' => 50,
                'status' => 'published',
                'is_active' => true,
            ],
            [
                'title' => 'Sessió Tarda - ESO',
                'description' => 'Sessió de tarda per a famílies interessades en ESO.',
                'session_date' => now()->addDays(10)->format('Y-m-d'),
                'start_time' => '17:00',
                'end_time' => '19:00',
                'capacity' => 25,
                'status' => 'published',
                'is_active' => true,
            ],
            [
                'title' => 'Jornada Cicles Formatius - Febrer',
                'description' => 'Descobreix tots els nostres cicles formatius de grau mitjà i superior.',
                'session_date' => now()->addDays(20)->format('Y-m-d'),
                'start_time' => '10:00',
                'end_time' => '12:00',
                'capacity' => 40,
                'status' => 'published',
                'is_active' => true,
            ],
            [
                'title' => 'Sessió Especial - Batxillerat Científic',
                'description' => 'Presentació del batxillerat científic amb visita als laboratoris.',
                'session_date' => now()->addDays(25)->format('Y-m-d'),
                'start_time' => '11:00',
                'end_time' => '13:00',
                'capacity' => 20,
                'status' => 'published',
                'is_active' => true,
            ],
            [
                'title' => 'Portes Obertes - Març 2025',
                'description' => 'Última jornada abans del període de preinscripció.',
                'session_date' => now()->addDays(45)->format('Y-m-d'),
                'start_time' => '09:00',
                'end_time' => '14:00',
                'capacity' => 60,
                'status' => 'published',
                'is_active' => true,
            ],

            // Sessió esborrany
            [
                'title' => 'Sessió Especial - Programació (Pendent)',
                'description' => 'Sessió per confirmar dates.',
                'session_date' => now()->addDays(60)->format('Y-m-d'),
                'start_time' => '10:00',
                'end_time' => '12:00',
                'capacity' => 30,
                'status' => 'draft',
                'is_active' => false,
            ],

            // Sessió cancel·lada
            [
                'title' => 'Jornada Cancel·lada - Desembre',
                'description' => 'Sessió cancel·lada per festivitats.',
                'session_date' => now()->subDays(5)->format('Y-m-d'),
                'start_time' => '10:00',
                'end_time' => '12:00',
                'capacity' => 30,
                'status' => 'cancelled',
                'is_active' => false,
            ],
        ];

        foreach ($sessions as $session) {
            OpenDoorSession::create($session);
        }
    }
}
