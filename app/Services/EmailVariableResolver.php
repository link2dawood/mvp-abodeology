<?php

namespace App\Services;

class EmailVariableResolver
{
    /**
     * Resolve all variables in the given body using the provided context data.
     *
     * Example:
     *  Body: "Hello {{user.name}}, your offer is {{offer.offer_amount}}"
     *  Data: ['user' => $userModel, 'offer' => $offerModel]
     */
    public function resolve(string $body, array $data): string
    {
        $placeholders = $this->extractPlaceholders($body);

        if (empty($placeholders)) {
            return $body;
        }

        $replacements = [];

        foreach ($placeholders as $placeholder) {
            $replacements['{{' . $placeholder . '}}'] = $this->resolvePlaceholderValue($placeholder, $data);
        }

        return strtr($body, $replacements);
    }

    /**
     * Extract placeholder names from a template body.
     *
     * Example: "Hello {{user.name}}" => ["user.name"]
     */
    protected function extractPlaceholders(string $body): array
    {
        preg_match_all('/\{\{\s*([a-zA-Z0-9_.]+)\s*\}\}/', $body, $matches);

        if (empty($matches[1])) {
            return [];
        }

        return array_values(array_unique($matches[1]));
    }

    /**
     * Resolve a placeholder value from the provided data using dot-notation.
     */
    protected function resolvePlaceholderValue(string $key, array $data): string
    {
        $value = data_get($data, $key);

        if (is_null($value)) {
            return '';
        }

        if (is_scalar($value) || (is_object($value) && method_exists($value, '__toString'))) {
            return (string) $value;
        }

        return (string) json_encode($value);
    }
}


