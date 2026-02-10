<?php

class QuizattemptController extends MyAppController
{

    /**
     * Initialize Courses
     *
     * @param string $action
     */
    public function __construct(string $action)
    {
        parent::__construct($action);
    }
    /**
 * Detect if question type looks like math/numeric/ratio/fraction.
 * Backwards compatible: only true when keywords match.
 */
private function rwuIsMathType(string $questionType): bool
{
    $qt = strtolower(trim($questionType));
    if ($qt === '') return false;

    // Add keywords that your system uses for math questions
    $needles = [
        'math', 'maths', 'mathematics',
        'ratio', 'fraction', 'decimal', 'percent', 'percentage',
        'numeric', 'number', 'equation', 'algebra',
        'division', 'multiply', 'multiplication', 'subtraction', 'addition'
    ];

    foreach ($needles as $n) {
        if (strpos($qt, $n) !== false) return true;
    }
    return false;
}

/**
 * Math-safe normalization:
 * - keep operators (: / = + - * x ^)
 * - normalize unicode × ÷
 * - remove spaces around operators
 * - collapse remaining whitespace
 */
private function rwuNormalizeMath(string $text): string
{
    $text = (string)$text;
    $text = trim($text);
    if ($text === '') return '';

    // 0) normalize case + strip invisible unicode
    $text = mb_strtolower($text, 'UTF-8');
    $text = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $text);

    // 1) Convert common LaTeX-ish tokens to plain
    // (MathLive sometimes gives latex/ascii-math)
    $text = str_replace(
        ['\\left', '\\right', '\\,', '\\:', '\\;', '\\!', "\t", "\n", "\r"],
        ['',      '',       ' ',  ' ',  ' ',  ' ',  ' ',  ' ',  ' '],
        $text
    );

    // \cdot, \times, \div, \pi etc
    $text = preg_replace('/\\\\cdot\b/u', '*', $text);
      $text = preg_replace('/\\\\dot\b/u', '*', $text);
    $text = preg_replace('/\\\\times\b/u', '*', $text);
    $text = preg_replace('/\\\\div\b/u',   '/', $text);
    $text = preg_replace('/\\\\pi\b/u',    'pi', $text);

    // \sqrt{a} -> sqrt(a)
    $text = preg_replace('/\\\\sqrt\s*\{\s*([^{}]+)\s*\}/u', 'sqrt($1)', $text);

    // \frac{a}{b} or \dfrac{a}{b} -> (a)/(b)
    // (simple but very effective for typical answers)
    $text = preg_replace('/\\\\(d)?frac\s*\{\s*([^{}]+)\s*\}\s*\{\s*([^{}]+)\s*\}/u', '($2)/($3)', $text);

    // 2) Unicode operator normalization (THIS fixes dot-multiply)
    $text = str_replace(
        [
            // multiply variants
            "×","✕","✖","⨯","⊗",
            "·","⋅","∙","•","⋆","∗","✱",
            // division variants
            "÷","∕","⁄",
            // minus variants
            "−","–","—",
            // plus/equals variants
            "＋","＝",
        ],
        [
            "*","*","*","*","*",
            "*","*","*","*","*","*","*",
            "/","/","/",
            "-","-","-",
            "+","=",
        ],
        $text
    );

    // 3) Convert vulgar unicode fractions to a/b
    $vulgar = [
        "½"=>"1/2","⅓"=>"1/3","⅔"=>"2/3","¼"=>"1/4","¾"=>"3/4",
        "⅕"=>"1/5","⅖"=>"2/5","⅗"=>"3/5","⅘"=>"4/5","⅙"=>"1/6","⅚"=>"5/6",
        "⅛"=>"1/8","⅜"=>"3/8","⅝"=>"5/8","⅞"=>"7/8",
    ];
    $text = strtr($text, $vulgar);

    // 4) Normalize superscripts -> caret powers
    // x² => x^2 , 10⁻² => 10^-2
    $supMap = [
        '⁰'=>'0','¹'=>'1','²'=>'2','³'=>'3','⁴'=>'4','⁵'=>'5','⁶'=>'6','⁷'=>'7','⁸'=>'8','⁹'=>'9',
        '⁺'=>'+','⁻'=>'-','⁽'=>'(','⁾'=>')'
    ];
    // Replace any run of superscripts after a token with ^(...)
    $text = preg_replace_callback(
        '/([0-9a-z\)])([⁰¹²³⁴⁵⁶⁷⁸⁹⁺⁻⁽⁾]+)/u',
        function ($m) use ($supMap) {
            $base = $m[1];
            $sup  = $m[2];
            $out  = '';
            $chars = preg_split('//u', $sup, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($chars as $ch) {
                $out .= $supMap[$ch] ?? '';
            }
            $out = trim($out);
            if ($out === '') return $m[0];
            // if it already looks wrapped, keep it; else just append
            return $base . '^' . $out;
        },
        $text
    );

    // Clean latex power braces: x^{2} => x^2
    $text = preg_replace('/\^\s*\{\s*([^{}]+)\s*\}/u', '^$1', $text);
// Clean parenthesized integer powers: 10^(7) -> 10^7, 10^(-2) -> 10^-2, x^(3) -> x^3
$text = preg_replace('/\^\s*\(\s*([+-]?\d+)\s*\)/u', '^$1', $text);

    // 5) Collapse whitespace
    $text = preg_replace('/\s+/u', ' ', trim($text));

    // 6) Mixed number -> improper fraction (numbers only)
    // 1 1/2 => 3/2
    $text = preg_replace_callback('/\b(\d+)\s+(\d+)\s*\/\s*(\d+)\b/u', function ($m) {
        $whole = (int)$m[1];
        $num   = (int)$m[2];
        $den   = (int)$m[3];
        if ($den === 0) return $m[0];
        $top = ($whole * $den) + $num;
        return $top . '/' . $den;
    }, $text);
// Convert various 10^ formats to consistent *10^
$text = preg_replace_callback('/(\d+(?:\.\d+)?)\s*(?:×|\*)?\s*10\s*[\^\^]\s*\(?\s*([+-]?\d+)\s*\)?/u', function($matches) {
    return $matches[1] . '*10^' . $matches[2];
}, $text);

// Handle 10⁻² style unicode superscripts (already handled by supMap, but ensure it creates *10^ format)
$text = preg_replace_callback('/(10)\s*([⁰¹²³⁴⁵⁶⁷⁸⁹⁺⁻]+)/u', function($m) use ($supMap) {
    $base = $m[1];
    $sup = $m[2];
    $out = '';
    $chars = preg_split('//u', $sup, -1, PREG_SPLIT_NO_EMPTY);
    foreach ($chars as $ch) {
        $out .= $supMap[$ch] ?? '';
    }
    return $base . '^' . $out;
}, $text);
    // 7) Convert numeric multiply forms:
    // 2 x 3 => 2*3  (letter x between digits)
    $text = preg_replace('/(\d)\s*x\s*(\d)/u', '$1*$2', $text);

    // 8) Implicit multiplication (useful for typical student answers)
    // 2pi => 2*pi , 2(x+1) => 2*(x+1) , )( => )*(
    // NOTE: We avoid breaking function names like sqrt(
    $text = preg_replace('/(\d)\s*(pi)\b/u', '$1*$2', $text);
    $text = preg_replace('/(\d)\s*\(/u', '$1*(', $text);
    $text = preg_replace('/\)\s*(\d|[a-z])/u', ')*$1', $text);
    $text = preg_replace('/\)\s*\(/u', ')*(', $text);
    $text = preg_replace('/([a-z])\s*\(/u', '$1*(', $text);
    // undo for common functions: sqrt*( -> sqrt(
    $text = preg_replace('/\b(sqrt|sin|cos|tan|log|ln)\*\(/u', '$1(', $text);

    // 9) IMPORTANT: dot-period as multiply only when spaced "2 . 3"
    // but keep decimals like 3.5
    $text = preg_replace('/(\d)\s*\.\s*(\d)/u', '$1*$2', $text);

    // 10) Remove spaces around operators/separators
    $text = preg_replace('/\s*([:\/=\+\-\*\^\(\),])\s*/u', '$1', $text);

    return trim($text);
}

private function rwuLooksLikeMath(string ...$parts): bool
{
    $s = mb_strtolower(trim(implode(' ', $parts)), 'UTF-8');
    if ($s === '') return false;

    // Anything that smells like maths input
    return (bool)preg_match(
        '/(\d|[×÷⋅·∙\^=+\-*\/()]|\\\\(frac|dfrac|sqrt|cdot|times|div|pi)\b|[½⅓⅔¼¾⅕⅖⅗⅘⅙⅚⅛⅜⅝⅞]|π|sqrt|pi)/u',
        $s
    );
}
private function rwuCanonicalizePrimeProduct(string $expr): ?string
{
    $expr = $this->rwuNormalizeMath($expr);

    // Only allow pure products of integers with optional exponents: 3*3*7 or 3^2*7
    if ($expr === '') return null;
    if (preg_match('/[+\-\/=,]/u', $expr)) return null;     // not a pure product
    if (strpos($expr, '(') !== false || strpos($expr, ')') !== false) return null;

    $factors = explode('*', $expr);
    if (empty($factors)) return null;

    $map = []; // base => exponent sum
    foreach ($factors as $f) {
        $f = trim($f);
        if ($f === '') continue;

        // base^exp or base
        if (!preg_match('/^(\d+)(?:\^(-?\d+))?$/u', $f, $m)) {
            return null; // contains symbols/vars -> not prime factor product
        }
        $base = (int)$m[1];
        $exp  = isset($m[2]) ? (int)$m[2] : 1;
        if ($exp === 0) continue;

        if (!isset($map[$base])) $map[$base] = 0;
        $map[$base] += $exp;
    }

    if (empty($map)) return null;

    ksort($map, SORT_NUMERIC);

    $out = [];
    foreach ($map as $base => $exp) {
        $out[] = ($exp === 1) ? (string)$base : ($base . '^' . $exp);
    }

    return implode('*', $out);
}

/**
 * Compare math answers with math-safe normalization.
 * Supports multiple acceptable answers separated by | (pipe).
 */
private function rwuIsMathCorrect(string $userAnswer, string $correctAnswer): bool
{
    $u = $this->rwuNormalizeMath($userAnswer);
    
    $alts = array_map('trim', explode('|', (string)$correctAnswer));
    $alts = array_values(array_filter($alts, fn($x) => $x !== ''));
    
    if (empty($alts)) return ($u === '');
    
    // First try exact normalized match
    foreach ($alts as $alt) {
        $c = $this->rwuNormalizeMath($alt);
        if ($c !== '' && $u === $c) return true;
    }
    
    // Then try prime factor product match
    $uCanon = $this->rwuCanonicalizePrimeProduct($u);
    if ($uCanon !== null) {
        foreach ($alts as $alt) {
            $c = $this->rwuNormalizeMath($alt);
            $cCanon = $this->rwuCanonicalizePrimeProduct($c);
            if ($cCanon !== null && $uCanon === $cCanon) {
                return true;
            }
        }
    }
    
    // Finally try numeric tolerance comparison
    foreach ($alts as $alt) {
        if ($this->rwuIsNumericApproxEqual($userAnswer, $alt)) {
            return true;
        }
    }
    
    return false;
}
/**
 * Batch AI grading for fallback (single API call).
 * Returns: [questionId => bool] for those graded, missing ids mean "no change".
 */
private function rwuAiBatchGrade(array $items, string $apiKey, int $maxOutTokens = 200): array
{
    if (empty($items)) return [];

    // Super short, strict JSON-only prompt
    $payloadItems = [];
    foreach ($items as $it) {
        $payloadItems[] = [
            'id' => (int)$it['id'],
            'q'  => (string)$it['q'],
            'expected' => (string)$it['expected'],
            'answer'   => (string)$it['answer'],
        ];
    }

      $prompt = <<<PROMPT
You are a math and science teacher grading student answers. Follow these RULES for grading:

**NUMERICAL/MATHEMATICAL ANSWERS:**
1. **Standard Form / Scientific Notation**: Accept equivalent forms:
   - 3.48×10⁻⁷ = 3.48e-7 = 0.000000348 = 3.48*10^-7
   - Unicode variations: ×, *, x, · all mean multiply
   - 10⁸ = 10^8 = 1e8 = 100,000,000

2. **Rounding Tolerance Rules** (CRITICAL):
   - For answers given to 2 decimal places: ±0.005 tolerance
   - For answers given to 3 decimal places: ±0.0005 tolerance
   - For answers given to n decimal places: ±0.5×10⁻ⁿ tolerance
   - For standard form a×10ᵇ: tolerance is half of the last significant digit
   - Examples that SHOULD be marked correct:
     * Expected: -3.475×10⁻⁷ → Student: -3.48×10⁻⁷ ✓ (rounded to 2 decimals)
     * Expected: 5.3708×10⁻⁷ → Student: 5.37085×10⁻⁷ ✓ (within 0.00005)
     * Expected: 6.25 → Student: 6.3 ✓ (if rounding to 1 decimal)
     * Expected: 1/3 → Student: 0.333 ✓ (if 3 decimal places shown)

3. **Significant Figures**:
   - 2.5 (2 s.f.) matches 2.50 (3 s.f.)? NO - precision differs
   - 2.5×10² matches 250? YES - same value
   - Use tolerance based on stated precision

4. **Fraction/Decimal Equivalents**:
   - 1/2 = 0.5 = 0.50 (within tolerance)
   - 1/3 ≈ 0.333 (within 0.001)
5) If expected is a single number (e.g. \"6\" or \"six\"), mark true if the student's answer contains that same number (digits or word form). Do NOT accept a different number.\n
6) If expected contains multiple required parts (e.g. \"3/4\", \"x=5\", \"prime factorization 2*3*5\"), ALL required parts must be present and consistent.\n
**TEXT/NON-MATH ANSWERS:**
1. Mark true ONLY if student answer contains required meaning
2. Allow minor spelling mistakes if meaning unchanged
3. Extra words allowed if they don't change meaning
4. If ambiguous/partially correct → false

**OUTPUT FORMAT:**
Return ONLY valid JSON exactly: {"results": {"<id>": true|false, ...}}

**DATA TO GRADE:**
PROMPT . json_encode($payloadItems, JSON_UNESCAPED_UNICODE);

    $data = [
        "model" => "gpt-4o-mini",   // choose your actual cheap model
        "messages" => [
            ["role" => "system", "content" => "You are a strict examiner. Output JSON only."],
            ["role" => "user", "content" => $prompt],
        ],
        "temperature" => 0.0,
        "max_tokens" => $maxOutTokens,
    ];

    $ch = curl_init('https://api.openai.com/v1/chat/completions');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
        ],
        CURLOPT_TIMEOUT => 20,
    ]);

    $response = curl_exec($ch);
    $curlErr  = curl_error($ch);
    $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false || $curlErr || $httpCode < 200 || $httpCode >= 300) {
        error_log("AI_FAIL http=$httpCode curlErr=$curlErr resp=" . substr((string)$response, 0, 800));
        die;
        return []; // fallback silently
    }

    $resp = json_decode((string)$response, true);
    $content = $resp['choices'][0]['message']['content'] ?? '';
    $content = trim((string)$content);

    $json = json_decode($content, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($json)) return [];

    $results = $json['results'] ?? [];
    if (!is_array($results)) return [];

    // normalize keys to ints + bool
    $out = [];
    foreach ($results as $qid => $val) {
        $out[(int)$qid] = (bool)$val;
    }
    return $out;
}
private function rwuTokApprox(string $s): int
{
    // Rough estimate: 1 token ≈ 4 chars (English-ish)
    return (int)ceil(strlen($s) / 4);
}

