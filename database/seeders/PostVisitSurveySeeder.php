<?php

namespace Database\Seeders;

use App\Models\OpenDoorRegistration;
use App\Models\PostVisitSurvey;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostVisitSurveySeeder extends Seeder
{
    private array $likedMostOptions = [
        'L\'atenció del personal va ser excel·lent, molt amables i atents.',
        'Les instal·lacions estan molt ben cuidades i són modernes.',
        'Ens van explicar tot molt clarament, sense deixar cap dubte.',
        'L\'ambient del centre és molt acollidor i familiar.',
        'Els alumnes que ens van guiar eren molt simpàtics i ens van transmetre molt bona energia.',
        'La presentació dels cicles formatius va ser molt completa.',
        'Ens va agradar molt veure els laboratoris i tallers en funcionament.',
        'La ubicació del centre és molt bona i accessible.',
        'El professorat que vam conèixer transmetia molta professionalitat.',
        'Les activitats extraescolars que ofereixen són molt variades.',
        'La metodologia d\'ensenyament ens va semblar molt innovadora.',
        'Ens van donar molta informació útil sobre beques i ajudes.',
        null,
        null,
    ];

    private array $improvementsOptions = [
        'Potser caldria més temps per visitar totes les instal·lacions.',
        'Seria útil tenir més informació escrita per endur-se a casa.',
        'L\'aparcament és una mica complicat, potser indicar alternatives.',
        'Estaria bé poder parlar més directament amb alumnes actuals.',
        'La sessió va ser una mica llarga, es podria resumir.',
        'Més exemples concrets de sortides professionals dels cicles.',
        'Caldria millorar la senyalització dins del centre.',
        'Oferir begudes o un petit refrigeri durant la visita.',
        'Seria bo tenir una app o web amb tota la informació.',
        'Més detalls sobre el procés d\'admissió i dates.',
        null,
        null,
        null,
    ];

    private array $additionalCommentsOptions = [
        'Molt contents amb la visita, segur que tornarem!',
        'Gràcies per l\'atenció, ha estat molt útil.',
        'El nostre fill/a està molt il·lusionat/da després de la visita.',
        'Recomanarem el centre als nostres coneguts.',
        'Tenim algunes preguntes més, contactarem per email.',
        'La visita ha superat les nostres expectatives.',
        'Comparant amb altres centres, aquest és el que més ens ha agradat.',
        null,
        null,
        null,
        null,
    ];

    public function run(): void
    {
        // Obtenir inscripcions amb status 'attended'
        $attendedRegistrations = OpenDoorRegistration::where('status', 'attended')
            ->whereDoesntHave('postVisitSurvey')
            ->get();

        foreach ($attendedRegistrations as $registration) {
            // Decidir si crear enquesta (90% de probabilitat)
            if (rand(1, 100) > 90) {
                continue;
            }

            $status = $this->getRandomStatus();
            $survey = $this->createSurvey($registration, $status);
        }

        // Crear algunes enquestes per a inscripcions 'no_show' que van ser re-confirmades (simulació)
        $confirmedRegistrations = OpenDoorRegistration::where('status', 'confirmed')
            ->whereDoesntHave('postVisitSurvey')
            ->take(5)
            ->get();

        foreach ($confirmedRegistrations as $registration) {
            // Simular que van assistir i crear enquesta pendent
            $this->createSurvey($registration, 'pending');
        }
    }

    private function getRandomStatus(): string
    {
        $rand = rand(1, 100);

        if ($rand <= 65) {
            return 'completed'; // 65% completades
        } elseif ($rand <= 85) {
            return 'pending'; // 20% pendents
        } else {
            return 'expired'; // 15% expirades
        }
    }

    private function createSurvey(OpenDoorRegistration $registration, string $status): PostVisitSurvey
    {
        $sessionDate = $registration->session->session_date;
        $createdAt = $sessionDate->copy()->addDay()->setTime(10, 0);

        $surveyData = [
            'open_door_registration_id' => $registration->id,
            'survey_token' => Str::uuid(),
            'status' => $status,
            'sent_at' => $createdAt,
            'expires_at' => $createdAt->copy()->addDays(14),
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ];

        if ($status === 'completed') {
            $surveyData = array_merge($surveyData, $this->generateCompletedData($createdAt));
        }

        return PostVisitSurvey::create($surveyData);
    }

    private function generateCompletedData($sentAt): array
    {
        // Generar valoracions amb tendència positiva (3-5 més probable)
        $overallRating = $this->getWeightedRating();

        // Les altres valoracions correlacionades amb l'overall
        $baseRating = $overallRating;

        $completedAt = $sentAt->copy()->addHours(rand(2, 72));

        return [
            'overall_rating' => $overallRating,
            'information_rating' => $this->getCorrelatedRating($baseRating),
            'attention_rating' => $this->getCorrelatedRating($baseRating),
            'facilities_rating' => $this->getCorrelatedRating($baseRating),
            'doubts_resolved' => rand(1, 100) <= 85, // 85% sí
            'liked_most' => $this->likedMostOptions[array_rand($this->likedMostOptions)],
            'improvements' => $this->improvementsOptions[array_rand($this->improvementsOptions)],
            'enrollment_interest' => $this->getWeightedInterest($overallRating),
            'additional_comments' => $this->additionalCommentsOptions[array_rand($this->additionalCommentsOptions)],
            'completed_at' => $completedAt,
            'updated_at' => $completedAt,
        ];
    }

    private function getWeightedRating(): int
    {
        $rand = rand(1, 100);

        if ($rand <= 5) return 1;      // 5%
        if ($rand <= 10) return 2;     // 5%
        if ($rand <= 25) return 3;     // 15%
        if ($rand <= 55) return 4;     // 30%
        return 5;                       // 45%
    }

    private function getCorrelatedRating(int $base): int
    {
        $variance = rand(-1, 1);
        $rating = $base + $variance;

        return max(1, min(5, $rating));
    }

    private function getWeightedInterest(int $overallRating): string
    {
        // L'interès correlaciona amb la valoració
        if ($overallRating >= 5) {
            $options = ['very_high', 'very_high', 'high', 'high', 'medium'];
        } elseif ($overallRating >= 4) {
            $options = ['very_high', 'high', 'high', 'medium', 'medium'];
        } elseif ($overallRating >= 3) {
            $options = ['high', 'medium', 'medium', 'low', 'low'];
        } else {
            $options = ['medium', 'low', 'low', 'none', 'none'];
        }

        return $options[array_rand($options)];
    }
}
