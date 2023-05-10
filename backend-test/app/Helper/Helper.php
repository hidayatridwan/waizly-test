<?php

namespace RidwanHidayat\BackendTest\Helper;

class Helper
{

    public static string $key = 'd584c96e6c1ba3ca448426f66e552e8e';

    public static function parseToArray(): void
    {
        $stream = file_get_contents('php://input');
        $raw = json_decode($stream, true);

        if ($raw !== null) {
            foreach ($raw as $key => $row) {
                $_POST[$key] = $row;
            }
        }
    }
}