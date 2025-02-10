<?php

namespace App\Core;

class Validator
{
    private array $errors = [];
    private array $data;

    /**
     * Validates data against specified rules
     * 
     * @param array $data Data to validate
     * @param array $rules Validation rules
     * @return bool Validation result
     */
    public function validate(array $data, array $rules): bool
    {
        $this->data = $data;
        $this->errors = [];

        foreach ($rules as $field => $fieldRules) {
            foreach ($fieldRules as $rule) {
                if (strpos($rule, ':') !== false) {
                    [$ruleName, $parameter] = explode(':', $rule);
                    $this->validateFieldWithParameter($field, $ruleName, $parameter);
                } else {
                    $this->validateField($field, $rule);
                }
            }
        }

        return empty($this->errors);
    }

    /**
     * Validates a single field against a rule
     * 
     * @param string $field Field name
     * @param string $rule Validation rule
     * @return void
     */
    private function validateField(string $field, string $rule): void
    {
        $value = $this->data[$field] ?? '';

        switch ($rule) {
            case 'required':
                if (empty($value)) {
                    $this->addError($field, 'Field is required');
                }
                break;

            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, 'Invalid email format');
                }
                break;
        }
    }

    /**
     * Validates a field with a parameterized rule
     * 
     * @param string $field Field name
     * @param string $rule Rule name
     * @param string $parameter Rule parameter
     * @return void
     */
    private function validateFieldWithParameter(string $field, string $rule, string $parameter): void
    {
        $value = $this->data[$field] ?? '';

        switch ($rule) {
            case 'min':
                if (strlen($value) < (int)$parameter) {
                    $this->addError($field, "Minimum length is $parameter characters");
                }
                break;
        }
    }

    /**
     * Adds an error message for a field
     * 
     * @param string $field Field name
     * @param string $message Error message
     * @return void
     */
    private function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    /**
     * Returns all validation errors
     * 
     * @return array Array of validation errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
