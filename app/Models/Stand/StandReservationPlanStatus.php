<?php

namespace App\Models\Stand;

enum StandReservationPlanStatus: string
{
    case DRAFT = 'draft';
    case SUBMITTED = 'submitted';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case IMPORTED = 'imported';
    
}