<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;

class ElevesImport implements ToArray
{
    /**
     * On récupère tout le fichier sous forme de tableau brut (avec les en-têtes en ligne 0)
     * pour pouvoir mapper les colonnes nous-mêmes, quel que soit l'ordre du fichier.
     */
    public function array(array $array)
    {
        return $array;
    }
}
