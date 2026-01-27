<?php

namespace App\Services;

use App\Models\EmailTemplate;
use App\Models\EmailTemplateAssignment;
use Illuminate\Support\Arr;

class EmailTemplateService
{
    /**
     * Get the active template for a given action.
     *
     * This will first look at explicit assignments, then fall back to the
     * latest active template for the action.
     */
    public function getTemplateForAction(string $action, array $data = []): ?EmailTemplate
    {
        // Prefer explicit assignment if present
        $assignment = EmailTemplateAssignment::query()
            ->where('action', $action)
            ->where('is_active', true)
            ->latest()
            ->first();

        if ($assignment && $assignment->template && $assignment->template->is_active) {
            return $assignment->template;
        }

        // Fallback: any active template directly for this action
        return EmailTemplate::query()
            ->where('action', $action)
            ->where('is_active', true)
            ->latest()
            ->first();
    }

    /**
     * Render template body with variables.
     *
     * This uses a simple {{variable}} / {{object.property}} replacement strategy.
     */
    public function renderTemplate(EmailTemplate $template, array $data): string
    {
        $body = $template->body ?? '';

        return $this->renderStringWithData($body, $data);
    }

    /**
     * Render the template subject with variables.
     *
     * Allows subjects like: "New Offer Received - {{property.address}}".
     */
    public function renderSubject(EmailTemplate $template, array $data): string
    {
        $subject = $template->subject ?? '';

        return $this->renderStringWithData($subject, $data);
    }

    /**
     * Get available variables for a given action.
     *
     * This will be enhanced later to use a dedicated EmailVariables helper.
     */
    public function getAvailableVariables(string $action): array
    {
        // Placeholder implementation: if there is an active template and it
        // defines variables metadata, return that; otherwise an empty array.
        $template = $this->getTemplateForAction($action);

        if (! $template || empty($template->variables)) {
            return [];
        }

        return $template->variables;
    }

    /**
     * Validate that the template body only uses known variables.
     *
     * @return array{unknown: string[], missing: string[]}
     */
    public function validateTemplate(string $body, array $variables): array
    {
        $placeholders = $this->extractPlaceholders($body);

        if (empty($placeholders)) {
            return ['unknown' => [], 'missing' => []];
        }

        // Flatten variables keys (supports nested arrays like ['property' => ['address' => '...']])
        $allowedKeys = $this->flattenVariableKeys($variables);

        $unknown = [];
        foreach ($placeholders as $placeholder) {
            if (! in_array($placeholder, $allowedKeys, true)) {
                $unknown[] = $placeholder;
            }
        }

        // "missing" can be used for variables defined but not used in the body
        $missing = array_values(array_diff($allowedKeys, $placeholders));

        return [
            'unknown' => array_values(array_unique($unknown)),
            'missing' => $missing,
        ];
    }

    /**
     * Render an arbitrary string with {{variable}} placeholders using the given data.
     */
    protected function renderStringWithData(string $value, array $data): string
    {
        if ($value === '') {
            return $value;
        }

        $placeholders = $this->extractPlaceholders($value);

        if (empty($placeholders)) {
            return $value;
        }

        $replacements = [];

        foreach ($placeholders as $placeholder) {
            $resolved = $this->resolvePlaceholderValue($placeholder, $data);
            $replacements['{{' . $placeholder . '}}'] = $resolved;
        }

        return strtr($value, $replacements);
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
     * Resolve a placeholder value from the provided data.
     *
     * Supports dot notation, e.g. "offer.offer_amount".
     */
    protected function resolvePlaceholderValue(string $key, array $data): string
    {
        // Use data_get to resolve nested keys, including on objects / arrays.
        $value = data_get($data, $key);

        if (is_null($value)) {
            return '';
        }

        if (is_scalar($value) || (is_object($value) && method_exists($value, '__toString'))) {
            return (string) $value;
        }

        // Fallback: JSON encode complex values
        return (string) json_encode($value);
    }

    /**
     * Flatten a nested variables definition into dot-notation keys.
     *
     * Example:
     * ['property' => ['address' => 'Property address']]
     * becomes: ['property.address']
     */
    protected function flattenVariableKeys(array $variables, string $prefix = ''): array
    {
        $keys = [];

        foreach ($variables as $key => $value) {
            $fullKey = $prefix === '' ? $key : $prefix . '.' . $key;

            if (is_array($value)) {
                $keys = array_merge($keys, $this->flattenVariableKeys($value, $fullKey));
            } else {
                $keys[] = $fullKey;
            }
        }

        return $keys;
    }
}


