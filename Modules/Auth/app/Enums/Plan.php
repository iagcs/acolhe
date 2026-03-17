<?php

namespace Modules\Auth\Enums;

enum Plan: string
{
    case Free = 'free';
    case Solo = 'solo';
    case Clinic = 'clinic';
}
