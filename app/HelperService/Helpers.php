<?php

namespace App\HelperService;

class Helpers
{
    public function response(bool $isSuccess, string $report, $details, $errors, $responseCode)
    {
        return response()->json([
            'success' => $isSuccess,
            'responseCode' => $responseCode,
            'errors' => $errors,
            'report' => $report,
            'details' => $details
        ], $responseCode, [], JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
    }
}

