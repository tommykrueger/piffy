<?php

namespace Piffy\Framework;

class Request
{
    private static $requestData;

    public static function has(string $key): bool
    {
        return !empty(self::$requestData[$key]);
    }

    public static function get(string $key): ?string
    {
        return self::$requestData[$key] ?? null;
    }

    public static function all(): void
    {
        $requestContent = trim(file_get_contents("php://input"));

        if ($requestContent) {
            $requestContent = json_decode($requestContent, true);
            self::$requestData = $requestContent;
            return;
        }

        $request = $_REQUEST['data'] ?? $_REQUEST;
        if (!empty($request)) {
            self::$requestData = $request;
        }
    }
}