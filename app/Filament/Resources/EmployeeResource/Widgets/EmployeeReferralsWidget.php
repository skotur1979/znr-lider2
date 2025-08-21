<?php

namespace App\Filament\Resources\EmployeeResource\Widgets;

use Filament\Widgets\Widget;
use App\Models\Employee;
use App\Models\MedicalReferral;

class EmployeeReferralsWidget extends Widget
{
    protected static string $view = 'filament.resources.employee-resource.widgets.employee-referrals-widget';

    public ?Employee $record = null;

    public function getReferrals()
    {
        return MedicalReferral::where('employee_id', $this->record->id)->latest()->get();
    }
}

