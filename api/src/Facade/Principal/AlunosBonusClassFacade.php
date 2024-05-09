<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\BO\Principal\AlunosBonusClassBO;
use App\Helper\ConstanteParametros;
use App\Helper\VariaveisCompartilhadas;
use App\Helper\SituacoesSistema;

/**
 *
 * @author Dayan Freitas
 */
class AlunosBonusClassFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\AlunosBonusClassRepository
     */
    private $alunosBonusClassRepository;

    /**
     *
     * @var \App\BO\Principal\AlunosBonusClassBO
     */
    private $alunosBonusClassBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->alunosBonusClassRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\AlunosBonusClass::class);
        $this->alunosBonusClassBO         = new AlunosBonusClassBO(self::getEntityManager());
    }

    /**
     * Lista todos os registros do banco de dados
     *
     * @param array $parametros Parametros da requisicao
     *
     * @return array
     */
    public function listar($parametros)
    {

        $retornoRepositorio = $this->alunosBonusClassRepository->filtrarBonusClassPorPagina($parametros);
        $retorno            = [
            ConstanteParametros::CHAVE_TOTAL => $retornoRepositorio->getTotalItemCount(),
            ConstanteParametros::CHAVE_ITENS => $retornoRepositorio->getItems(),
        ];
        return $retorno;
    }

    /**
     * Busca o registro pela chave primaria
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id Chave primaria do registro
     *
     * @return array|Object
     */
    public function buscarPorId(&$mensagemErro, $id)
    {

    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return mixed|null|Object
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $objetoORM = null;
        if ($this->alunosBonusClassBO->podeSalvar($parametros, $mensagemErro) === true) {
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\AlunosBonusClass::class, true, $parametros);
            self::persistSeguro($objetoORM, $mensagemErro);
        }

        return (is_null($objetoORM) === false) && (empty($mensagemErro) === true);
    }

    /**
     * Atualiza um registro no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id Chave primaria do registro
     * @param array $parametros Campos e valores que iram ser atualizados
     *
     * @return boolean
     */
    public function atualizar(&$mensagemErro, $id, $parametros=[])
    {
        $objetoORM = $this->alunosBonusClassRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Aluno bonus class não encontrado na base de dados.";
        } else {
            if ($this->alunosBonusClassBO->podeSalvar($parametros, $mensagemErro) === true) {
                self::getFctHelper()->setParams($parametros, $objetoORM);
            }
        }

        return empty($mensagemErro);
    }

    /**
     * Cria ou atualiza bonus class dos alunos
     *
     * @param array $parametros
     * @param string $mensagemErro
     * @param string $retornoAddAlunoPrincipal
     *
     * @return boolean|mixed|NULL|object
     */
    public function criaAtualizaAlunosBonusClass($parametros, &$mensagemErro, &$retornoAddAlunoPrincipal)
    {
        $bPossuiAlunoBonusClass = isset($parametros[ConstanteParametros::CHAVE_ID]);

        
        if ($bPossuiAlunoBonusClass === true) {
            $alunoBonusClassId = $parametros[ConstanteParametros::CHAVE_ID];
            unset($parametros[ConstanteParametros::CHAVE_ID]);
            $bSuccesso = $this->atualizar($mensagemErro, $alunoBonusClassId, $parametros);
        } else {
            if ($parametros['selecionado'] == 1) {
                if ($this->alunosBonusClassRepository->filtrarBonusClassAlunoPrincipal($parametros)) {
                    $parametros['selecionado'] = '0';
                    $retornoAddAlunoPrincipal = 'RESERVA';
                    
               }
            }
            
            $bSuccesso = $this->criar($mensagemErro, $parametros);
        }

        return $bSuccesso;
    }

    /**
     * Remove um registro do banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para o front-end
     * @param int $id Chave primaria do registro
     *
     * @return boolean
     */
    public function remover(&$mensagemErro, $id)
    {

        $objetoORM = $this->alunosBonusClassRepository->find($id);
        if (is_object($objetoORM) === true) {
            self::removerSeguro($objetoORM, $mensagemErro);
            self::flushSeguro($mensagemErro);
        } else {
            $mensagemErro = 'AlunoBonusClass não encontrado';
        }

        return empty($mensagemErro);
    }


}
