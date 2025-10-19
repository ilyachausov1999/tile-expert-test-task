<?php

declare(strict_types = 1);

namespace App\Trait;

trait ValidationTrait
{
    /**
     * @param $errors
     * @return array
     */
    private function formatValidationErrors($errors): array
    {
        $errorMessages = [];
        foreach ($errors as $error) {
            $errorMessages[$error->getPropertyPath()] = $error->getMessage();
        }

        return $errorMessages;
    }
}