private function rwuTruncate(string $s, int $maxChars): string
{
    $s = trim($s);
    if ($s === '') return '';
    if (strlen($s) <= $maxChars) return $s;
    return substr($s, 0, $maxChars) . '…';
}
/**
 * Smart numeric comparison with adaptive tolerance.
 * Handles rounding, significant figures, and scientific notation.
 */
private function rwuSmartNumericCompare(string $userExpr, string $correctExpr): bool
{
    // First normalize both
    $uNorm = $this->rwuNormalizeMath($userExpr);
    $cNorm = $this->rwuNormalizeMath($correctExpr);
    
    // Try exact match first
    if ($uNorm === $cNorm) return true;
    
    // Parse both as numbers
    $uParsed = $this->rwuParseNumericWithTolerance($uNorm);
    $cParsed = $this->rwuParseNumericWithTolerance($cNorm);
    
    if (!$uParsed || !$cParsed) {
        // Not numeric, can't compare
        return false;
    }
    
    $uVal = (float)$uParsed['value'];
    $cVal = (float)$cParsed['value'];
    
    // Handle very small numbers near zero
    if (abs($cVal) < 1e-15 && abs($uVal) < 1e-15) {
        return true; // Both effectively zero
    }
    
    // Calculate relative error
    $absDiff = abs($uVal - $cVal);
    $magnitude = max(abs($cVal), abs($uVal), 1e-12);
    $relError = $absDiff / $magnitude;
    
    // Determine expected precision from the expressions
    $uPrecision = $this->rwuEstimatePrecision($uNorm);
    $cPrecision = $this->rwuEstimatePrecision($cNorm);
    $expectedPrecision = min($uPrecision, $cPrecision);
    
    // Adaptive tolerance: more lenient for small numbers
    $adaptiveTol = max(1e-9, $expectedPrecision * $magnitude, $absDiff * 0.1);
    
    // Also check if difference is within last digit tolerance
    $lastDigitTol = $this->rwuLastDigitTolerance($uNorm, $cNorm);
    
    $tol = max($adaptiveTol, $lastDigitTol, 1e-12);
    
    return $absDiff <= $tol;
}

/**
 * Estimate precision from number format (decimal places, sig figs)
 */
private function rwuEstimatePrecision(string $expr): float
{
    // Standard form: a×10^b
    if (preg_match('/(\d+(?:\.\d+)?)\s*\*\s*10\s*\^\s*([+-]?\d+)/', $expr, $m)) {
        $mantissa = $m[1];
        $exp = (int)$m[2];
        
        // Count decimal places in mantissa
        if (strpos($mantissa, '.') !== false) {
            $decimals = strlen($mantissa) - strpos($mantissa, '.') - 1;
            return 0.5 * pow(10, $exp - $decimals);
        } else {
            // Integer mantissa: precision is 0.5×10^exp
            return 0.5 * pow(10, $exp);
        }
    }
    
    // Decimal number
    if (preg_match('/^[+-]?\d+(\.\d+)?$/', $expr, $m)) {
        if (strpos($expr, '.') !== false) {
            $decimals = strlen($expr) - strpos($expr, '.') - 1;
            return 0.5 * pow(10, -$decimals);
        } else {
            // Integer: precision is 0.5
            return 0.5;
        }
    }
    
    // Default precision
    return 1e-9;
}

/**
 * Calculate tolerance based on last significant digit
 */
private function rwuLastDigitTolerance(string $expr1, string $expr2): float
{
    // Extract mantissas for comparison
    $getMantissa = function($str) {
        if (preg_match('/([+-]?\d+(?:\.\d+)?)/', $str, $m)) {
            return $m[1];
        }
        return $str;
    };
    
    $m1 = $getMantissa($expr1);
    $m2 = $getMantissa($expr2);
    
    // Find the more precise (more decimal places)
    $decimals1 = $this->rwuCountDecimals($m1);
    $decimals2 = $this->rwuCountDecimals($m2);
    $decimals = min($decimals1, $decimals2);
    
    // Base tolerance on the precision
    return 0.5 * pow(10, -$decimals);
}

    /**
 * Optional AI correctness check for story-based answers.
 * Returns:
 * - true/false when AI responded correctly
 * - null when AI is unavailable (quota / API error / bad response)
 */
// private function rwuAiIsCorrectStory(
//     string $questionTitle,
//     string $studentAnswer,
//     string $correctAnswer,
//     string $apiKey
// ): ?bool {

//     $questionTitle  = trim($questionTitle);
//     $studentAnswer  = trim((string)$studentAnswer);
//     $correctAnswer  = trim((string)$correctAnswer);

//     if ($questionTitle === '') return null;
//     if ($studentAnswer === '') return false; // empty answer is wrong

//     // JSON-only prompt (NO explanation)
//     $prompt = <<<PROMPT
// You are marking a student's answer.

// Decide if the student's answer is correct.

// Return ONLY valid JSON, no extra text:
// {"is_correct": true} or {"is_correct": false}

// Question:
// {$questionTitle}

// Expected answer (reference):
// {$correctAnswer}

// Student answer:
// {$studentAnswer}
// PROMPT;

