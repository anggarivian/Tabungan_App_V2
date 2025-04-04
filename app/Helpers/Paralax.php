<?php
if (!function_exists('generateBoxShadow')) {
    function generateBoxShadow($count = 100) {
        $shadows = [];
        for ($i = 0; $i < $count; $i++) {
            $x = rand(0, 2000);
            $y = rand(0, 2000);
            $shadows[] = "{$x}px {$y}px #FFF";
        }
        return implode(',', $shadows);
    }
}
