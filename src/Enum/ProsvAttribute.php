<?php

namespace App\Enum;

enum ProsvAttribute: string
{
    case Level = 'level';                 // Уровень образования
    case Grade = 'grade';                 // Класс / возраст детей
    case Subject = 'subject';             // Предмет
    case Series = 'series';               // Серии, УМК
    case Role = 'role';                   // Роль
    case Region = 'region';               // Регион
    case District = 'district';           // Район
}
