<?php
function relation_icon(string $relation): string
{
    return match ($relation) {
        'Son' => '👦',
        'Daughter' => '👧',
        'Mother' => '👩',
        'Father' => '👨',
        'Grandparent' => '👵',
        'Spouse' => '❤️',
        'Sibling' => '👫',
        default => '👤',
    };
}
