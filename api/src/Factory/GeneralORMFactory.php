<?php
namespace App\Factory;

/**
 *
 * @author Luiz Antonio Costa
 */
class GeneralORMFactory
{


    /**
     * Cria um novo objeto ORM
     *
     * @param string $classe A Entity que deseja criar como por exemplo \App\Entity\Principal\Usuario::class
     * @param bool $comArgumentos Flag para informar a fabrica se deseja criar o objeto com as propriedades e valores
     * @param array $argumentos Os atributos da classe ORM e seus valores<br><b>Exemplo da ORM de Usuarios:</b> array("nome"=>"valor","data_ultimo_login"=> new \DateTime())
     *
     * @return $classe
     */
    public static function criar(string $classe, bool $comArgumentos=false, array $argumentos=[])
    {

        $classe    = "\\" . $classe;
        $objetoORM = new $classe;
        if ($comArgumentos === true) {
            \App\Helper\FunctionHelper::setParams($argumentos, $objetoORM);
        }

        return $objetoORM;
    }


}
