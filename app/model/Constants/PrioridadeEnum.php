<?php

enum PrioridadeEnum: int
{

    case ABERTA = 1;
    case EXECUCAO = 2;
    case CONCLUIDA = 3;
    case CANCELADA = 4;

    public function label(): string
    {
        return match($this)
        {
            self::ABERTA=> "ABERTA",
            self::EXECUCAO=>"EM EXECUÇÃO",
            self::CONCLUIDA=>"CONCLUIDA",
            self::CANCELADA=>"CANCELADA"
        };
    }


}