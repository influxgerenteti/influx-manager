<?php
namespace App\BO\Principal;

use Doctrine\ORM\EntityManagerInterface;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Luiz Antonio Costa
 */
class OrigemOcorrenciaBO extends GenericBO
{


    function __construct(EntityManagerInterface $entityManager)
    {

    }

    /**
     *
     * @param \App\Repository\Principal\OrigemOcorrenciaRepository $origemOcorrenciaRepository
     * @param string $tipo
     * @param string $mensagemErro
     * @param \App\Entity\Principal\OrigemOcorrencia|null $retornoORM
     *
     * @return boolean
     */
    public static function verificaOrigemOcorrenciaPorTipoExiste(\App\Repository\Principal\OrigemOcorrenciaRepository $origemOcorrenciaRepository, $tipo, &$mensagemErro, &$retornoORM=null)
    {
        $retornoORM = $origemOcorrenciaRepository->findOneBy([ConstanteParametros::CHAVE_TIPO_ORIGEM => $tipo]);
        if (is_null($retornoORM) === false) {
            return true;
        }

        $mensagemErro = "Origem não existente para o tipo:" . $tipo . "\n";
        return false;
    }


}
