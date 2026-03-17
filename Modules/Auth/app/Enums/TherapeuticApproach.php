<?php

namespace Modules\Auth\Enums;

enum TherapeuticApproach: string
{
    case Tcc = 'tcc';
    case Psychoanalysis = 'psychoanalysis';
    case Humanistic = 'humanistic';
    case Systemic = 'systemic';
    case Gestalt = 'gestalt';
    case Other = 'other';
}
