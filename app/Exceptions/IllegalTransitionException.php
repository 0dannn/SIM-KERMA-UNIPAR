<?php

namespace App\Exceptions;

use Exception;

class IllegalTransitionException extends Exception
{
    // Dilempar jika ada pelanggaran FSM (percobaan lompatan status liar atau pelanggaran wewenang peran RBAC)
}
