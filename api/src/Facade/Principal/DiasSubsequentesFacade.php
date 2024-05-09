<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Luiz A Costa
 */
class DiasSubsequentesFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\DiasSubsequentesRepository
     */
    private $diasSubsequentesRepository;

    /**
     *
     * @var \App\Repository\Principal\FranqueadaRepository
     */
    private $franqueadaRepository;

    /**
     * Verifica se conseguiu preencher a lista de dias subsequentes para serem adicionados
     *
     * @param int[] $arrayIdSubsequentes
     * @param \App\Entity\Principal\DiasSubsequentes[] $retornoObjetosORM
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function retornaListaDiasSubsequentes($arrayIdSubsequentes, &$retornoObjetosORM, &$mensagemErro)
    {
        $bRetorno = true;
        for ($i = 0; $i < count($arrayIdSubsequentes); $i++) {
            $objetoORM = $this->diasSubsequentesRepository->find($arrayIdSubsequentes[$i]);
            if (is_null($objetoORM) === true) {
                $mensagemErro = "Dia Subsequente com a id:" . $arrayIdSubsequentes[$i] . " não encontrado.";
                $bRetorno     = false;
                break;
            }

            $retornoObjetosORM[] = $objetoORM;
        }

        return $bRetorno;
    }

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->diasSubsequentesRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\DiasSubsequentes::class);
        $this->franqueadaRepository       = self::getEntityManager()->getRepository(\App\Entity\Principal\Franqueada::class);
    }

    /**
     * Busca todos os diasSubsequentes na base de dados
     *
     * @return array|NULL
     */
    public function listarTodosDiasSubsequentes()
    {
        return $this->diasSubsequentesRepository->buscaDiasSubsequentes();
    }

    /**
     * Busca o registro pela chave primaria
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $idFranqueada Chave primaria do registro
     *
     * @return array
     */
    public function buscarPorFranqueada(&$mensagemErro, $idFranqueada)
    {
        return $this->diasSubsequentesRepository->buscaDiasSubsequentesPorFranqueada($idFranqueada);
    }

    /**
     * Atualiza um registro no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $idFranqueada Chave primaria do registro
     * @param array $parametros Campos e valores que iram ser atualizados
     *
     * @return boolean
     */
    public function atualizar(&$mensagemErro, $idFranqueada, $parametros=[])
    {
        $objetoORM = $this->franqueadaRepository->find($idFranqueada);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Franqueada não encontrada na base de dados.";
        } else {
            $retornoObjetosORM = [];
            if ($this->retornaListaDiasSubsequentes($parametros[ConstanteParametros::CHAVE_DIAS_SUBSEQUENTES], $retornoObjetosORM, $mensagemErro) === true) {
                $diasSubsequentesArray = $objetoORM->getDiasSubsequentes();
                $tamanhoArray          = $diasSubsequentesArray->count();
                for ($i = 0;$i < $tamanhoArray;$i++) {
                    $diaSubsequenteRemovido = $diasSubsequentesArray->get($i);
                    $objetoORM->removeDiasSubsequente($diaSubsequenteRemovido);
                    $diaSubsequenteRemovido->removeFranqueada($objetoORM);
                }

                foreach ($retornoObjetosORM as $diaSubsequenteORM) {
                    $objetoORM->addDiasSubsequente($diaSubsequenteORM);
                }

                self::flushSeguro($mensagemErro);
            }
        }

        return empty($mensagemErro);
    }


}
