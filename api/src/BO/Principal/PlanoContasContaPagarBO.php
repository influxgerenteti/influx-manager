<?php
namespace App\BO\Principal;

use Doctrine\ORM\EntityManagerInterface;

/**
 *
 * @author Marcelo André Naegeler
 */
class PlanoContasContaPagarBO extends GenericBO
{
    /**
     *
     * @var \App\Repository\Principal\PlanoContasContaPagarRepository
     */
    private $planoContasContaPagarRepository;

    function __construct(EntityManagerInterface $entityManager)
    {
        $this->planoContasContaPagarRepository = $entityManager->getRepository(\App\Entity\Principal\PlanoContasContaPagar::class);
    }

    /**
     * Executa e verifica se podemos prosseguir com o processo de salvar os dados na base
     *
     * @param array $parametros Array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     *
     * @return boolean
     */
    public function podeSalvar(&$parametros, &$mensagemErro)
    {
    }

    /**
     * Verifica através da ID se existe o registro na base de dados
     *
     * @param int $id
     * @param string $mensagemErro
     * @param null|\App\Entity\Principal\PlanoContasContaPagar $planoContasContaPagarORM Ponteiro para retornar o objeto
     *
     * @return boolean
     */
    public function verificaPlanoContasContaPagarExiste ($id, &$mensagemErro, &$planoContasContaPagarORM=null)
    {
        $planoContasContaPagarORM = $this->planoContasContaPagarRepository->find($id);

        if (is_null($planoContasContaPagarORM) === true) {
            $mensagemErro .= "Não foi possivel encontrar o plano de contas na base de dados.";
            return false;
        }

        return true;
    }


}
