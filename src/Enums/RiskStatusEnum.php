<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Enums;

enum RiskStatusEnum: string
{
    case New = 'new';
    case Ack = 'ack';
    case Assigned = 'assigned';
    case Patched = 'patched';
    case Closed = 'closed';
    case ClosedBenign = 'closed-benign';
    case ClosedFp = 'closed-fp';
    case ClosedDuplicate = 'closed-duplicate';
    case ClosedWorkaround = 'closed-workaround';
    case ClosedRiskAcceptance = 'closed-risk-acceptance';
}
