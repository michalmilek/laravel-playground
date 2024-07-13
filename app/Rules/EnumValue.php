<?php

namespace App\Rules;
use Illuminate\Contracts\Validation\Rule;

class EnumValue implements Rule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    protected $enumClass;

    public function __construct($enumClass)
    {
        $this->enumClass = $enumClass;
    }

    public function passes($attribute, $value)
    {
        return in_array($value, array_column($this->enumClass::cases(), 'value'));
    }

    public function message()
    {
        return 'The :attribute field is invalid.';
    }
}
