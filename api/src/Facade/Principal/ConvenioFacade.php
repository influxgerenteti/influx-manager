<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\ConstanteParametros;
use App\BO\Principal\ConvenioBO;

/**
 *
 * @author Luiz A Costa
 */
class ConvenioFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\ConvenioRepository
     */
    private $convenioRepository;

    /**
     *
     * @var \App\BO\Principal\ConvenioBO
     */
    private $convenioBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->convenioRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\Convenio::class);
        $this->convenioBO         = new ConvenioBO(self::getEntityManager());
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
        $retornoRepositorio = $this->convenioRepository->filtrarConvenioPorPagina($parametros, $parametros[ConstanteParametros::CHAVE_PAGINA]);
        $retorno            = [
            ConstanteParametros::CHAVE_TOTAL => $retornoRepositorio->getTotalItemCount(),
            ConstanteParametros::CHAVE_ITENS => $retornoRepositorio->getItems(),
        ];
        return $retorno;
    }

    /**
     * Lista os followups
     *
     * @param array $parametros
     *
     * @return array
     */
    public function listarFollowup($parametros)
    {
        $retornoRepositorio = $this->convenioRepository->filtrarConvenioFollowup($parametros);
        $retorno            = [
            ConstanteParametros::CHAVE_TOTAL => $retornoRepositorio->getTotalItemCount(),
            ConstanteParametros::CHAVE_ITENS => $retornoRepositorio->getItems(),
        ];
        return $retorno;
    }

    /**
     * Lista Convenios nacionais
     *
     * @param array $parametros Parametros da requisicao
     *
     * @return array
     */
    public function listaConveniosNacionais($parametros)
    {
        $retornoRepositorio = $this->convenioRepository->filtrarConvenioNacionaisAtivosPorPagina($parametros, $parametros[ConstanteParametros::CHAVE_PAGINA]);
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
    public function buscarPorId(&$mensagemErro, $id, $parametros)
    {
        $objetoORM = $this->convenioRepository->buscarRegistroPorId($id, $parametros);
        if (empty($objetoORM) === true) {
            $mensagemErro = "Convenio não encontrado na base de dados.";
        }

        return $objetoORM;
    }

    /**
     * Busca registros de convenios
     *
     * @param string $nome Nome da pessoa a ser buscado
     *
     * @return \App\Entity\Principal\Convenio[]
     */
    public function buscarEmpresaPorNome ($nome)
    {
        return $this->convenioRepository->buscarEmpresaPorNome($nome);
    }

    /**
     * Busca registros de convênios por nome
     *
     * @param string $query Nome a ser buscado
     *
     * @return array
     */
    public function buscarPorNome($query)
    {
        return $this->convenioRepository->buscarPorNome($query);
    }

    /**
     * Busca registros de convênios ativos por nome
     *
     * @param string $query Nome a ser buscado
     * @param array $parametros
     *
     * @return array
     */
    public function buscarAtivosPorNome($query, $parametros)
    {
        return $this->convenioRepository->buscarAtivosPorNome($query, $parametros);
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $usuarioId Usuario id
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return mixed|null|Object
     */
    public function criar(&$mensagemErro, $usuarioId, $parametros=[])
    {
        $objetoORM         = null;
        $usuarioORM        = null;
        $bEncontrouUsuario = $this->convenioBO->verificaUsuarioExisteBO([ConstanteParametros::CHAVE_USUARIO => $usuarioId], $mensagemErro, $usuarioORM);
        $bEncontrouPessoaConveniada = is_null($this->convenioRepository->findOneBy(["pessoa" => $parametros[ConstanteParametros::CHAVE_PESSOA]])) === false;
        if ($bEncontrouUsuario === false) {
            $mensagemErro = "Usuario não encontrado na base de dados.";
        } else if ($bEncontrouPessoaConveniada === true) {
            $mensagemErro = "Empresa já se encontra cadastrada no sistema.";
        } else {
            if ($this->convenioBO->podeCriar($parametros, $mensagemErro) === true) {
                $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\Convenio::class);
                if ($this->convenioBO->configuraParametros($objetoORM, $parametros[ConstanteParametros::CHAVE_FRANQUEADA], $usuarioORM, $parametros, $mensagemErro) === true) {
                    self::criarRegistro($objetoORM, $mensagemErro);
                } else {
                    $objetoORM = null;
                }
            }
        }

        return $objetoORM;
    }

    /**
     * Atualiza um registro no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id Chave primaria do registro
     * @param int $usuarioId usuario logado
     * @param array $parametros Campos e valores que iram ser atualizados
     *
     * @return boolean
     */
    public function atualizar(&$mensagemErro, $id, $usuarioId, $parametros=[])
    {
        $objetoORM     = $this->convenioRepository->find($id);
        $usuarioORM    = null;
        $franqueadaOBJ = null;

        $bEncontrouFranqueada = $this->convenioBO->verificaFranqueadaExisteBO($parametros, $mensagemErro, $franqueadaOBJ);
        $bEncontrouUsuario    = $this->convenioBO->verificaUsuarioExisteBO([ConstanteParametros::CHAVE_USUARIO => $usuarioId], $mensagemErro, $usuarioORM);

        if (is_null($objetoORM) === true) {
            $mensagemErro = "Convenio não encontrado na base de dados.";
        } else if ($bEncontrouUsuario === false) {
            $mensagemErro = "Usuario não encontrado na base de dados.";
        } else if ($bEncontrouFranqueada === false) {
            $mensagemErro = "Franqueada não encontrado na base de dados.";
        } else {
            if (($bEncontrouFranqueada === true) && ($bEncontrouUsuario === true)) {
                if (($objetoORM->getFranqueada()->getId() === $franqueadaOBJ->getId()) || ($usuarioORM->isUsuarioPertenceFranqueadora() === true)) {
                    if ($this->convenioBO->podeAtualizar($parametros, $mensagemErro) === true) {
                        $franqueadaID = $parametros[ConstanteParametros::CHAVE_FRANQUEADA];
                        unset($parametros[ConstanteParametros::CHAVE_FRANQUEADA]);
                        if ($this->convenioBO->configuraParametros($objetoORM, $franqueadaID, $usuarioORM, $parametros, $mensagemErro) === true) {
                            self::flushSeguro($mensagemErro);
                        }
                    } else {
                        $mensagemErro .= "O Registro não pode ser alterado, pois não pertence a franqueada selecionada.";
                    }
                }
            }
        }//end if

        return empty($mensagemErro);
    }

    public function followup(&$mensagemErro, $id, $usuarioId, $parametros=[])
    {
        $objetoORM            = $this->convenioRepository->find($id);
        $usuarioORM           = null;
        $franqueadaOBJ        = null;
        $bEncontrouFranqueada = $this->convenioBO->verificaFranqueadaExisteBO($parametros, $mensagemErro, $franqueadaOBJ);
        $bEncontrouUsuario    = $this->convenioBO->verificaUsuarioExisteBO([ConstanteParametros::CHAVE_USUARIO => $usuarioId], $mensagemErro, $usuarioORM);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Convenio não encontrado na base de dados.";
        } else if ($bEncontrouUsuario === false) {
            $mensagemErro = "Usuario não encontrado na base de dados.";
        } else if ($bEncontrouFranqueada === false) {
            $mensagemErro = "Franqueada não encontrado na base de dados.";
        } else {
            if (($bEncontrouFranqueada === true) && ($bEncontrouUsuario === true)) {
                if ($this->convenioBO->podeAdicionarFollowup($parametros, $mensagemErro) === true) {
                    $franqueadaID = $parametros[ConstanteParametros::CHAVE_FRANQUEADA];
                    unset($parametros[ConstanteParametros::CHAVE_FRANQUEADA]);
                    if ($this->convenioBO->configuraParametros($objetoORM, $franqueadaID, $usuarioORM, $parametros, $mensagemErro) === true) {
                        self::flushSeguro($mensagemErro);
                    }
                }
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
     * Inativa um convenio no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para o front-end
     * @param int $id Chave primaria do registro
     *
     * @return boolean
     */
    public function inativar(&$mensagemErro, $id)
    {
        $convenioORM = $this->convenioRepository->find($id);
        if (empty($convenioORM) === true) {
            $mensagemErro = "Convenio não encontrado na base de dados.";
            return false;
        }

        $convenioORM->setSituacao('I');

        self::flushSeguro($mensagemErro);

        return $convenioORM;
    }

    /**
     * Busca a lista de convenios para o Funil de Vendas
     *
     * @param int $usuarioId
     * @param array $parametros
     *
     * @return array|NULL
     */
    public function listaFunilVendas($usuarioId, $parametros=[])
    {
        return $this->convenioRepository->buscaFunilVendas($usuarioId, $parametros);
    }

    /**
     * Busca a lista de convenios para o Funil de Vendas
     *
     * @param int $usuarioId
     * @param array $parametros
     *
     * @return array|NULL
     */
    public function listaFunilVendasAtrasado($usuarioId, $parametros=[])
    {
        return $this->convenioRepository->buscaFunilVendasAtrasado($usuarioId, $parametros);
    }

    public function buscarDadosRelatorioNegociacaoConvenios($filtros)
    {
        return $this->convenioRepository->gerarDadosRelatorioNegociacaoConvenios($filtros);
    }

}
