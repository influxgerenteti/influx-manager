<?php
namespace App\BO\Principal;

use Doctrine\ORM\EntityManagerInterface;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Luiz Antonio Costa
 */
class TipoVisibilidadeBO extends GenericBO
{


    function __construct(EntityManagerInterface $entityManager)
    {
        parent::configuraGenericBO(
            [
                "franqueadaRepository" => $entityManager->getRepository(\App\Entity\Principal\TipoVisibilidade::class),
            ]
        );
    }

    /**
     * Verifica campos relacionais
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaCamposRelacionaisObrigatorios(&$parametros, &$mensagemErro)
    {
        if (self::verificaFranqueadaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === true) {
            return true;
        }

        return false;
    }

    /**
     * Verifica campos que fazem parte das regras de negocio
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function podeSalvar(&$parametros, &$mensagemErro)
    {
        if ($this->verificaCamposRelacionaisObrigatorios($parametros, $mensagemErro) === true) {
            return true;
        }

        return false;
    }

    /**
     * Verifica se existe a tipo_visibilidade no banco de dados, se existir, ele retorna no ultimo parametro na funcao, caso contrario, ele preenchera a msg de erro e retornara falso
     *
     * @param \App\Repository\Principal\TipoVisibilidadeRepository $tipoVisibilidadeRepository Repositorio da TipoVisibilidade
     * @param integer $id Chave primaria da tipo_visibilidade
     * @param string $mensagemErro Msg de erro para ser retornada ao front-end
     * @param \App\Entity\Principal\TipoVisibilidade|null $tipoVisibilidadeORM Retorno no caso de sucesso
     *
     * @return boolean true|false
     */
    public static function verificaTipoVisibilidadeExiste(\App\Repository\Principal\TipoVisibilidadeRepository $tipoVisibilidadeRepository, $id, &$mensagemErro, &$tipoVisibilidadeORM)
    {
        $tipoVisibilidadeORM = $tipoVisibilidadeRepository->find($id);
        if (is_null($tipoVisibilidadeORM) === true) {
            $mensagemErro = "TipoVisibilidade não encontrada na base de dados.";
            return false;
        }

        return true;
    }


}
