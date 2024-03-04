<?php

namespace App\Dtos;

abstract class Dto
{
    public static function properties(): array
    {
        return array_keys(get_class_vars(get_called_class()));
    }

    public function attachValues(array $values): void
    {
        $properties = self::properties();
        foreach ($values as $propertyName => $propertyValue) {
            if (!in_array($propertyName, $properties)) {
                continue;
            }

            $this->{$propertyName} = $propertyValue;
        }
    }
}
