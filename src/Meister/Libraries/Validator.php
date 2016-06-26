<?php

namespace Meister\Meister\Libraries;

use Symfony\Component\Validator\Validation;

class Validator {
    /**
     * @return \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    public static function validator() {
        return Validation::createValidatorBuilder()
            ->addMethodMapping('loadValidatorMetadata')
            ->enableAnnotationMapping()
            ->getValidator();
    }
}