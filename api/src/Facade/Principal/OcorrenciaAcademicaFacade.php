<?php

namespace App\Facade\Principal;

use App\BO\Principal\OcorrenciaAcademicaBO;
use App\Facade\Principal\GenericFacade;
use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 *
 * @author Dayan Freitas
 */
class OcorrenciaAcademicaFacade extends GenericFacade
{


    /**
     *
     * @var \App\BO\Principal\OcorrenciaAcademicaBO
     */
    private $ocorrenciaAcademicaBO;

    /**
     *
     * @var \App\Repository\Principal\OcorrenciaAcademicaRepository $ocorrenciaAcademicaRepository
     */
    private $ocorrenciaAcademicaRepository;

    /**
     *
     * @var \App\Repository\Principal\FuncionarioRepository
     */
    private $funcionarioRepository;

    /**
     *
     * @var \App\Repository\Principal\ItemRepository
     */
    private $itemRepository;

    /**
     * Retorna a id do primeiro funcionario que tiver o usuario informado caso não encontre, retornará null
     *
     * @param string $usuarioId
     *
     * @return number|NULL
     */
    public function retornaFuncionarioIdDoUsuario($usuarioId)
    {
        $funcionarios = $this->funcionarioRepository->findBy([ConstanteParametros::CHAVE_USUARIO => $usuarioId]);
        if (count($funcionarios) > 0) {
            $funcionarioORM = $funcionarios[0];
            return $funcionarioORM->getId();
        }

        return null;
    }