//     $data = [
//         "model" => "GPT-5 mini",
//         "messages" => [
//             ["role" => "system", "content" => "You are a strict examiner. Output JSON only."],
//             ["role" => "user", "content" => $prompt]
//         ],
//         "temperature" => 0.0,
//         "max_tokens" => 50
//     ];

//     $ch = curl_init('https://api.openai.com/v1/chat/completions');
//     curl_setopt_array($ch, [
//         CURLOPT_RETURNTRANSFER => true,
//         CURLOPT_POST => true,
//         CURLOPT_POSTFIELDS => json_encode($data),
//         CURLOPT_HTTPHEADER => [
//             'Content-Type: application/json',
//             'Authorization: Bearer ' . $apiKey
//         ],
//         CURLOPT_TIMEOUT => 15,
//     ]);

//     $response = curl_exec($ch);
//     $curlErr  = curl_error($ch);
//     $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
//     curl_close($ch);

//     // Network / curl failure => treat as unavailable
//     if ($response === false || $curlErr) {
//         return null;
//     }

//     $responseData = json_decode((string)$response, true);

//     // Non-JSON response => unavailable
//     if (json_last_error() !== JSON_ERROR_NONE || !is_array($responseData)) {
//         return null;
//     }

//     // OpenAI error (quota/rate limit/etc.) => unavailable (do NOT stop execution)
//     if (isset($responseData['error'])) {
//         // e.g. insufficient_quota, rate_limit_exceeded
//         return null;
//     }

//     // Must be 200 to trust (but still parse defensively)
//     if ($httpCode < 200 || $httpCode >= 300) {
//         return null;
//     }

//     $content = $responseData['choices'][0]['message']['content'] ?? '';
//     $content = trim((string)$content);
//     if ($content === '') return null;

//     $json = json_decode($content, true);
//     if (json_last_error() !== JSON_ERROR_NONE || !is_array($json) || !isset($json['is_correct'])) {
//         return null;
//     }

//     return (bool)$json['is_correct'];
// }

        /**
     * Normalize free-text for comparison
     * - lowercase + trim
     * - remove punctuation
     * - collapse spaces
     * - convert number words to digits (twenty one -> 21)
     * - normalize plurals (apples->apple, candies->candy)
     * - remove filler words (optional): a, an, the, of
     */
    private function rwuNormalizeText(string $text, bool $removeFillers = true): string
    {
        $text = trim(mb_strtolower($text, 'UTF-8'));

        // If it's probably LaTeX / MathLive, don't destroy it (keep only trim + collapse spaces)
        if (preg_match('/\\\\|\\{|\\}|\\^|_/', $text)) {
            $text = preg_replace('/\s+/u', ' ', $text);
            return trim($text);
        }

        // normalize hyphens to spaces so "twenty-one" works
        $text = str_replace(['-', '–', '—'], ' ', $text);

        // remove punctuation (keep letters/numbers/spaces)
        $text = preg_replace('/[^\p{L}\p{N}\s]+/u', ' ', $text);

        // collapse spaces
        $text = preg_replace('/\s+/u', ' ', $text);
        $text = trim($text);

        // convert number-words to digits (supports common English forms)
        $text = $this->rwuNumberWordsToDigits($text);

        // tokenize
        $words = $text === '' ? [] : explode(' ', $text);

        // remove filler words (optional)
        if ($removeFillers) {
            $fillers = array_flip(['a', 'an', 'the', 'of']);
            $words = array_values(array_filter($words, fn($w) => !isset($fillers[$w])));
        }

        // normalize plurals (simple rules)
        $words = array_map([$this, 'rwuSingularize'], $words);

        // final collapse
        $out = trim(preg_replace('/\s+/u', ' ', implode(' ', $words)));
        return $out;
    }

    private function rwuSingularize(string $w): string
    {
        $w = trim($w);
        if ($w === '') return $w;

        // candies -> candy
        if (preg_match('/ies$/u', $w) && mb_strlen($w, 'UTF-8') > 3) {
            return preg_replace('/ies$/u', 'y', $w);
        }

        // boxes/churches/dishes -> box/church/dish
        if (preg_match('/(ches|shes|xes|zes|ses)$/u', $w) && mb_strlen($w, 'UTF-8') > 4) {
            return preg_replace('/es$/u', '', $w);
        }

        // apples -> apple (but keep "ss" like "glass" -> "glass")
        if (preg_match('/s$/u', $w) && !preg_match('/ss$/u', $w) && mb_strlen($w, 'UTF-8') > 2) {
            return preg_replace('/s$/u', '', $w);
        }

        return $w;
    }

    /**
     * Convert number words inside a sentence to digits.
     * Handles:
     * - one..nineteen
     * - twenty..ninety + optional unit (twenty one)
     * - hundred (one hundred five)
     * - thousand (two thousand one hundred)
     */
    private function rwuNumberWordsToDigits(string $text): string
    {
        $ones = [
            'zero'=>0,'one'=>1,'two'=>2,'three'=>3,'four'=>4,'five'=>5,'six'=>6,'seven'=>7,'eight'=>8,'nine'=>9,
            'ten'=>10,'eleven'=>11,'twelve'=>12,'thirteen'=>13,'fourteen'=>14,'fifteen'=>15,'sixteen'=>16,'seventeen'=>17,'eighteen'=>18,'nineteen'=>19
        ];
        $tens = [
            'twenty'=>20,'thirty'=>30,'forty'=>40,'fifty'=>50,'sixty'=>60,'seventy'=>70,'eighty'=>80,'ninety'=>90
        ];
        $scales = ['hundred'=>100,'thousand'=>1000];

        $tokens = $text === '' ? [] : explode(' ', $text);
        $out = [];

        $current = 0;   // current chunk value
        $total = 0;     // total value for larger scales
        $inNumber = false;

        $flush = function () use (&$out, &$current, &$total, &$inNumber) {
            if ($inNumber) {
                $out[] = (string)($total + $current);
                $current = 0;
                $total = 0;
                $inNumber = false;
            }
        };

        foreach ($tokens as $tok) {
            if ($tok === '') continue;

            if ($tok === 'and') {
                // ignore "and" inside number phrases: "one hundred and five"
                if ($inNumber) continue;
                $out[] = $tok;
                continue;
            }

            if (isset($ones[$tok])) {
                $current += $ones[$tok];
                $inNumber = true;
                continue;
            }

            if (isset($tens[$tok])) {
                $current += $tens[$tok];
                $inNumber = true;
                continue;
            }

            if (isset($scales[$tok])) {
                $scale = $scales[$tok];
                if (!$inNumber) {
                    // "hundred" without a leading number -> treat as word
                    $out[] = $tok;
                    continue;
                }

                if ($scale === 100) {
                    $current = max(1, $current) * 100;
                } else {
                    // thousand
                    $total += max(1, $current) * 1000;
                    $current = 0;
                }
                $inNumber = true;
                continue;
            }

            // token is not a number word -> flush pending number
            $flush();
            $out[] = $tok;
        }

        // flush any trailing number
        $flush();

        return implode(' ', $out);
    }

    /**
     * Compare user answer to correct answer using normalization.
     * Supports multiple acceptable answers separated by | (pipe).
     */
    private function rwuIsTextCorrect(string $userAnswer, string $correctAnswer, bool $removeFillers = true): bool
    {
        $u = $this->rwuNormalizeText($userAnswer, $removeFillers);

        // allow multiple acceptable answers like: "car|automobile|vehicle"
        $alts = array_map('trim', explode('|', (string)$correctAnswer));
        $alts = array_filter($alts, fn($x) => $x !== '');

        if (empty($alts)) {
            return $u === '';
        }

        foreach ($alts as $alt) {
            $c = $this->rwuNormalizeText($alt, $removeFillers);
            if ($c !== '' && $u === $c) return true;
        }
        return false;
    }


    /**
     * Course list
     *
     * @return void
     */
    public function index()
    {

        if (isset($_GET['subtopic'])) {
            $subtopic = $_GET['subtopic'];
        } else {
            $subtopic = null; // Set default if 'subtopic' is not in the query string
        }

        $subtopicNAme = $this->getSubjectNameById($subtopic);
        $alltopics = $this->getTopicnames($subtopic);
        $params = FatApp::getQueryStringData();
        $data = [];
        if (isset($params['catg']) && $params['catg'] > 0) {
            $data['course_cate_id'] = [$params['catg']];
        }
        $searchSession = $_SESSION[AppConstant::SEARCH_SESSION] ?? [];
        // $subtopicId = $_SESSION[$subtopicId] ?? [];
        $_SESSION['subtopicId'] = $subtopic;
        $_SESSION['subtopicName'] = $subtopicNAme;



        $srchFrm = CourseSearch::getSearchForm($this->siteLangId);
        $srchFrm->fill($data + $searchSession);
        unset($_SESSION[AppConstant::SEARCH_SESSION]);
        $this->set('srchFrm', $srchFrm);
        $this->set('subtopicId', $subtopic);
        $this->set('alltopics', $alltopics);
        $this->set('filterTypes', Course::getFilterTypes());


        $posts = FatApp::getPostedData();
        $posts['pageno'] = $posts['pageno'] ?? 1;
        $posts['pagesize'] = AppConstant::PAGESIZE;
        $posts['price_sorting'] = FatApp::getPostedData('price_sorting', FatUtility::VAR_INT, AppConstant::SORT_PRICE_ASC);
        $frm = CourseSearch::getSearchForm($this->siteLangId);
        if (!$post = $frm->getFormDataFromArray($posts, ['course_cate_id'])) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }

        $post['course_status'] = Course::PUBLISHED;

        $offset = ($posts['pageno'] - 1) * $posts['pagesize'];
        $db = FatApp::getDb();
        $query = "SELECT q.video_url, q.previous_paper_pdf,t.tier,t.type,t.examBoards
                  FROM course_subtopics q 
                  INNER JOIN tbl_course_management t ON t.id = q.course_id ";


        $query .= " LIMIT $offset, {$posts['pagesize']}";
        $result = $db->query($query, [AppConstant::ACTIVE]);
        $quizzes = [];
        if ($result) {
            $quizzes = $db->fetchAll($result); // Get quizzes as an array
        }
        $countQuery = "SELECT COUNT(*) AS total FROM course_subtopics";
        $countResult = $db->query($countQuery, [AppConstant::ACTIVE]);
        $totalCount = 0;
        if ($countResult) {
            $countRow = $db->fetch($countResult);
            $totalCount = $countRow['total'] ?? 0;
        }

        $cart = new Cart($this->siteUserId, $this->siteLangId);
        $checkoutForm = $cart->getCheckoutForm([0 => Label::getLabel('LBL_NA')]);
        $checkoutForm->fill(['order_type' => Order::TYPE_COURSE]);


        $this->sets([
            'post' => $post,
            'courses' => $quizzes,
            'recordCount' => $totalCount,
            'pageCount' => ceil($totalCount / $posts['pagesize']),
            'levels' => Course::getCourseLevels(),
            'types' => Course::getTypes(),
            'checkoutForm' => $checkoutForm
        ]);


        $this->_template->render();
    }

    public function getSubtopicIdByName($subtopicName)
    {
        $db = FatApp::getDb();
        $subjectId = 0;

        $subtopicName = trim($subtopicName);  // Trim any extra spaces
        $subtopicName = addslashes($subtopicName);  // Escape special characters (optional)


        $query = "SELECT id FROM course_topics WHERE topic = '$subtopicName' AND subject_id = $subjectId LIMIT 1";

        $result = $db->query($query);

        if (!$result) {
            echo "Error executing query: " . $db->errorInfo();
            die();
        }

        $subtopic = [];
        if ($result) {
            $subtopic = $db->fetch($result);  // Assuming fetch returns a single row
        }

        return !empty($subtopic) ? $subtopic['id'] : null;
    }

    public function getSubjectNameById($subtopicId)
    {
        $db = FatApp::getDb();
        $subjectId = 0;

        $subtopicId = trim($subtopicId);  // Trim any extra spaces
        $subtopicId = addslashes($subtopicId);  // Escape special characters (optional)


        $query = "SELECT id,subject FROM course_subjects WHERE   id = $subtopicId LIMIT 1";

        $result = $db->query($query);

        if (!$result) {
            echo "Error executing query: " . $db->errorInfo();
            die();
        }

        $subtopic = [];
        if ($result) {
            $subtopic = $db->fetch($result);  // Assuming fetch returns a single row
        }

        return !empty($subtopic) ? $subtopic['subject'] : null;
    }


    public function getTopicnames($subjectid)
    {
        $db = FatApp::getDb();
        $subjectId = 0;


        $query = "SELECT id,topic FROM course_topics WHERE   subject_id = $subjectid";

        $result = $db->query($query);

        if (!$result) {
            echo "Error executing query: " . $db->errorInfo();
            die();
        }

        $subtopic = [];
        if ($result) {
            $subtopic = $db->fetchAll($result);  // Assuming fetch returns a single row
        }

        return $subtopic;
    }



