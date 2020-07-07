<?php
namespace App\Service;

use App\Exception\AppException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\GroupSequence;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidatorService
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Validates a value against a constraint or a list of constraints.
     *
     * If no constraint is passed, the constraint
     * {@link \Symfony\Component\Validator\Constraints\Valid} is assumed.
     *
     * @param mixed                                              $value       The value to validate
     * @param Constraint|Constraint[]                            $constraints The constraint(s) to validate against
     * @param string|GroupSequence|(string|GroupSequence)[]|null $groups      The validation groups to validate. If none is given, "Default" is assumed
     *
     * @return ConstraintViolationListInterface A list of constraint violations
     *                                          If the list is empty, validation
     *                                          succeeded
     * @throws AppException
     */
    public function validate($value, $constraints = null, $groups = null): ConstraintViolationListInterface
    {
        $errors = $this->validator->validate($value, $constraints, $groups);
        if (count($errors) > 0) {
            /* @var ConstraintViolation $violation */
            $violation = $errors[0];
            throw new AppException($violation->getMessage(), 422);
        }

        return $errors;
    }
}
