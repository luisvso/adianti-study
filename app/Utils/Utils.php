<?php

class Utils
{
    public static function formatarValor($value)
    {

        if(is_numeric($value))
        {
            return number_format($value, 2, ",", ".");
        }

    }

}