    /**
     * Retorna a id da primeira ocorrencia que tiver o tipo informado caso nao encontre, retornará null
     *
     * @param int $itemId
     *
     * @return number|NULL
     */
    private function retornaTipoOcorrenciaIdDoTipoItem($itemId)
    {
        $itemORM           = $this->itemRepository->find($itemId);
        $tipoOcorrenciaORM = $itemORM->getTipoItem()->getTipoOcorrencia();
        if (is_null($tipoOcorrenciaORM) === false) {
            return $tipoOcorrenciaORM->getId();
        }

        return null;
    }

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->ocorrenciaAcademicaBO         = new OcorrenciaAcademicaBO(self::getEntityManager());
        $this->ocorrenciaAcademicaRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\OcorrenciaAcademica::class);
        $this->funcionarioRepository         = self::getEntityManager()->getRepository(\App\Entity\Principal\Funcionario::class);
        $this->itemRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\Item::class);
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
        $retornoRepositorio = $this->ocorrenciaAcademicaRepository->listar($parametros, $parametros[ConstanteParametros::CHAVE_PAGINA]);

        $retorno = [
            ConstanteParametros::CHAVE_TOTAL => $retornoRepositorio->getTotalItemCount(),
            ConstanteParametros::CHAVE_ITENS => $retornoRepositorio->getItems(),
        ];
        return $retorno;
    }

    /**
     * Busca a lista de ocorrências acadêmicas para o Funil de Vendas
     *
     * @param int $usuarioId
     * @param array $parametros Parametros da requisicao
     *
     * @return array|NULL
     */
    public function listaFunilVendas($usuarioId, $parametros=[])
    {
        return $this->ocorrenciaAcademicaRepository->buscaFunilVendas($usuarioId, $parametros);
    }

    /**
     * Busca a lista de ocorrências acadêmicas para o Funil de Vendas
     *
     * @param int $usuarioId
     * @param array $parametros Parametros da requisicao
     *
     * @return array|NULL
     */
    public function listaFunilVendasAtrasado($usuarioId, $parametros=[])
    {
        return $this->ocorrenciaAcademicaRepository->buscaFunilVendasAtrasado($usuarioId, $parametros);
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
        $objetoORM = $this->ocorrenciaAcademicaRepository->buscarRegistroPorId($id);
        if (empty($objetoORM) === true) {
            $mensagemErro = "Ocorrencia academica não encontrado na base de dados.";
        }

        return $objetoORM;
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

        if ($this->ocorrenciaAcademicaBO->podeCriar($parametros, $mensagemErro) === true) {
            if ((isset($parametros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO]) === false)) {
                $parametros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO] = new \DateTime($parametros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO]);

                if ((isset($parametros[ConstanteParametros::CHAVE_HORARIO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_HORARIO]) === false)) {
                    $horario = explode(":", $parametros[ConstanteParametros::CHAVE_HORARIO]);
                    $parametros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO]->setTime($horario[0], $horario[1]);
                }
            }

            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\OcorrenciaAcademica::class, true, $parametros);
            self::persistSeguro($objetoORM, $mensagemErro);
        }

        return $objetoORM;

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
        $ocorenciaAcademica = $this->ocorrenciaAcademicaRepository->find($id);
        if (is_null($ocorenciaAcademica) === true) {
            $mensagemErro = "Ocorrência acadêmica não encontrada na base de dados";
        } else {
            if ($this->ocorrenciaAcademicaBO->podeCriar($parametros, $mensagemErro) === true) {
                if ((isset($parametros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO]) === false)) {
                    $parametros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO] = new \DateTime($parametros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO]);

                    if ((isset($parametros[ConstanteParametros::CHAVE_HORARIO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_HORARIO]) === false)) {
                        $horario = explode(":", $parametros[ConstanteParametros::CHAVE_HORARIO]);
                        $parametros[ConstanteParametros::CHAVE_DATA_PROXIMO_CONTATO]->setTime($horario[0], $horario[1]);
                    }
                }

                self::getFctHelper()->setParams($parametros, $ocorenciaAcademica);
            }
        }

        return empty($mensagemErro);
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
        return empty($mensagemErro);
    }

    /**
     * Monta os parametros para gerar uma ocorrencia Academica já encerrada
     *
     * @param string $mensagemErro
     * @param int $franqueadaId
     * @param int $alunoId
     * @param int $usuarioId
     * @param int $itemId
     * @param string $obsevacaoOcorrencia
     * @param string $situacao
     *
     * @return array[]
     */
    public function gerarParametrosOcorrenciaAcademica(&$mensagemErro, $franqueadaId, $alunoId, $usuarioId, $itemId, $obsevacaoOcorrencia="", $situacao=SituacoesSistema::OCORRENCIA_ENCERRADA)
    {
        $retornaFuncionarioIdDoUsuario     = $this->retornaFuncionarioIdDoUsuario($usuarioId);
        $retornaTipoOcorrenciaIdDoTipoItem = $this->retornaTipoOcorrenciaIdDoTipoItem($itemId);
        if (is_null($retornaFuncionarioIdDoUsuario) === true) {
            $mensagemErro .= "Não foi encontrado um funcionario cadastrado para o usuario informado.\n";
        }

        if (is_null($retornaTipoOcorrenciaIdDoTipoItem) === true) {
            $mensagemErro .= "Não foi encontrado um tipo_ocorrencia cadastrado para o TipoItem informado.\n";
        }

        return [
            ConstanteParametros::CHAVE_FRANQUEADA      => $franqueadaId,
            ConstanteParametros::CHAVE_ALUNO           => $alunoId,
            ConstanteParametros::CHAVE_USUARIO         => $usuarioId,
            ConstanteParametros::CHAVE_FUNCIONARIO     => $retornaFuncionarioIdDoUsuario,
            ConstanteParametros::CHAVE_TIPO_OCORRENCIA => $retornaTipoOcorrenciaIdDoTipoItem,
            ConstanteParametros::CHAVE_DATA_CONCLUSAO  => new \DateTime(),
            ConstanteParametros::CHAVE_SITUACAO        => $situacao,
            ConstanteParametros::CHAVE_TEXTO           => $obsevacaoOcorrencia,
        ];
    }



    /**
     * Retorna as ocorrências do contrato passado conforme parametros
     *
     * @param string $mensagemErro
     * @param array $parametros
     *
     * @return array[]
     */
    public function getOcorrenciasContrato(&$mensagemErro, $parametros)
    {
        $ocorrencias = $this->ocorrenciaAcademicaRepository->getOcorrenciasContrato($parametros);
        return $ocorrencias;
    }

    public function buscarDadosRelatorioOcorrencias($parametros) 
    {
        return $this->ocorrenciaAcademicaRepository->gerarDadosRelatorioOcorrencias($parametros);
    }
}
