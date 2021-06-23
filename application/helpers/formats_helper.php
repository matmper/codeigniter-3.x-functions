<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
* Created by github.com/matmper
* Permission to copy, use and edit is free, but change the names and credits when you do this
* Use is at the user's own risk, no guarantee for support, updates, code or security
* 2020 (Use mask)
*/

/* DATE & TIME */

// d/m/Y - H:i:s
if (!function_exists('date_time_complete')) {
    function date_time_complete($date)
    {
        if ($date) {
            return date("d/m/Y - H:i:s", strtotime($date));
        }
        
        return null;
    }
}

// dd/mm/yy - H:i
if (!function_exists('date_time_mini')) {
    function date_time_mini($date)
    {
        if ($date) {
            return date("d/m/y - H:i", strtotime($date));
        }
        
        return null;
    }
}

if (!function_exists('date_time_period')) {
    function date_time_period($date, $time, $period = 'days', $type = '+')
    {
        if (!$date) {
            $date = date('Y-m-d H:i:s');
        }

        if (!is_numeric($time)) {
            return null;
        }

        if (in_array($period, ['seconds', 'minutes', 'hours', 'days', 'months', 'years'])) {
            // check date format to return
            if (strpos($date, '-') !== false && strpos($date, ':') !== false) {
                $format = 'Y-m-d H:i:s';
            } elseif (strpos($date, '-') !== false) {
                $format = 'Y-m-d';
            } elseif (strpos($date, ':') !== false) {
                $format = 'H:i:s';
            } else {
                return null;
            }

            // check if is to add or remove period
            $type = in_array($type, ['+', '-']) ? $type : '+';

            return date($format, strtotime("{$date} {$type}{$time} {$period}"));
        }

        return null;
    }
}

/* DATE */

 // dd/mm/yyyy
if (!function_exists('date_complete')) {
    function date_complete($date)
    {
        if ($date) {
            return date("d/m/Y", strtotime($date));
        }
        
        return null;
    }
}

// dd/mm/yy
if (!function_exists('date_mini')) {
    function date_mini($date)
    {
        if ($date) {
            return date("d/m/y", strtotime($date));
        }
        
        return null;
    }
}

// dd/mm/yyyy -> yyyy-mm-dd
if (!function_exists('date_sql')) {
    function date_sql($date)
    {
        if ($date) {
            return implode('-', array_reverse(explode('/', $date)));
        }
        
        return null;
    }
}

if (!function_exists('date_validate')) {
    function date_validate($date): bool
    {
        if ($date) {
            $date   = explode("-", $date); // fatia a string $dat em pedados, usando / como referência
            $d      = $date[2];
            $m      = $date[1];
            $y      = $date[0];
            $res    = checkdate($m, $d, $y);
            if ($res == 1) {
                return true;
            }
        }

        return false;
    }
}

/* TIME */

// H:i:s
if (!function_exists('time_complete')) {
    function time_complete($date)
    {
        if ($date) {
            return date("H:i:s", strtotime($date));
        }
        
        return null;
    }
}

// H:i
if (!function_exists('time_mini')) {
    function time_mini($date)
    {
        if ($date) {
            return date("H:i", strtotime($date));
        }
        
        return null;
    }
}

/*  CURRENCY AND MONEY */
if (!function_exists('float_currency')) {
    function float_currency($value, $currency = 'BRL', $currency_position = 'before')
    {
        if (!$value || !is_numeric($value)) {
            return null;
        }

        switch ($currency) {
            case 'R$':
            case 'BRL':
                $value  = number_format($value, 2, ',', '.');
                break;
            default:
                $value  = number_format($value, 2, '.', ',');
                break;
        }

        switch ($currency_position) {
            case 'before':
                return $currency . $value;
            break;
            case 'after':
                return $value . $currency;
            break;
            default:
                return $value;
            break;
        }
    }
}
