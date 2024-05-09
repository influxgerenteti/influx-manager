<?php
namespace App\BO\Principal;

use Doctrine\ORM\EntityManagerInterface;

/**
 *
 * @author Luiz Antonio Costa
 */
class FranqueadaBO
{


    /**
     *
     * @var \App\Repository\Principal\FranqueadaRepository
     */
    private static $franqueadaRepository;

    function __construct(EntityManagerInterface $entityManager)
    {
        self::$franqueadaRepository = $entityManager->getRepository(\App\Entity\Principal\Franqueada::class);
    }

    /**
     * Verifica se a Franqueada ja existe no banco
     *
     * @param \App\Repository\Principal\FranqueadaRepository $franqueadaRepository Repositorio da Franqueada
     * @param array $params Parametros que possuam os campos das entidades e valores
     *
     * @return boolean
     */
    public static function franqueadaExisteBanco($franqueadaRepository, $params, &$franqueadaORM=null)
    {
        if (count($params) > 3) {
            $franqueadaORM = $franqueadaRepository->findOneByCnpj($params["cnpj"]);
        } else {
            $franqueadaORM = $franqueadaRepository->findOneBy($params);
        }

        if (is_null($franqueadaORM) === true) {
            return false;
        }

        return true;
    }

    /**
     * Compara a data informada no parametro com a data atual. Se a diferenca for maior que a configurada no banco, retornara true.
     *
     * @param \DateTime $dataComparada
     */
    public static function verificaDataMaiorTempoLimiteDeAlteracao($dataComparada)
    {
        $franqueada = self::$franqueadaRepository->find(\App\Helper\VariaveisCompartilhadas::$franqueadaID);

        $dataAtual       = new \DateTime();
        $intervaloData   = $dataComparada->diff($dataAtual);
        $intervaloEmDias = $intervaloData->format('%a');
        $limiteEmDias    = $franqueada->getLimiteDiasAlteracaoDocumento();

        if ($intervaloEmDias < $limiteEmDias) {
            return false;
        }

        return true;
    }


}