/**
 * Parse numeric answers (standard form / e-notation / decimals / simple fractions).
 * Returns array: ['value'=>float, 'tol'=>float] or null if not parseable.
 */
private function rwuParseNumericWithTolerance(string $expr): ?array
{
    $expr = $this->rwuNormalizeMath($expr);
    if ($expr === '') return null;

    // 1) First try to parse as standard form with various patterns
    // Pattern 1: mantissa * 10^exp (with optional * and spacing)
    if (preg_match('/^([+-]?\d+(?:\.\d+)?)\s*\*?\s*10\s*\^\s*([+-]?\d+)$/u', $expr, $m)) {
        $mant = (float)$m[1];
        $exp = (int)$m[2];
        $val = $mant * pow(10, $exp);
        
        $decimals = $this->rwuCountDecimals($m[1]);
        $tol = 0.5 * pow(10, $exp - $decimals);
        $tol = max($tol, 1e-12);
        
        return ['value' => $val, 'tol' => $tol];
    }
    
    // Pattern 2: e-notation (3.48e-7 or 3.48E-7)
    if (preg_match('/^([+-]?\d+(?:\.\d+)?)\s*[eE]\s*([+-]?\d+)$/u', $expr, $m)) {
        $mant = (float)$m[1];
        $exp = (int)$m[2];
        $val = $mant * pow(10, $exp);
        
        $decimals = $this->rwuCountDecimals($m[1]);
        $tol = 0.5 * pow(10, $exp - $decimals);
        $tol = max($tol, 1e-12);
        
        return ['value' => $val, 'tol' => $tol];
    }
    
    // Pattern 3: ×10^ with unicode (from rwuNormalizeMath, this becomes *10^)
    if (preg_match('/^([+-]?\d+(?:\.\d+)?)\s*\*\s*10\s*\^\s*([+-]?\d+)$/u', $expr, $m)) {
        $mant = (float)$m[1];
        $exp = (int)$m[2];
        $val = $mant * pow(10, $exp);
        
        $decimals = $this->rwuCountDecimals($m[1]);
        $tol = 0.5 * pow(10, $exp - $decimals);
        $tol = max($tol, 1e-12);
        
        return ['value' => $val, 'tol' => $tol];
    }
    
    // 2) Simple fraction a/b
    if (preg_match('/^([+-]?\d+(?:\.\d+)?)\s*\/\s*([+-]?\d+(?:\.\d+)?)$/u', $expr, $m)) {
        $a = (float)$m[1];
        $b = (float)$m[2];
        if ($b == 0.0) return null;
        $val = $a / $b;
        
        $da = $this->rwuCountDecimals($m[1]);
        $db = $this->rwuCountDecimals($m[2]);
        $tol = max(0.5 * pow(10, -$da), 0.5 * pow(10, -$db), 1e-12);
        return ['value' => $val, 'tol' => $tol];
    }
    
    // 3) Plain number
    if (preg_match('/^([+-]?\d+(?:\.\d+)?)$/u', $expr, $m)) {
        $val = (float)$m[1];
        $decimals = $this->rwuCountDecimals($m[1]);
        $tol = 0.5 * pow(10, -$decimals);
        $tol = max($tol, 1e-12);
        return ['value' => $val, 'tol' => $tol];
    }
    
    return null;
}
private function rwuCountDecimals(string $numStr): int
{
    $numStr = (string)$numStr;
    $pos = strpos($numStr, '.');
    if ($pos === false) return 0;
    return max(0, strlen($numStr) - $pos - 1);
}

/**
 * Numeric approximate compare using tolerances derived from precision.
 */
