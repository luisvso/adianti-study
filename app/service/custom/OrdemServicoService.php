<?php

use Adianti\Database\TTransaction;
use Adianti\Widget\Dialog\TMessage;

class OrdemServicoService
{
    public function __construct()
    {
    }

    public static function onSave()
    {
    }

    public function onDelete()
    {
    }

    public function onEdit($param)
    {
        try
        {
        }

        catch (Exception $ex)
        {
            new TMessage("error", $ex);
            TTransaction::rollback();
        }
    }
}
