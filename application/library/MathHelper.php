<?php
// File: application/libraries/MathHelper.php

class MathHelper
{
    /**
     * Check if math editor is enabled
     */
    public static function isEnabled(): bool
    {
        return defined('CONF_ENABLE_MATH_EDITOR') && CONF_ENABLE_MATH_EDITOR;
    }

    /**
     * Escape LaTeX for safe rendering
     */
    public static function escapeLatex(string $latex): string
    {
        if (empty($latex)) {
            return '';
        }
        
        // Remove potentially dangerous LaTeX commands
        $dangerous = [
            '\\write', '\\openin', '\\openout', '\\input', '\\include',
            '\\catcode', '\\begingroup', '\\endgroup', '\\def', '\\let',
            '\\futurelet', '\\csname', '\\endcsname'
        ];
        
        $safeLatex = $latex;
        foreach ($dangerous as $cmd) {
            $safeLatex = str_replace($cmd, '', $safeLatex);
        }
        
        // HTML escape
        return htmlspecialchars($safeLatex, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Create math field HTML
     */
    public static function createMathField(string $name, string $value = '', array $options = []): string
    {
        if (!self::isEnabled()) {
            // Fallback to textarea if math editor disabled
            return '<textarea name="' . $name . '" class="form-control">' 
                   . htmlspecialchars($value) . '</textarea>';
        }

        $id = $options['id'] ?? 'math-' . uniqid();
        $keyboard = $options['keyboard'] ?? (defined('CONF_MATH_KEYBOARD_DEFAULT') ? CONF_MATH_KEYBOARD_DEFAULT : 'basic');
        
        $html = '<div class="rwu-math-wrapper" data-math-field="true" data-keyboard="' . $keyboard . '">';
        $html .= '<span class="rwu-mathfield rwu-math-target"></span>';
        $html .= '<button type="button" class="rwu-math-clear">Clear</button>';
        $html .= '<input type="hidden" name="' . $name . '" value="' . htmlspecialchars($value) . '">';
        $html .= '<div class="rwu-math-raw">' . htmlspecialchars(substr($value, 0, 100)) . '</div>';
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Render LaTeX for display
     */
    public static function renderLatex(string $latex, bool $displayMode = false): string
    {
        if (empty($latex)) {
            return '';
        }
        
        $safeLatex = self::escapeLatex($latex);
        $delimiters = $displayMode ? '\\[' . $safeLatex . '\\]' : '\\(' . $safeLatex . '\\)';
        
        return '<span class="latex-render">' . $delimiters . '</span>';
    }

    /**
     * Validate LaTeX input
     */
    public static function validateLatex(string $latex): array
    {
        $errors = [];
        
        // Check length
        $maxLength = defined('CONF_MATH_MAX_LATEX_LENGTH') ? CONF_MATH_MAX_LATEX_LENGTH : 5000;
        if (strlen($latex) > $maxLength) {
            $errors[] = 'LaTeX too long (max ' . $maxLength . ' characters)';
        }
        
        // Check for unbalanced braces
        $openBraces = substr_count($latex, '{');
        $closeBraces = substr_count($latex, '}');
        if ($openBraces !== $closeBraces) {
            $errors[] = 'Unbalanced braces in LaTeX';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
}