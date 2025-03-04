<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class ValidationServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // CNPJ validation rule
        Validator::extend('cnpj', function ($attribute, $value, $parameters, $validator) {
            // Remove non-numeric characters
            $cnpj = preg_replace('/[^0-9]/', '', $value);
            
            // Check if the CNPJ has 14 digits
            if (strlen($cnpj) != 14) {
                return false;
            }
            
            // Check for known invalid patterns
            if (preg_match('/^(\d)\1+$/', $cnpj)) {
                return false;
            }
            
            // Validate first check digit
            $sum = 0;
            $weight = 5;
            for ($i = 0; $i < 12; $i++) {
                $sum += intval($cnpj[$i]) * $weight;
                $weight = ($weight == 2) ? 9 : --$weight;
            }
            $remainder = $sum % 11;
            $digit1 = ($remainder < 2) ? 0 : 11 - $remainder;
            
            if ($digit1 != intval($cnpj[12])) {
                return false;
            }
            
            // Validate second check digit
            $sum = 0;
            $weight = 6;
            for ($i = 0; $i < 13; $i++) {
                $sum += intval($cnpj[$i]) * $weight;
                $weight = ($weight == 2) ? 9 : --$weight;
            }
            $remainder = $sum % 11;
            $digit2 = ($remainder < 2) ? 0 : 11 - $remainder;
            
            return $digit2 == intval($cnpj[13]);
        });
        
        // Add a custom error message for CNPJ validation
        Validator::replacer('cnpj', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':attribute', $attribute, 'O campo :attribute não contém um CNPJ válido.');
        });
        
        // Phone validation rule
        Validator::extend('phone', function ($attribute, $value, $parameters, $validator) {
            // Remove non-numeric characters
            $phone = preg_replace('/[^0-9]/', '', $value);
            
            // Check if the phone number has between 10 and 11 digits (with DDD)
            $length = strlen($phone);
            
            if ($length < 10 || $length > 11) {
                return false;
            }
            
            // For 11 digits, the first digit should be 9 (mobile)
            if ($length == 11 && $phone[2] != '9') {
                return false;
            }
            
            // Check if DDD is valid (between 11 and 99)
            $ddd = intval(substr($phone, 0, 2));
            if ($ddd < 11 || $ddd > 99) {
                return false;
            }
            
            return true;
        });
        
        // Add a custom error message for phone validation
        Validator::replacer('phone', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':attribute', $attribute, 'O campo :attribute não contém um número de telefone válido.');
        });
        
        // CPF validation rule
        Validator::extend('cpf', function ($attribute, $value, $parameters, $validator) {
            // Remove non-numeric characters
            $cpf = preg_replace('/[^0-9]/', '', $value);
            
            // Check if the CPF has 11 digits
            if (strlen($cpf) != 11) {
                return false;
            }
            
            // Check for known invalid patterns
            if (preg_match('/^(\d)\1+$/', $cpf)) {
                return false;
            }
            
            // Validate first check digit
            $sum = 0;
            for ($i = 0; $i < 9; $i++) {
                $sum += intval($cpf[$i]) * (10 - $i);
            }
            $remainder = $sum % 11;
            $digit1 = ($remainder < 2) ? 0 : 11 - $remainder;
            
            if ($digit1 != intval($cpf[9])) {
                return false;
            }
            
            // Validate second check digit
            $sum = 0;
            for ($i = 0; $i < 10; $i++) {
                $sum += intval($cpf[$i]) * (11 - $i);
            }
            $remainder = $sum % 11;
            $digit2 = ($remainder < 2) ? 0 : 11 - $remainder;
            
            return $digit2 == intval($cpf[10]);
        });
        
        // Add a custom error message for CPF validation
        Validator::replacer('cpf', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':attribute', $attribute, 'O campo :attribute não contém um CPF válido.');
        });
    }
}
