<?php

namespace App\Rules;

use Closure;
use GuzzleHttp\Client;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ReachableUrl implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param string $attribute
     * @param mixed $value
     * @param Closure(string, ?string=): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $client = new Client();

        try {
            $response = $client->head($value, [
                'timeout' => 5, // Set a timeout to avoid long waits
                'connect_timeout' => 5,
            ]);

            if ($response->getStatusCode() !== Response::HTTP_OK) {
                $fail('The url is not reachable or invalid.');
            }

        } catch (Throwable $e) {
            // Return false for any exception (e.g., unreachable URL)
            $fail('The url is not reachable or invalid.');
        }
    }

}