private function rwuIsNumericApproxEqual(string $userExpr, string $correctExpr): bool
{
    return $this->rwuSmartNumericCompare($userExpr, $correctExpr);
}


    /**
     * Find Teachers
     */
    public function search()
    {


        $posts = FatApp::getPostedData();
        $posts['pageno'] = $posts['pageno'] ?? 1;
        $posts['pagesize'] = AppConstant::PAGESIZE;
        $posts['price_sorting'] = FatApp::getPostedData('price_sorting', FatUtility::VAR_INT, AppConstant::SORT_PRICE_ASC);
        $frm = CourseSearch::getSearchForm($this->siteLangId);
        if (!$post = $frm->getFormDataFromArray($posts, ['course_cate_id'])) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }

        $post['course_status'] = Course::PUBLISHED;

        $offset = ($posts['pageno'] - 1) * $posts['pagesize'];
        $db = FatApp::getDb();
        $query = "SELECT q.video_url, q.previous_paper_pdf,t.tier,t.type,t.examBoards
                  FROM course_subtopics q 
                  INNER JOIN tbl_course_management t ON t.id = q.course_id ";


        $query .= " LIMIT $offset, {$posts['pagesize']}";
        $result = $db->query($query, [AppConstant::ACTIVE]);
        $quizzes = [];
        if ($result) {
            $quizzes = $db->fetchAll($result); // Get quizzes as an array
        }
        $countQuery = "SELECT COUNT(*) AS total FROM course_subtopics";
        $countResult = $db->query($countQuery, [AppConstant::ACTIVE]);
        $totalCount = 0;
        if ($countResult) {
            $countRow = $db->fetch($countResult);
            $totalCount = $countRow['total'] ?? 0;
        }

        $cart = new Cart($this->siteUserId, $this->siteLangId);
        $checkoutForm = $cart->getCheckoutForm([0 => Label::getLabel('LBL_NA')]);
        $checkoutForm->fill(['order_type' => Order::TYPE_COURSE]);


        $this->sets([
            'post' => $post,
            'courses' => $quizzes,
            'recordCount' => $totalCount,
            'pageCount' => ceil($totalCount / $posts['pagesize']),
            'levels' => Course::getCourseLevels(),
            'types' => Course::getTypes(),
            'checkoutForm' => $checkoutForm
        ]);

        $this->_template->render(false, false);
    }

    public function submitAnswers()
    {
        $answersJson = FatApp::getPostedData('answers');
        $subtopicId = FatApp::getPostedData('subtopicid');
  $aiDebug = (FatApp::getQueryStringData()['ai_debug'] ?? '0') == '1';
        // Convert JSON string to PHP array
        $answers = json_decode($answersJson, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            FatUtility::dieJsonError("Invalid answer data.");
        }
FatUtility::dieJsonError('TEMP STOP: debugging');



        $api_key = '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';
$questionMarksDefault = 2;


              $results = [];
              $aiCandidates = [];       // items to possibly send to GPT
$resultsIndexByQid = [];  // map questionId => index in $results array
 // ✅ IMPORTANT: prevent undefined $results warning

        foreach ($answers as $item) {
            $questionId = $item['questionId'];
            $userAnswer = $item['answer'];

            $srch = new SearchBase('tbl_quaestion_bank');
            $srch->addCondition('id', '=', $questionId);
            $srch->addMultipleFields(['question_title', 'correct_answer', 'question_type']);
            $rs = $srch->getResultSet();
            $question = FatApp::getDb()->fetch($rs);

            if (!$question) continue;

            $questionType  = (string)$question['question_type'];
            $questionMarks = 2;
            $questionTitle = (string)$question['question_title'];
            $correctAnswer = (string)$question['correct_answer'];

            $qt = strtolower(trim($questionType));

        $questionMarks = $questionMarksDefault;
// $qt = strtolower(trim($questionType));
$qt = strtolower(trim($questionType));
$qt = str_replace(["–","—","_"], "-", $qt);        // normalize dash/underscore
$qt = preg_replace('/\s+/', '-', $qt);            // spaces -> hyphen


if ($qt === 'story-based' || strpos($qt, 'story') !== false) {

    // 1) First try normalization (cheap + no API)
    $ua = is_array($userAnswer) ? implode(' ', $userAnswer) : (string)$userAnswer;

  $normCorrect = false;
if (trim((string)$correctAnswer) !== '') {

    // ✅ NEW: Detect math even for Story questions using content (ua / correct / title)
    $isMath = $this->rwuIsMathType($questionType)
        || $this->rwuLooksLikeMath($ua, (string)$correctAnswer, (string)$questionTitle);

    if ($isMath) {
        $normCorrect = $this->rwuIsMathCorrect($ua, (string)$correctAnswer);
    } else {
        $normCorrect = $this->rwuIsTextCorrect($ua, (string)$correctAnswer, true);
    }
}


    $isCorrect = $normCorrect;
if (!$isCorrect) {
    $aiCandidates[] = [
        'id' => (int)$questionId,
        'q'  => $this->rwuTruncate((string)$questionTitle, 180),
        'expected' => $this->rwuTruncate((string)$correctAnswer, 180),
        'answer'   => $this->rwuTruncate((string)$ua, 220),
    ];
}

  

    $obtainedMarks = $isCorrect ? $questionMarks : 0;

    $results[] = [
        'questionId'     => $questionId,
        'userAnswer'     => $ua,
        'correctAnswer'  => (string)$correctAnswer, // keep for records
        'isCorrect'      => $isCorrect,
        'marksObtained'  => $obtainedMarks,
        'explanation'    => '', // ✅ you said: no explanation
    ];
$resultsIndexByQid[$questionId] = count($results) - 1;


                // ... keep your existing ChatGPT grading block EXACTLY as-is ...

            }
            // -------------------------
            // 2) Text / Short answer => normalization-based compare
            // -------------------------
      else if (
    strpos($qt, 'short') !== false ||
    strpos($qt, 'text')  !== false ||
    strpos($qt, 'blank') !== false ||
    strpos($qt, 'fill')  !== false
) {
    $ua = is_array($userAnswer) ? implode(' ', $userAnswer) : (string)$userAnswer;

    $isMath = $this->rwuIsMathType($questionType)
        || $this->rwuLooksLikeMath($ua, (string)$correctAnswer, (string)$questionTitle);

    if ($isMath) {
        $isCorrect = $this->rwuIsMathCorrect($ua, (string)$correctAnswer);
    } else {
        $isCorrect = $this->rwuIsTextCorrect($ua, (string)$correctAnswer, true);
    }

    $obtainedMarks = $isCorrect ? $questionMarks : 0;

    $results[] = [
        'questionId'     => $questionId,
        'userAnswer'     => $ua,
        'correctAnswer'  => $correctAnswer,
        'isCorrect'      => $isCorrect,
        'marksObtained'  => $obtainedMarks,
        'explanation'    => '',
    ];
    $resultsIndexByQid[$questionId] = count($results) - 1;

    // ✅ NEW: send to AI when normalizer fails
    if (!$isCorrect) {
        $aiCandidates[] = [
            'id' => (int)$questionId,
            'q'  => $this->rwuTruncate((string)$questionTitle, 180),
            'expected' => $this->rwuTruncate((string)$correctAnswer, 180),
            'answer'   => $this->rwuTruncate((string)$ua, 220),
        ];
    }
}

         // -------------------------
            // 3) MCQ => your existing A/B/C/D logic
            // -------------------------
            else {
                $correctArray = array_map(function($x){
                    return strtoupper(trim((string)$x));
                }, explode(',', $correctAnswer));

                $correctArray = array_values(array_filter($correctArray, fn($x) => $x !== ''));

                $userArray = is_array($userAnswer) ? $userAnswer : [$userAnswer];
                $userArray = array_map(function($x){
                    return strtoupper(trim((string)$x));
                }, $userArray);

                $userArray = array_values(array_filter($userArray, fn($x) => $x !== ''));

                sort($correctArray);
                sort($userArray);

                $isCorrect = ($correctArray === $userArray);
                $marksObtained = $isCorrect ? $questionMarks : 0;

                $results[] = [
                    'questionId'     => $questionId,
                    'userAnswer'     => $userArray,
                    'correctAnswer'  => $correctArray,
                    'isCorrect'      => $isCorrect,
                    'marksObtained'  => $marksObtained,
                    'explanation'    => '',
                ];
            }
        }
        // ✅ Collect debug info to show in Network tab response
$aiDebugInfo = [
    'enabled' => $aiDebug,
    'candidatesCount' => count($aiCandidates),
    'selectedCount' => 0,
    'selectedIds' => [],
    'skippedIds' => [],
    'budget' => [
        'total' => 0,
        'in'    => 0,
        'out'   => 0,
        'used'  => 0,
    ],
    'payloadPreview' => [], // truncated items for inspection
];

// ---- GPT fallback budget control ----
// $BUDGET_TOTAL = 800;
// $MAX_OUT = 200;                 // output cap
// $BUDGET_IN = $BUDGET_TOTAL - $MAX_OUT;

// $selected = [];
// $used = 0;

// // Always keep prompt small: only send what fits budget
// foreach ($aiCandidates as $it) {
//     $piece = json_encode($it, JSON_UNESCAPED_UNICODE);
//     $cost = $this->rwuTokApprox($piece);

//     if (($used + $cost) > $BUDGET_IN) {
//         continue; // skip, keep normalizer result
//     }
//     $selected[] = $it;
//     $used += $cost;
// }
// ---- GPT fallback budget control ----
$BUDGET_TOTAL = 800;
$MAX_OUT = 200;
$BUDGET_IN = $BUDGET_TOTAL - $MAX_OUT;

$selected = [];
$used = 0;

$aiDebugInfo['budget'] = [
    'total' => $BUDGET_TOTAL,
    'in'    => $BUDGET_IN,
    'out'   => $MAX_OUT,
    'used'  => 0,
];

foreach ($aiCandidates as $it) {
    $piece = json_encode($it, JSON_UNESCAPED_UNICODE);
    $cost  = $this->rwuTokApprox($piece);

    if (($used + $cost) > $BUDGET_IN) {
        if ($aiDebug) {
            $aiDebugInfo['skippedIds'][] = (int)$it['id'];
        }
        continue;
    }

    $selected[] = $it;
    $used += $cost;

    if ($aiDebug) {
        $aiDebugInfo['selectedIds'][] = (int)$it['id'];

        // store a small preview so you can see WHAT was sent
        $aiDebugInfo['payloadPreview'][] = [
            'id' => (int)$it['id'],
            'q' => $it['q'],
            'expected' => $it['expected'],
            'answer' => $it['answer'],
        ];
    }
}

$aiDebugInfo['selectedCount'] = count($selected);
$aiDebugInfo['budget']['used'] = $used;

if (!empty($selected)) {
    $aiMarks = $this->rwuAiBatchGrade($selected, $api_key, $MAX_OUT);

    // apply AI results to $results
    foreach ($aiMarks as $qid => $aiCorrect) {
        if (!isset($resultsIndexByQid[$qid])) continue;

        $idx = $resultsIndexByQid[$qid];
        $results[$idx]['isCorrect'] = (bool)$aiCorrect;
        $results[$idx]['marksObtained'] = $aiCorrect ? $questionMarksDefault : 0;
        // no explanation
    }
    
}



        $totalCorrect = 0;
        $totalMarks = 0;
        $marksObtained = 0;

        // Store results after loop


        if (is_array($results) && count($results) > 0) {
            foreach ($results as $res) {
                $totalMarks += isset($res['marksObtained']) ? $res['marksObtained'] : 0;
                if ($res['isCorrect']) {
                    $totalCorrect++;
                }
            }
        }
        $totalQuestions = count($results);

        $passingPercentage = 80;
      $tm = 0;
foreach ($results as $res) {
    $tm += $questionMarksDefault; // OR store max marks per question if you later vary
}
$percentage = ($tm > 0) ? (($totalMarks / $tm) * 100) : 0;

        $resultStatus = $percentage >= $passingPercentage ? 'pass' : 'fail';
        $db = FatApp::getDb();

        $userid = '';
        if (isset($_SESSION['quiz_user']['id']) && !empty($_SESSION['quiz_user']['id'])) {
            $userid = $_SESSION['quiz_user']['id'];
        }

        $quizAttemptData = [
            'user_id' => $userid,
            'subtopic_id' => $subtopicId,
            'total_questions' => $totalQuestions,
            'total_correct' => $totalCorrect,
            'total_marks' => $totalQuestions * $questionMarks, // if uniform
            'marks_obtained' => $totalMarks,
            'result' => $resultStatus
        ];

        if (!$db->insertFromArray('tbl_quiz_attempts', $quizAttemptData)) {
            FatUtility::dieJsonError('Failed to insert quiz attempt');
        }

        $attemptId = $db->getInsertId(); // You’ll need this to link answers

        foreach ($results as $res) {
            $answerData = [
                'attempt_id' => $attemptId,
                'question_id' => $res['questionId'],
                'user_answer' => is_array($res['userAnswer']) ? implode(',', $res['userAnswer']) : $res['userAnswer'],
                'correct_answer' => is_array($res['correctAnswer']) ? implode(',', $res['correctAnswer']) : (string) $res['correctAnswer'],
                'marks_obtained' => $res['marksObtained'],
                'is_correct' => $res['isCorrect'] ? 1 : 0,
            ];

            if (!$db->insertFromArray('tbl_quiz_attempt_answers', $answerData)) {
                FatUtility::dieJsonError('Failed to insert answer for question ID: ' . $res['questionId']);
            }
        }


        FatUtility::dieJsonSuccess([
            'message' => 'Quiz submitted successfully!',
            'success' => 123, // if you have results to show
            'attemptid' => $attemptId,
            'status' => $resultStatus,
            'marksObtained' => $totalMarks,
            'totalMarks' => $totalQuestions * $questionMarks,
        ]);
    }
    public function getQuestions()
{
    header('Content-Type: application/json; charset=utf-8');

    $debugStep = 'start';

    register_shutdown_function(function () use (&$debugStep) {
        $err = error_get_last();
        if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
            echo json_encode([
                'success' => false,
                'message' => 'FATAL: ' . $err['message'],
                'file'    => $err['file'] ?? '',
                'line'    => $err['line'] ?? 0,
                'debug'   => ['step' => $debugStep],
            ]);
        }
    });

    set_error_handler(function ($severity, $message, $file, $line) {
        if (!(error_reporting() & $severity)) return false;
        throw new ErrorException($message, 0, $severity, $file, $line);
    });

    try {

        /* ---------- image detection helper (keep your existing logic) ---------- */
        $isImagePath = function ($v): bool {
            $v = strtolower(trim((string)$v));
            if ($v === '') return false;

            if (preg_match('/\.(png|jpe?g|gif|webp|svg)$/i', $v)) return true;
            if (strpos($v, '/uploads/') !== false) return true;
            if (strpos($v, '/public/') !== false) return true;
            if (strpos($v, 'uploads/') === 0) return true;

            return false;
        };

        $normalizeOption = function ($raw) use ($isImagePath) {
            $raw = is_string($raw) ? trim($raw) : $raw;
            if ($raw === null || $raw === '') return null;

            // If stored JSON like {"type":"image","value":"/path/to.png"}
            if (is_string($raw) && strlen($raw) > 1 && ($raw[0] === '{' || $raw[0] === '[')) {
                $decoded = json_decode($raw, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $type  = strtolower(trim((string)($decoded['type'] ?? 'text')));
                    $value = (string)($decoded['value'] ?? ($decoded['url'] ?? ''));
                    $value = trim($value);
                    if ($value !== '') {
                        return ['type' => ($type === 'image' ? 'image' : 'text'), 'value' => $value];
                    }
                }
            }

            $type = $isImagePath($raw) ? 'image' : 'text';
            return ['type' => $type, 'value' => (string)$raw];
        };

        $debugStep = 'read_posts';
        $posts = FatApp::getPostedData();

        $debugStep = 'parse_inputs';
        $quizMgmtId = FatUtility::int($posts['subtopicid'] ?? 0);

        $limit = FatUtility::int($posts['limit'] ?? 10);
        if ($limit < 1) $limit = 10;
        if ($limit > 50) $limit = 50;

        if ($quizMgmtId < 1) {
            throw new Exception("Invalid subtopicid/quizMgmtId. Received: {$quizMgmtId}");
        }

        $debugStep = 'get_db';
        $db = FatApp::getDb();
        if (!$db) {
            throw new Exception("FatApp::getDb() returned null/false");
        }

        /* ---------------------------
         * 1) Fetch questions
         * IMPORTANT: include new image option columns
         * --------------------------- */
        $debugStep = 'questions_query';
        $questionsSql = "
            SELECT 
                id,
                question_title,
                question_type,
                answer_a, answer_b, answer_c, answer_d,
                answer_a_image, answer_b_image, answer_c_image, answer_d_image,
                correct_answer,
                hint,
                explanation,
                image
            FROM tbl_quaestion_bank
            WHERE subtopic_id = {$quizMgmtId}
            ORDER BY RAND()
            LIMIT {$limit}
        ";

        $rs = $db->query($questionsSql);
        if (!$rs) {
            throw new Exception("Questions query failed. SQL: {$questionsSql}");
        }

        $debugStep = 'questions_fetchAll';
        $rows = $db->fetchAll($rs);
        if (!is_array($rows) || empty($rows)) {
            throw new Exception("No questions found for quizMgmtId={$quizMgmtId}");
        }

        /* ---------------------------
         * 2) Format questions
         * - If answer_*_image present => image option
         * - else fallback to answer_* text
         * --------------------------- */
        $debugStep = 'format_questions';
        $formattedQuestions = [];

        foreach ($rows as $q) {
            // Build A/B/C/D using image fields first
            $rawOptions = [
                ['img' => $q['answer_a_image'] ?? '', 'txt' => $q['answer_a'] ?? ''],
                ['img' => $q['answer_b_image'] ?? '', 'txt' => $q['answer_b'] ?? ''],
                ['img' => $q['answer_c_image'] ?? '', 'txt' => $q['answer_c'] ?? ''],
                ['img' => $q['answer_d_image'] ?? '', 'txt' => $q['answer_d'] ?? ''],
            ];

            $options = [];
            foreach ($rawOptions as $pair) {
                $img = trim((string)($pair['img'] ?? ''));
                $txt = trim((string)($pair['txt'] ?? ''));

                // prefer image column if present
                if ($img !== '') {
                    $opt = $normalizeOption($img);
                    // force type image (because this column is specifically for images)
                    if ($opt) {
                        $opt['type'] = 'image';
                        $options[] = $opt;
                    }
                    continue;
                }

                // fallback to text
                if ($txt !== '') {
                    $opt = $normalizeOption($txt);
                    if ($opt) {
                        $opt['type'] = 'text';
                        $options[] = $opt;
                    }
                }
            }

            $formattedQuestions[] = [
                "id"          => (int)($q['id'] ?? 0),
                "text"        => (string)($q['question_title'] ?? ''),
                "type"        => (string)($q['question_type'] ?? ''),
                "options"     => $options,
                "answer"      => array_values(array_filter(array_map(
                    function ($s) { return strtoupper(trim((string)$s)); },
                    explode(",", (string)($q['correct_answer'] ?? ''))
                ))),
                "hint"        => (string)($q['hint'] ?? ''),
                "explanation" => (string)($q['explanation'] ?? ''),
                "image"       => (string)($q['image'] ?? ''),
            ];
        }

        /* ---------------------------
         * 3) Resolve subject properly (keep your existing math logic)
         * quiz_management.id -> quiz_setup_id -> quiz_setup.subject_id -> course_subjects.subject
         * --------------------------- */
        $setupId = 0;
        $subjectId = 0;
        $subjectName = '';

        $debugStep = 'get_setup_id';
        $rs = $db->query("SELECT quiz_setup_id FROM tbl_quiz_management WHERE id = {$quizMgmtId} LIMIT 1");
        if (!$rs) {
            throw new Exception("Failed to query tbl_quiz_management for id={$quizMgmtId}");
        }
        $row = $db->fetch($rs);
        $setupId = (int)($row['quiz_setup_id'] ?? 0);

        if ($setupId > 0) {
            $debugStep = 'get_subject_id';
            $rs = $db->query("SELECT subject_id FROM tbl_quiz_setup WHERE id = {$setupId} LIMIT 1");
            if (!$rs) {
                throw new Exception("Failed to query tbl_quiz_setup for id={$setupId}");
            }
            $row = $db->fetch($rs);
            $subjectId = (int)($row['subject_id'] ?? 0);
        }

        if ($subjectId > 0) {
            $debugStep = 'get_subject_name';
            $rs = $db->query("SELECT subject FROM course_subjects WHERE id = {$subjectId} LIMIT 1");
            if (!$rs) {
                throw new Exception("Failed to query course_subjects for id={$subjectId}");
            }
            $row = $db->fetch($rs);
            $subjectName = (string)($row['subject'] ?? '');
        }

        $debugStep = 'math_check';
        $isMathSubject = (preg_match('/\bmaths?\b/i', $subjectName) === 1);

        restore_error_handler();

        $debugStep = 'success_return';
        FatUtility::dieJsonSuccess([
            'success' => true,
            'data'    => $formattedQuestions,
            'meta'    => [
                'subjectName'   => $subjectName,
                'isMathSubject' => $isMathSubject,
                'quizMgmtId'    => $quizMgmtId,
                'setupId'       => $setupId,
                'subjectId'     => $subjectId,
                'debug' => [
                    'step'  => $debugStep,
                    'limit' => $limit,
                    'count' => count($formattedQuestions),
                ],
            ],
        ]);

    } catch (Throwable $e) {
        restore_error_handler();

        echo json_encode([
            'success' => false,
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
            'debug'   => ['step' => $debugStep],
        ]);
        die;
    }
}

