<?php

use Adianti\Core\AdiantiCoreLoader;
use Adianti\Database\TTransaction;
use Adianti\Widget\Dialog\TToast;

class TaskRepository
{

    public function __construct()
    {
    }

    public static function onSave(Task $object)
    {

        TTransaction::open('desenvolvimento');

        $object->store();
        TToast::show("success", "Ordem de Serviço cadastrada com sucesso", "top right", "far:check-circle");

        TTransaction::close();

    }

    public static function onDelete($param)
    {

        $key = $param['key'];

        TTransaction::open('desenvolvimento');

        $object = new Task($key);

        $object->delete();

        TTransaction::close();

    }


}