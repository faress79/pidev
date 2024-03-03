<?php

namespace App\Entity\CustomAssert;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

#[\Attribute] class NoBadWordsValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if ($value === null || $value === '') {
            return;
        }

        // Define the list of prohibited words or phrases
        $badWords = ['badword1', 'badword2', 'badword3'];

        // Check if the value contains any prohibited words
        foreach ($badWords as $badWord) {
            if (stripos($value, $badWord) !== false) {
                $this->context->buildViolation($constraint->message)
                    ->addViolation();
                return;
            }
        }
    }
}