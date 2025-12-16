<?php

namespace App\Exports;

use App\Models\OpenDoorRegistration;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OpenDoorRegistrationsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    use Exportable;

    protected ?int $sessionId;
    protected ?string $status;
    protected ?string $dateFrom;
    protected ?string $dateTo;

    public function __construct(?int $sessionId = null, ?string $status = null, ?string $dateFrom = null, ?string $dateTo = null)
    {
        $this->sessionId = $sessionId;
        $this->status = $status;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    public function query()
    {
        $query = OpenDoorRegistration::query()->with('session');

        if ($this->sessionId) {
            $query->where('open_door_session_id', $this->sessionId);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        return $query->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return [
            __('export.id'),
            __('export.session'),
            __('export.session_date'),
            __('export.student_name'),
            __('export.student_surname'),
            __('export.student_birthdate'),
            __('export.current_school'),
            __('export.current_grade'),
            __('export.tutor_name'),
            __('export.tutor_surname'),
            __('export.tutor_email'),
            __('export.tutor_phone'),
            __('export.tutor_relationship'),
            __('export.interested_grades'),
            __('export.how_did_you_know'),
            __('export.comments'),
            __('export.status'),
            __('export.registration_date'),
            __('export.confirmed_at'),
            __('export.attended_at'),
        ];
    }

    public function map($registration): array
    {
        $statusLabels = [
            'pending' => __('open_doors.reg_status_pending'),
            'confirmed' => __('open_doors.reg_status_confirmed'),
            'attended' => __('open_doors.reg_status_attended'),
            'no_show' => __('open_doors.reg_status_no_show'),
            'cancelled' => __('open_doors.reg_status_cancelled'),
        ];

        $relationshipLabels = [
            'father' => __('open_doors.relationship_father'),
            'mother' => __('open_doors.relationship_mother'),
            'tutor' => __('open_doors.relationship_tutor'),
            'other' => __('open_doors.relationship_other'),
        ];

        $gradeLabels = [
            'eso' => __('open_doors.grade_eso'),
            'batxillerat' => __('open_doors.grade_batxillerat'),
            'cfgm' => __('open_doors.grade_cfgm'),
            'cfgs' => __('open_doors.grade_cfgs'),
        ];

        $sourceLabels = [
            'web' => __('open_doors.source_web'),
            'social_media' => __('open_doors.source_social_media'),
            'friends' => __('open_doors.source_friends'),
            'school' => __('open_doors.source_school'),
            'press' => __('open_doors.source_press'),
            'other' => __('open_doors.source_other'),
        ];

        $interestedGrades = is_array($registration->interested_grades)
            ? implode(', ', array_map(fn($g) => $gradeLabels[$g] ?? $g, $registration->interested_grades))
            : '';

        return [
            $registration->id,
            $registration->session->title ?? '',
            $registration->session->session_date?->format('d/m/Y') ?? '',
            $registration->student_name,
            $registration->student_surname,
            $registration->student_birthdate?->format('d/m/Y') ?? '',
            $registration->current_school,
            $registration->current_grade,
            $registration->tutor_name,
            $registration->tutor_surname,
            $registration->tutor_email,
            $registration->tutor_phone,
            $relationshipLabels[$registration->tutor_relationship] ?? $registration->tutor_relationship,
            $interestedGrades,
            $sourceLabels[$registration->how_did_you_know] ?? $registration->how_did_you_know,
            $registration->comments,
            $statusLabels[$registration->status] ?? $registration->status,
            $registration->created_at?->format('d/m/Y H:i') ?? '',
            $registration->confirmed_at?->format('d/m/Y H:i') ?? '',
            $registration->attended_at?->format('d/m/Y H:i') ?? '',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '0d6efd']
                ],
            ],
        ];
    }
}
