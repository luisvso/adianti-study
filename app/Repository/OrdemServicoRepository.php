<?php

use Adianti\Database\TTransaction;

class OrdemServicoRepository
{
    public function __construct()
    {

    }

    public static function onSave(OrdemServico $object)
    {

        TTransaction::open("desenvolvimento");

        $object->store();

        TTransaction::close();

    }

    public function onDelete($param)
    {
        $key = $param["key"];

        TTransaction::open("desenvolvimento");

        $object = new OrdemServico($key);
        $object->delete();

        TTransaction::close();
    }


}