// public function getQuestions()
// {
//     header('Content-Type: application/json; charset=utf-8');

//     $debugStep = 'start';

//     register_shutdown_function(function () use (&$debugStep) {
//         $err = error_get_last();
//         if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
//             echo json_encode([
//                 'success' => false,
//                 'message' => 'FATAL: ' . $err['message'],
//                 'file'    => $err['file'] ?? '',
//                 'line'    => $err['line'] ?? 0,
//                 'debug'   => ['step' => $debugStep],
//             ]);
//         }
//     });

//     set_error_handler(function ($severity, $message, $file, $line) {
//         if (!(error_reporting() & $severity)) return false;
//         throw new ErrorException($message, 0, $severity, $file, $line);
//     });

//     try {
//         $debugStep = 'read_posts';
//         $posts = FatApp::getPostedData();

//         // IMPORTANT:
//         // your frontend sends subtopicid=57, but this is actually tbl_quiz_management.id
//         $debugStep = 'parse_inputs';
//         $quizMgmtId = FatUtility::int($posts['subtopicid'] ?? 0);

//         $limit = FatUtility::int($posts['limit'] ?? 10);
//         if ($limit < 1) $limit = 10;
//         if ($limit > 50) $limit = 50;

//         if ($quizMgmtId < 1) {
//             throw new Exception("Invalid subtopicid/quizMgmtId. Received: {$quizMgmtId}");
//         }

//         $debugStep = 'get_db';
//         $db = FatApp::getDb();
//         if (!$db) {
//             throw new Exception("FatApp::getDb() returned null/false");
//         }

//         // ---------------------------
//         // 1) Fetch questions
//         // tbl_quaestion_bank.subtopic_id matches tbl_quiz_management.id in your flow
//         // ---------------------------
//         $debugStep = 'questions_query';
//         $questionsSql = "
//             SELECT 
//                 id,
//                 question_title,
//                 question_type,
//                 answer_a,
//                 answer_b,
//                 answer_c,
//                 answer_d,
//                 correct_answer,
//                 hint,
//                 explanation,
//                 image
//             FROM tbl_quaestion_bank
//             WHERE subtopic_id = {$quizMgmtId}
//             ORDER BY RAND()
//             LIMIT {$limit}
//         ";

//         $rs = $db->query($questionsSql);
//         if (!$rs) {
//             throw new Exception("Questions query failed. SQL: {$questionsSql}");
//         }

//         $debugStep = 'questions_fetchAll';
//         $rows = $db->fetchAll($rs);
//         if (!is_array($rows) || empty($rows)) {
//             throw new Exception("No questions found for quizMgmtId={$quizMgmtId}");
//         }

//         $debugStep = 'format_questions';
//         $formattedQuestions = [];
//         foreach ($rows as $q) {
//             $formattedQuestions[] = [
//                 "id"          => $q['id'] ?? 0,
//                 "text"        => $q['question_title'] ?? '',
//                 "type"        => $q['question_type'] ?? '',
//                 "options"     => array_values(array_filter([
//                     $q['answer_a'] ?? '',
//                     $q['answer_b'] ?? '',
//                     $q['answer_c'] ?? '',
//                     $q['answer_d'] ?? '',
//                 ])),
//                 "answer"      => array_values(array_filter(array_map(
//                     'trim',
//                     explode(",", (string)($q['correct_answer'] ?? ''))
//                 ))),
//                 "hint"        => $q['hint'] ?? '',
//                 "explanation" => $q['explanation'] ?? '',
//                 "image"       => $q['image'] ?? '',
//             ];
//         }

