<?php

namespace Fnp\Dto\Common\Traits;

use Fnp\Dto\Common\Traits\DtoToArray;
use Illuminate\Validation\Validator;

trait DtoValidate
{
    use DtoToArray;

    private $validationProcessor;
    private $validationErrors;

    /**
     * Provides validation rules
     *
     * @return array
     */
    abstract public function validationRules();

    private function validate()
    {
        $rules = $this->validationRules();
        $messages = NULL;

        if (method_exists($this, 'validationMessages')) {
            $messages = $this->validationMessages();
        }

    }

    public function valid()
    {

    }
}