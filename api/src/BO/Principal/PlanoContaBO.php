<?php
namespace App\BO\Principal;

use Doctrine\ORM\EntityManagerInterface;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Marcelo André Naegeler
 */
class PlanoContaBO extends GenericBO
{
    /**
     *
     * @var \App\Repository\Principal\PlanoContaRepository
     */
    private $planoContaRepository;

    function __construct(EntityManagerInterface $entityManager)
    {
        $this->planoContaRepository = $entityManager->getRepository(\App\Entity\Principal\PlanoConta::class);
        parent::configuraGenericBO(
            [
                "franqueadaRepository" => $entityManager->getRepository(\App\Entity\Principal\Franqueada::class),
            ]
        );
    }

    /**
     * Executa e verifica se podemos prosseguir com o processo de salvar os dados na base
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param boolean $bAtualizar Para atualizar o registro ou nao
     *
     * @return boolean
     */
    public function podeSalvar(&$parametros, &$mensagemErro, $bAtualizar=false)
    {
        $bRetorno = true;
        if (((isset($parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === true) && (empty($parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === false))|| ($bAtualizar === true)) {
            if ($bAtualizar === true) {
                $mensagemErro = "";
                $bRetorno     = true;
            } else {
                $bRetorno = self::verificaFranqueadaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FRANQUEADA]);
            }

            if ($bRetorno === true) {
                if ((isset($parametros[ConstanteParametros::CHAVE_PAI]) === true) && (empty($parametros[ConstanteParametros::CHAVE_PAI]) === false)) {
                    $bRetorno = $this->verificaPlanoContaExiste($this->planoContaRepository, $parametros[ConstanteParametros::CHAVE_PAI], $mensagemErro, $parametros[ConstanteParametros::CHAVE_PAI]);
                }
            }
        } else {
            $bRetorno     = false;
            $mensagemErro = "Franqueada não encontrada na requisição.";
        }

        return $bRetorno;
    }

    /**
     * Verifica através da ID se existe o registro na base de dados
     *
     * @param \App\Repository\Principal\PlanoContaRepository $planoContaRepository Repositório para ser acessado estaticamente
     * @param int $id Chave primaria do PlanoConta
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\PlanoConta $planoContaORM Ponteiro para retornar o objeto
     *
     * @return boolean
     */
    public static function verificaPlanoContaExiste($planoContaRepository, $id, &$mensagemErro, &$planoContaORM=null)
    {
        $planoContaORM = $planoContaRepository->find($id);

        if (is_null($planoContaORM) === true) {
            $mensagemErro .= "Não foi possivel encontrar o plano de contas na base de dados.";
            return false;
        }

        return true;
    }

    /**
     * Verifica se o plano de contas informado possui filhos
     *
     * @param int $id
     * @param string $mensagemErro
     * @param \App\Repository\Principal\PlanoContaRepository $planoContaRepository
     * @param bool $bRetornaMsg
     *
     * @return boolean
     */
    public static function verificaPlanoContaTemFilhos($id, &$mensagemErro, $planoContaRepository=null, $bRetornaMsg=false)
    {
        if (is_null($planoContaRepository) === false) {
            $planoContaORM = $planoContaRepository->findBy(["pai" => $id]);
        } else {
            $planoContaORM = self::$planoContaRepository->findBy(["pai" => $id]);
        }

        if (count($planoContaORM) === 0) {
            return false;
        }

        if ($bRetornaMsg === true) {
            $mensagemErro .= "Este plano de contas possui filhos.";
        }

        return true;
    }


}