//         // ---------------------------
//         // 2) Resolve subject properly:
//         // quiz_management.id (57)
//         // -> quiz_setup_id (33)
//         // -> quiz_setup.subject_id (8)
//         // -> course_subjects.subject ("Math")
//         // ---------------------------
//         $setupId = 0;
//         $subjectId = 0;
//         $subjectName = '';

//         $debugStep = 'get_setup_id';
//         $rs = $db->query("SELECT quiz_setup_id FROM tbl_quiz_management WHERE id = {$quizMgmtId} LIMIT 1");
//         if (!$rs) {
//             throw new Exception("Failed to query tbl_quiz_management for id={$quizMgmtId}");
//         }
//         $row = $db->fetch($rs);
//         $setupId = (int)($row['quiz_setup_id'] ?? 0);

//         if ($setupId > 0) {
//             $debugStep = 'get_subject_id';
//             $rs = $db->query("SELECT subject_id FROM tbl_quiz_setup WHERE id = {$setupId} LIMIT 1");
//             if (!$rs) {
//                 throw new Exception("Failed to query tbl_quiz_setup for id={$setupId}");
//             }
//             $row = $db->fetch($rs);
//             $subjectId = (int)($row['subject_id'] ?? 0);
//         }

//         if ($subjectId > 0) {
//             $debugStep = 'get_subject_name';
//             $rs = $db->query("SELECT subject FROM course_subjects WHERE id = {$subjectId} LIMIT 1");
//             if (!$rs) {
//                 throw new Exception("Failed to query course_subjects for id={$subjectId}");
//             }
//             $row = $db->fetch($rs);
//             $subjectName = (string)($row['subject'] ?? '');
//         }

//         $debugStep = 'math_check';
//         $isMathSubject = (preg_match('/\bmaths?\b/i', $subjectName) === 1);

//         restore_error_handler();

//         $debugStep = 'success_return';
//         FatUtility::dieJsonSuccess([
//             'success' => true,
//             'data'    => $formattedQuestions,
//             'meta'    => [
//                 'subjectName'   => $subjectName,
//                 'isMathSubject' => $isMathSubject,
//                 'quizMgmtId'    => $quizMgmtId,
//                 'setupId'       => $setupId,
//                 'subjectId'     => $subjectId,
//                 'debug' => [
//                     'step'  => $debugStep,
//                     'limit' => $limit,
//                     'count' => count($formattedQuestions),
//                 ],
//             ],
//         ]);

//     } catch (Throwable $e) {
//         restore_error_handler();

