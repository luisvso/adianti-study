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

    public static function gerarIdentificador(string $prefixo) : string
    {

        $ano = date("Y");
        return $prefixo . "-" . $ano . "-" . strtoupper(bin2hex(random_bytes(2)));
    }

}