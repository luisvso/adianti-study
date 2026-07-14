<?php

use Adianti\Database\TRecord;

class Task extends TRecord
{
    const TABLENAME = 'task';
    const PRIMARYKEY = 'id_task';
    const IDPOLICY = 'serial';

    public function __construct($id_task = NULL, $objectTrue = true)
    {
        parent::__construct($id_task, true);
        parent::addAttribute("nm_titulo");
        parent::addAttribute("ds_task");
        parent::addAttribute("tp_prioridade");
    }

}