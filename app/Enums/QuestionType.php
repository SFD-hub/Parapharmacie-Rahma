<?php

namespace App\Enums;

enum QuestionType: string
{
    case SingleChoice = 'single_choice';
    case MultipleChoice = 'multiple_choice';
    case Text = 'text';

    public function label(): string
    {
        return match ($this) {
            self::SingleChoice => 'Choix unique',
            self::MultipleChoice => 'Choix multiple',
            self::Text => 'Texte libre',
        };
    }

    public function hasOptions(): bool
    {
        return $this !== self::Text;
    }
}