//         echo json_encode([
//             'success' => false,
//             'message' => $e->getMessage(),
//             'file'    => $e->getFile(),
//             'line'    => $e->getLine(),
//             'debug'   => ['step' => $debugStep],
//         ]);
//         die;
//     }
// }



    public function getQuizizzList()
    {
        // Fetch posted data
        $posts = FatApp::getPostedData();
        $posts['pageno'] = $posts['pageno'] ?? 1; // Default to page 1 if not provided
        $posts['pagesize'] = AppConstant::PAGESIZE; // Default page size from constant
        $posts['price_sorting'] = FatApp::getPostedData('price_sorting', FatUtility::VAR_INT, AppConstant::SORT_PRICE_ASC);

        // Set default condition for quiz status
        $post['quiz_status'] = Quiz::PUBLISHED;

        // Prepare pagination variables
        $offset = ($posts['pageno'] - 1) * $posts['pagesize'];

        $db = FatApp::getDb();
        $query = "SELECT q.quiz_id, q.quiz_name, q.quiz_price, q.quiz_level, q.quiz_type, t.user_username 
              FROM tbl_quizzes q 
              LEFT JOIN tbl_teachers t ON t.user_id = q.quiz_teacher_id 
              WHERE q.quiz_status = ?";

        // Add price sorting if provided
        if ($posts['price_sorting'] == AppConstant::SORT_PRICE_DESC) {
            $query .= " ORDER BY q.quiz_price DESC";
        } else {
            $query .= " ORDER BY q.quiz_price ASC";
        }

        // Add pagination
        $query .= " LIMIT $offset, {$posts['pagesize']}";

        // Prepare and execute the query
        $result = $db->query($query, [AppConstant::ACTIVE]);

        // Fetch quizzes and process them
        $quizzes = [];
        if ($result) {
            $quizzes = $db->fetchAll($result); // Get quizzes as an array

            // Now, for each quiz, get the count of associated questions
            foreach ($quizzes as &$quiz) {
                $quizId = $quiz['quiz_id'] ?? null;

                if (!empty($quizId)) {
                    // Query to count questions for each quiz
                    $questionQuery = "SELECT COUNT(*) AS question_count FROM tbl_questions WHERE question_quiz_id = ?";
                    $questionResult = $db->query($questionQuery, [$quizId]);

                    if ($questionResult) {
                        $questionRow = $db->fetch($questionResult);
                        $quiz['question_count'] = $questionRow['question_count'] ?? 0;
                    } else {
                        $quiz['question_count'] = 0;
                    }
                } else {
                    $quiz['question_count'] = 0;
                }
            }
        }

        // Get the total record count (for pagination)
        $countQuery = "SELECT COUNT(*) AS total FROM tbl_quizzes WHERE quiz_status = ?";
        $countResult = $db->query($countQuery, [AppConstant::ACTIVE]);
        $totalCount = 0;
        if ($countResult) {
            $countRow = $db->fetch($countResult);
            $totalCount = $countRow['total'] ?? 0;
        }

        // Return the response in the required format
        $this->sets([
            'quizzes' => $quizzes,
            'recordCount' => $totalCount,
            'pageCount' => ceil($totalCount / $posts['pagesize']),
        ]);

        // Render the response without using a specific template
        $this->_template->render(false, false);
    }


    /**
     * View course detail
     *
     * @param string $slug
     * @return void
     */
    public function view(string $slug)
    {
        if (empty($slug)) {
            FatUtility::exitWithErrorCode(404);
        }
        /* get course details */
        $srch = new CourseSearch($this->siteLangId, $this->siteUserId, 0);
        $srch->addSearchDetailFields();
        $srch->applyPrimaryConditions();
        $srch->addCondition('course_slug', '=', $slug);
        $srch->joinTable(TeacherStat::DB_TBL, 'INNER JOIN', 'testat.testat_user_id = teacher.user_id', 'testat');
        $srch->joinTable(
            User::DB_TBL_LANG,
            'LEFT JOIN',
            'userlang.userlang_user_id = teacher.user_id AND userlang.userlang_lang_id = ' . $this->siteLangId,
            'userlang'
        );
        $srch->addCondition('course.course_active', '=', AppConstant::ACTIVE);
        $srch->addCondition('teacher.user_username', '!=', "");
        $srch->setPageSize(1);
        $courses = $srch->fetchAndFormat(true);
        if (empty($courses)) {
            FatUtility::exitWithErrorCode(404);
        }
        $course = current($courses);
        $teacherCourses = TeacherSearch::getCourses([$course['course_teacher_id']]);
        $course['teacher_courses'] = $teacherCourses[$course['course_teacher_id']] ?? 0;
        /* get more course by the same teacher */
        $courseObj = new CourseSearch($this->siteLangId, $this->siteUserId, 0);
        $moreCourses = $courseObj->getMoreCourses($course['course_teacher_id'], $course['course_id']);
        /* get intended learner section details */
        $intended = new IntendedLearner();
        $intendedLearners = $intended->get($course['course_id'], $this->siteLangId);
        /* get curriculum */
        $curriculum = $this->curriculum($course['course_id']);
        /* fetch rating data */
        $revObj = new CourseRatingReview();
        $reviews = $revObj->getRatingStats($course['course_id']);
        /* Get order course data */
        $orderCourse = OrderCourse::getAttributesById($course['ordcrs_id'], ['ordcrs_status', 'ordcrs_reviewed']);
        $canRate = false;
        if ($orderCourse) {
            $canRate = OrderCourseSearch::canRate($orderCourse, $this->siteUserType);
        }
        /* Get and fill form data */
        $frm = $this->getReviewSrchForm();
        $frm->fill(['course_id' => $course['course_id']]);
        /* checkout form */
        $cart = new Cart($this->siteUserId, $this->siteLangId);
        $checkoutForm = $cart->getCheckoutForm([0 => Label::getLabel('LBL_NA')]);
        $checkoutForm->fill(['order_type' => Order::TYPE_COURSE]);


        $db = FatApp::getDb();

        $courseId = $course['course_id'] ?? null; // Safely get the course ID

        if (!empty($courseId)) {

            $query = "SELECT COUNT(*) AS section_count FROM tbl_sections WHERE section_course_id = " . $courseId . " AND section_quiz_id != 0";

            $result = $db->query($query); // Pass courseId as parameter

            if ($result) {
                $row = $db->fetch($result); // Fetch the result as an associative array
                $course['section_count'] = $row['section_count'] ?? 0;
            } else {
                $course['section_count'] = 0; // Default if query fails
            }
        } else {
            $course['section_count'] = 0; // Default if course ID is missing
        }


        $this->sets([
            'course' => $course,
            'moreCourses' => $moreCourses,
            'frm' => $frm,
            'intendedLearners' => $intendedLearners,
            'sections' => $curriculum['sections'],
            'videos' => $curriculum['videos'],
            'totalResources' => $curriculum['totalResources'],
            'reviews' => $reviews,
            'canRate' => $canRate,
            'checkoutForm' => $checkoutForm,
        ]);
        $this->_template->render();
    }

    /**
     * Preview video in popoup
     *
     * @param int $courseId
     * @return void
     */
    public function previewVideo(int $courseId)
    {
        $courseId = FatUtility::int($courseId);
        if ($courseId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $this->set('courseId', $courseId);
        /* get course details */
        $srch = new SearchBase(Course::DB_TBL, 'course');
        $srch->joinTable(
            Course::DB_TBL_LANG,
            'LEFT JOIN',
            'crsdetail.course_id = course.course_id',
            'crsdetail'
        );
        $srch->addFld('crsdetail.course_title');
        $srch->addCondition('course.course_id', '=', $courseId);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $this->set('course', FatApp::getDb()->fetch($srch->getResultSet()));
        $this->_template->render(false, false);
    }

    /**
     * Get curriculum list
     *
     * @param int $courseId
     * @return array
     */
    private function curriculum(int $courseId)
    {
        $srch = new SectionSearch($this->siteLangId, $this->siteUserId, User::LEARNER);
        $srch->addSearchListingFields();
        $srch->addCondition('section.section_course_id', '=', $courseId);
        $srch->applyPrimaryConditions();
        $srch->addOrder('section.section_order');
        $sections = $srch->fetchAndFormat();
        /* get list of lecture ids */
        $lectureIds = Lecture::getIds($sections);
        $videos = (count($lectureIds) > 0) ? Lecture::getVideos($lectureIds) : [];
        return [
            'videos' => $videos,
            'sections' => $sections,
            'totalResources' => array_sum(array_column($sections, 'total_resources'))
        ];
    }

    /**
     * Render course reviews
     *
     */
    public function reviews()
    {
        $frm = $this->getReviewSrchForm();
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        $srch = new SearchBase(RatingReview::DB_TBL, 'ratrev');
        $srch->joinTable(Course::DB_TBL, 'INNER JOIN', 'course.course_id = ratrev.ratrev_type_id', 'course');
        $srch->joinTable(User::DB_TBL, 'INNER JOIN', 'learner.user_id = ratrev.ratrev_user_id', 'learner');
        $srch->addCondition('ratrev.ratrev_status', '=', RatingReview::STATUS_APPROVED);
        $srch->addCondition('ratrev.ratrev_type', '=', AppConstant::COURSE);
        $srch->addCondition('ratrev.ratrev_type_id', '=', $post['course_id']);
        $srch->addMultipleFields([
            'user_first_name',
            'user_last_name',
            'ratrev_id',
            'ratrev_user_id',
            'ratrev_title',
            'ratrev_detail',
            'ratrev_overall',
            'ratrev_created',
            'course_reviews'
        ]);
        $srch->addOrder('ratrev.ratrev_id', $post['sorting']);
        $pagesize = AppConstant::PAGESIZE;
        $srch->setPageSize($pagesize);
        $srch->setPageNumber($post['pageno']);
        $this->sets([
            'reviews' => FatApp::getDb()->fetchAll($srch->getResultSet()),
            'pageCount' => $srch->pages(),
            'post' => $post,
            'pagesize' => $pagesize,
            'recordCount' => $srch->recordCount(),
            'frm' => $frm,
        ]);
        $this->_template->render(false, false);
    }

    /**
     * Get Review Form
     * 
     * @return Form
     */
    private function getReviewSrchForm(): Form
    {
        $frm = new Form('reviewFrm');
        $fld = $frm->addHiddenField('', 'course_id');
        $fld->requirements()->setRequired(true);
        $fld->requirements()->setIntPositive();
        $frm->addHiddenField('', 'sorting', RatingReview::SORTBY_NEWEST);
        $frm->addHiddenField('', 'pageno', 1);
        return $frm;
    }

    /**
     * Get video content for preview
     *
     * @param int $resourceId
     */
    public function resource(int $resourceId)
    {
        $resourceId = FatUtility::int($resourceId);
        if ($resourceId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $srch = new SearchBase(Lecture::DB_TBL_LECTURE_RESOURCE, 'lecsrc');
        $srch->joinTable(
            Lecture::DB_TBL,
            'INNER JOIN',
            'lecture.lecture_id = lecsrc.lecsrc_lecture_id',
            'lecture'
        );
        $srch->addCondition('lecsrc.lecsrc_deleted', 'IS', 'mysql_func_NULL', 'AND', true);
        $srch->addCondition('lecture_deleted', 'IS', 'mysql_func_NULL', 'AND', true);
        $srch->addMultipleFields(['lecsrc_link', 'lecture.lecture_title', 'lecsrc_course_id', 'lecsrc_id', 'lecsrc_lecture_id']);
        $srch->doNotCalculateRecords();
        $srch1 = clone $srch;

        $srch->addCondition('lecsrc.lecsrc_id', '=', $resourceId);
        $srch->setPageSize(1);
        $resource = FatApp::getDb()->fetch($srch->getResultSet());
        $this->set('resource', $resource);
        /* get free lectures */
        $srch1->joinTable(
            Lecture::DB_TBL,
            'INNER JOIN',
            'lecture.lecture_id = lecsrc.lecsrc_lecture_id',
            'lecture'
        );
        $srch1->addFld('lecture_duration');
        $srch1->addCondition('lecsrc.lecsrc_deleted', 'IS', 'mysql_func_NULL', 'AND', true);
        $srch1->addCondition('lecture_deleted', 'IS', 'mysql_func_NULL', 'AND', true);
        $srch1->addCondition('lecsrc_course_id', '=', $resource['lecsrc_course_id']);
        $srch1->addCondition('lecture_is_trial', '=', AppConstant::YES);
        $srch1->addCondition('lecsrc_type', '=', Lecture::TYPE_RESOURCE_EXTERNAL_URL);
        $this->set('lectures', FatApp::getDb()->fetchAll($srch1->getResultSet()));
        $this->_template->render(false, false);
    }

    /**
     * Auto Complete JSON
     */
    public function autoComplete()
    {
        $keyword = FatApp::getPostedData('term', FatUtility::VAR_STRING, '');
        if (empty($keyword)) {
            FatUtility::dieJsonSuccess(['data' => []]);
        }
        $filterTypes = Course::getFilterTypes();

        $courses = $this->getCourses($keyword);
        $data = [];
        if ($courses) {
            $data[] = $this->formatFiltersData($courses, Course::FILTER_COURSE);
        }
        /* find teachers */
        $teachers = $this->getTeachers($keyword);
        if (count($teachers) > 0) {
            $data[] = $this->formatFiltersData($teachers, Course::FILTER_TEACHER);
        }
        /* find tags */
        $tagsList = $this->getTags($keyword);
        $keyword = strtolower($keyword);
        if (count($tagsList)) {
            $list = [];
            foreach ($tagsList as $tags) {
                $tags = json_decode($tags['course_srchtags']);
                if (count($tags) > 0) {
                    foreach ($tags as $tag) {
                        if (stripos(strtolower($tag), $keyword) !== FALSE) {
                            $list[] = $tag;
                        }
                    }
                }
            }
            $child = [];
            if (count($list) > 0) {
                $list = array_unique($list);
                foreach ($list as $tag) {
                    $child[] = [
                        "id" => $tag,
                        "text" => $tag
                    ];
                }
            }

            $data[] = [
                'text' => $filterTypes[Course::FILTER_TAGS],
                'type' => Course::FILTER_TAGS,
                'children' => $child
            ];
        }
        echo json_encode($data);
        die;
    }

    /**
     * Function to format autocomplete filter data
     *
     * @param array $filtersData
     * @param int   $type
     * @return array
     */
    private function formatFiltersData(array $filtersData, int $type)
    {
        $filterTypes = Course::getFilterTypes();
        $child = [];
        foreach ($filtersData as $data) {
            $child[] = [
                "id" => $data['id'],
                "text" => $data['name']
            ];
        }
        return [
            'text' => $filterTypes[$type],
            'type' => $type,
            'children' => $child
        ];
    }

    /**
     * Function to get courses for autocomplete filter
     *
     * @param string $keyword
     * @return array
     */
    private function getCourses($keyword = '')
    {
        $srch = new CourseSearch($this->siteLangId, $this->siteUserId, 0);
        $srch->applyPrimaryConditions();
        $srch->addCondition('course.course_status', '=', Course::PUBLISHED);
        $srch->addCondition('course.course_active', '=', AppConstant::ACTIVE);
        $srch->addCondition('teacher.user_username', '!=', "");
        $srch->addMultiplefields(['course.course_id as id', 'crsdetail.course_title as name']);
        if (!empty($keyword)) {
            $srch->addCondition('crsdetail.course_title', 'LIKE', '%' . $keyword . '%');
        }
        $srch->setPageSize(5);
        $srch->doNotCalculateRecords();
        $courses = FatApp::getDb()->fetchAll($srch->getResultSet());
        if (!empty($courses)) {
            return $courses;
        }
        return [];
    }

    /**
     * Function to get teachers for autocomplete filter
     *
     * @param string $keyword
     * @return array
     */
    private function getTeachers($keyword = '')
    {
        $srch = new TeacherSearch($this->siteLangId, $this->siteUserId, User::LEARNER);
        $srch->applyPrimaryConditions();
        $cnd = $srch->addCondition('teacher.user_first_name', 'LIKE', '%' . $keyword . '%');
        $cnd->attachCondition('teacher.user_last_name', 'LIKE', '%' . $keyword . '%', 'OR');
        $cnd->attachCondition('mysql_func_CONCAT(teacher.user_first_name, " ", teacher.user_last_name)', 'LIKE', '%' . $keyword . '%', 'OR', true);
        $srch->addOrder('teacher.user_first_name', 'ASC');
        $srch->addMultiplefields(['teacher.user_id as id', 'CONCAT(teacher.user_first_name, " ", teacher.user_last_name) as name']);
        $srch->setPageSize(5);
        $srch->doNotCalculateRecords();
        $teachers = FatApp::getDb()->fetchAll($srch->getResultSet());
        if (!empty($teachers)) {
            return $teachers;
        }
        return [];
    }

    /**
     * Function to get tags for autocomplete filter
     *
     * @param string $keyword
     * @return array
     */
    private function getTags($keyword = '')
    {
        $srch = new SearchBase(Course::DB_TBL_LANG, 'crsdetail');
        $srch->joinTable(
            Course::DB_TBL,
            'INNER JOIN',
            'crsdetail.course_id = course.course_id',
            'course'
        );
        $srch->doNotCalculateRecords();
        $srch->setPageSize(5);
        $srch->addCondition('mysql_func_LOWER(course_srchtags)', 'LIKE', '%' . strtolower($keyword) . '%', 'AND', true);
        $srch->addFld('course_srchtags');
        $srch->addCondition('course.course_deleted', 'IS', 'mysql_func_NULL', 'AND', true);
        $srch->addCondition('course.course_status', '=', Course::PUBLISHED);
        $srch->addCondition('course.course_active', '=', AppConstant::ACTIVE);
        $tagsList = FatApp::getDb()->fetchAll($srch->getResultSet());
        if (!empty($tagsList)) {
            return $tagsList;
        }
        return [];
    }

}
