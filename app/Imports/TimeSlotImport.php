<?php

namespace App\Imports;

use App\Models\TimeSlot;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TimeSlotImport implements ToModel, WithHeadingRow
{
    public function __construct($clinic_id)
    {
        $this->clinic_id=$clinic_id;
    }

    public function model(array $row)
    {
        return new TimeSlot([
            'clinic_id'     => $this->clinic_id,
            'date'     => $row['date'],
            'internal_start_time'     => $row['start_time'],
            'start_time'     => date('h:i A', strtotime($row['start_time'])),
            'duration'     => 60,
            'grade_1'     => $row['grade_1'],
            'grade_2'     => $row['grade_2'],
            'grade_3'     => $row['grade_3'],
            'grade_4'     => $row['grade_4'],
            'isactive'     => 1

        ]);
    }
}
