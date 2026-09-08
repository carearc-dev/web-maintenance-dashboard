<?php

namespace App\Enums;

enum SiteStatus: string
{
    case Normal = 'normal';
    case NeedsCheck = 'needs_check';
    case UpdatesAvailable = 'updates_available';
    case Incident = 'incident';
    case Suspended = 'suspended';
}
