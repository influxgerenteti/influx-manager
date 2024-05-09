<?php
/**
 * @desc    Classe para armazenar valores temporários para variáveis usadas em todo o sistema
 * @author  Marcelo A Naegeler <marcelo@gatilabs.com.br>
 * @license MIT <http://gatilabs.com.br>
 */

namespace App\Helper;

class VariaveisCompartilhadas
{


    /**
     * Guarda a ID da franqueada passada por query param
     *
     * @var integer
     */
    public static $franqueadaID = null;

    /**
     * Guarda a ID do usuário logado
     *
     * @var integer
     */
    public static $usuarioID = null;

}
