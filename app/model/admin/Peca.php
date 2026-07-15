<?php

use Adianti\Database\TRecord;

class Peca extends TRecord
{
    const TABLENAME = "peca";
    const PRIMARYKEY = "id_peca";
    const IDPOLICY = "serial";

    public function __construct($id_ordemservico = NULL, $objectTrue = TRUE)
    {
        parent::__construct($id_ordemservico, $objectTrue);
        parent::addAttribute("cd_peca");
        parent::addAttribute("nm_peca");
        parent::addAttribute("vl_unitario");
    }
}