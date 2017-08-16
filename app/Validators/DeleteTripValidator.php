<?php

namespace App\Validators;

use App\Models\Trip;
use App\Rules\DeleteTrip\DeleteTripRule;
use App\User;

class DeleteTripValidator
{
    protected $rules;

    public function __construct(DeleteTripRule ...$rules)
    {
        $this->rules = $rules;
    }

    public function validate(Trip $trip, User $user): bool
    {
        foreach ($this->rules as $rule) {
            $rule->validate($trip, $user);
        }

        return true;
    }
}