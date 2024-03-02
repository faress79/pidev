<?php

namespace App\Form;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\File;

class EventImageTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        // Transform File to a string (e.g., the file path)
        if ($value instanceof File) {
            return $value->getPathname();
        }

        return null;
    }

    public function reverseTransform($value)
    {
        // Transform a string (e.g., file path) to a File object
        if ($value instanceof File) {
            return $value;
        }

        return new File($value);
    }
}