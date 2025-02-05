<?php

namespace App\Enum;

use App\Entity\Property\Grade;
use App\Entity\Property\StudyLevel;
use App\Entity\Property\Subject;
use App\Entity\Property\Umk;

enum PropertyType: int
{
    case Addition = 1;
    case Competence = 2;
    case District = 3;
    case Grade = 4;
    case Publisher = 5;
    case Region = 6;
    case Role = 7;
    case StudyLevel = 8;
    case Subject = 9;
    case Tag = 10;
    case EventType = 11;
    case Umk = 12;
    case DigitalService = 13;
    case ControlForm = 14;

    public function getClassName(): string
    {
        return match($this) {
            self::Grade => Grade::class,
            self::Subject => Subject::class,
            self::StudyLevel => StudyLevel::class,
            self::Umk => Umk::class,
        };
    }
}
