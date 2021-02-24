<?php

namespace App\Modules\Core\Classes;

use Illuminate\Validation\ValidationException;

abstract class Validator
{

    /**
     * @var Validator
     */
    protected $validator;

    /**
     * @param array $data
     * @return mixed
     */
    public function getValidator(array $data)
    {

        $validator = \Illuminate\Support\Facades\Validator::make($data, $this->rules(), $this->messages());

        return $validator;
    }

    /**
     * @param array $data
     * @throws ValidationException
     */
    public function validate(array $data)
    {
        $this->validator = $this->getValidator($data);

        if ($this->validator->fails()) {
            if (getenv('APP_DEBUG') === 'true') {
                \Log::debug($this->validator->errors());
            }

            throw new ValidationException($this->validator);
        }
    }

    /**
     * @return array
     */
    protected function messages(): array
    {
        return [];
    }

    /**
     * @return array
     */
    abstract protected function rules(): array;
}
