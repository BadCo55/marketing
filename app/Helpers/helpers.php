<?php

if (!function_exists('snakeToNormal')) {
    function snakeToNormal($string)
    {
        return ucwords(str_replace('_', ' ', $string));
    }
}