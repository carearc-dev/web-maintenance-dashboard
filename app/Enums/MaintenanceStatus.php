<?php

namespace App\Enums;

enum MaintenanceStatus: string
{
    case Normal = 'normal';
    case NeedsCheck = 'needs_check';
    case ActionRequired = 'action_required';
    case NotApplicable = 'not_applicable';
}
