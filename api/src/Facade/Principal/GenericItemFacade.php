<?php

namespace App\Facade\Principal;


use App\BO\Principal\GenericItemBO;
use App\BO\Principal\ItemFranqueadaBO;
use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\VariaveisCompartilhadas;

/**
 *
 * @author Luiz A Costa
 */
class GenericItemFacade extends GenericFacade
{
    /**
     *
     * @var \App\BO\Principal\GenericItemBO
     */
    protected $itemBO;

    /**
     *
     * @var \App\Repository\Principal\ItemRepository
     */
    private $itemRepository;

    /**
     *
     * @var \App\Repository\Principal\TipoItemRepository
     */
    private $tipoItemRepository;

    /**
     *
     * @var \App\Repository\Principal\UsuarioRepository
     */
    private $usuarioRepository;

    /**
     *
     * @var \App\Repository\Principal\ItemFranqueadaRepository
     */
    private $itemFranqueadaRepository;

    /**
     *
     * @var \App\BO\Principal\ItemFranqueadaBO
     */
    private $itemFranqueadaBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->itemBO         = new GenericItemBO(self::getEntityManager(), "");
        $this->itemRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\Item::class);
        $this->tipoItemRepository       = self::getEntityManager()->getRepository(\App\Entity\Principal\TipoItem::class);
        $this->usuarioRepository        = self::getEntityManager()->getRepository(\App\Entity\Principal\Usuario::class);
        $this->itemFranqueadaBO         = new ItemFranqueadaBO(self::getEntityManager());
        $this->itemFranqueadaRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\ItemFranqueada::class);
    }

    /**
     * Monta o log do item
     *
     * @param \App\Entity\Principal\Item $itemORM
     * @param string $log
     */
    private function montaLogItem($itemORM, &$log)
    {
        $log .= "ID: " . $itemORM->getId() . "\n";
        $log .= "Descrição: " . $itemORM->getDescricao() . "\n";
        $log .= "Narrativa: " . $itemORM->getNarrativa() . "\n";
        $log .= "Situação: " . $itemORM->getSituacao() . "\n";
    }

    /**
     * Cria Log de alteração do item
     *
     * @param \App\Entity\Principal\Item $itemORM
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return \App\Entity\Principal\HistoricoItem
     */
    protected function criarLogAlteracao($itemORM, $parametros, &$mensagemErro)
    {
        $usuarioLogado = $this->usuarioRepository->find(VariaveisCompartilhadas::$usuarioID);
        $historicoItem = new \App\Entity\Principal\HistoricoItem();
        $historicoItem->setFranqueada($itemORM->getFranqueada());
        $historicoItem->setUsuario($usuarioLogado);
        $historicoItem->setLog("");
        self::persistSeguro($historicoItem, $mensagemErro);
        return $historicoItem;
    }

    /**
     * Lista todos os registros do banco de dados
     *
     * @param array $parametros Parametros da requisicao
     *
     * @return array
     */
    public function listaItensContrato($parametros)
    {
        $retornoRepositorio = $this->itemRepository->filtrarItemsContratoPorPagina($parametros, $parametros[ConstanteParametros::CHAVE_PAGINA]);
        $retorno            = [
            ConstanteParametros::CHAVE_TOTAL => $retornoRepositorio->getTotalItemCount(),
            ConstanteParametros::CHAVE_ITENS => $retornoRepositorio->getItems(),
        ];
        return $retorno;
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
        $retornoRepositorio = $this->itemRepository->filtrarItemsPorPagina($parametros, $parametros[ConstanteParametros::CHAVE_PAGINA]);
        $retorno            = [
            ConstanteParametros::CHAVE_TOTAL => $retornoRepositorio->getTotalItemCount(),
            ConstanteParametros::CHAVE_ITENS => $retornoRepositorio->getItems(),
        ];
        return $retorno;
    }

     /**
     * Lista todos os registros do banco de dados
     *
     * @param array $parametros Parametros da requisicao
     *
     * @return array
     */
    public function listar_para_contrato($franquiaId)
    {
        $itens = $this->itemRepository->listarSimplesContrato($franquiaId);
        $retorno            = [
            ConstanteParametros::CHAVE_TOTAL => 9999,
            ConstanteParametros::CHAVE_ITENS => $itens,
        ];
        return $retorno;
    }

    /**
     * Busca o registro pela chave primaria
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id Chave primaria do registro
     *
     * @return null|\App\Entity\Principal\Item
     */
    public function buscarPorId(&$mensagemErro, $id)
    {
        $objetoORM = null;
        $this->itemBO->verificaItemExistePorId($id, $mensagemErro, $objetoORM, $this->itemRepository);

        return $objetoORM;
    }

    /**
     * Busca registros de item com determinada descricao
     *
     * @param string $descricao descrição a ser buscada
     * @param integer $franqueada
     * @param array $tipoItem
     *
     * @return \App\Entity\Principal\Item[]
     */
    public function buscarPorDescricao ($descricao, $franqueada, $tipoItem)
    {
        return $this->itemRepository->buscarPorDescricao($descricao, $franqueada, $tipoItem);
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return null|\App\Entity\Principal\Item
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $objetoORM = null;
        if ($this->itemBO->podeSalvar($parametros, $mensagemErro) === true) {
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\Item::class, true, $parametros);
            self::persistSeguro($objetoORM, $mensagemErro);
            $itemFranqueada = $objetoORM->getItemFranqueadas()->get(0);

            if ((is_null($itemFranqueada) === true)) {
                $parametrosItemFranqueada = $parametros[ConstanteParametros::CHAVE_ITEM_FRANQUEADAS][0];
            } else {
                $parametrosItemFranqueada = [];
            }

            $parametrosItemFranqueada[ConstanteParametros::CHAVE_FILTRO_FRANQUEADA] = VariaveisCompartilhadas::$franqueadaID;

            $retorno = $this->preencheItemFranqueada($mensagemErro, $objetoORM, $parametrosItemFranqueada);
            if ($retorno === true) {
                self::flushSeguro($mensagemErro);
            }
        }

        if (empty($mensagemErro) === false) {
            return null;
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
        $objetoORM = $this->itemRepository->find($id);

        if (is_null($objetoORM) === true) {
            $mensagemErro = "Item não encontrado na base de dados.";
        } else {
            if ($this->itemBO->podeAlterar($parametros, $mensagemErro) === true) {
                $historicoItemORM = $this->criarLogAlteracao($objetoORM, $parametros, $mensagemErro);
                $log  = "O usuario: '" . $historicoItemORM->getUsuario()->getNome() . "' alterou o item de id: '" . $objetoORM->getId() . "', descrição: '" . $objetoORM->getDescricao() . "'.\n";
                $log .= "Item antes da alteração:\n";
                $this->montaLogItem($objetoORM, $log);
                self::getFctHelper()->setParams($parametros, $objetoORM);
                $log .= "Item após da alteração:\n";
                $this->montaLogItem($objetoORM, $log);
                $historicoItemORM->setLog($log);

                if ((isset($parametros[ConstanteParametros::CHAVE_ITEM_FRANQUEADAS]) === true)&&(is_array($parametros[ConstanteParametros::CHAVE_ITEM_FRANQUEADAS]) === true) && ((count($parametros[ConstanteParametros::CHAVE_ITEM_FRANQUEADAS]) > 0) === true)) {
                    $parametrosItemFranqueada = $parametros[ConstanteParametros::CHAVE_ITEM_FRANQUEADAS][0];
                }

                $parametrosItemFranqueada[ConstanteParametros::CHAVE_FILTRO_FRANQUEADA] = VariaveisCompartilhadas::$franqueadaID;

                $retorno = $this->preencheItemFranqueada($mensagemErro, $objetoORM, $parametrosItemFranqueada);
                if ($retorno === true) {
                    self::flushSeguro($mensagemErro);
                }
            }//end if
        }//end if

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
        $objetoORM = null;
        if ($this->itemBO->verificaItemExistePorId($id, $mensagemErro, $objetoORM) === true) {
            $objetoORM->setSituacao(SituacoesSistema::SITUACAO_INATIVO);
            self::flushSeguro($mensagemErro);
        }

        return empty($mensagemErro);
    }

    /**
     * Retorna o primeiro item para o tipoItem informado
     *
     * @param string $tipoItem
     *
     * @return \App\Entity\Principal\Item|NULL
     */
    public function retornaPrimeiroItemPorTipoItem($tipoItem)
    {
        $tipoItemORM = $this->tipoItemRepository->findOneBy([ConstanteParametros::CHAVE_TIPO => $tipoItem]);
        if (is_null($tipoItemORM) === false) {
            $itemsORM = $tipoItemORM->getItems();
            return $itemsORM->get(0);
        }

        return null;
    }

    /**
     * Gera as informações para a seleção de registros do relatório.
     *
     * @param array  $parametros
     *
     * @return string
     */
    public function gerarDadosRelatorio($parametros)
    {
        return $this->itemRepository->prepararDadosRelatorio($parametros);
    }

    /**
     * Atualiza o valor de venda de um item
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param mixed $idOuObjeto Chave primaria do registro
     * @param array $parametros Campos e valores que iram ser atualizados
     *
     * @return boolean
     */
    public function preencheItemFranqueada(&$mensagemErro, $idOuObjeto, $parametros=[])
    {
        $id = $idOuObjeto;
        if (is_object($idOuObjeto) === true) {
            $id = $idOuObjeto->getId();
        }

        $objetoORM = $this->itemFranqueadaRepository->findOneBy([ 'item' => $id, 'franqueada' => $parametros[ConstanteParametros::CHAVE_FILTRO_FRANQUEADA] ]);

        if (is_null($objetoORM) === true) {
            $parametros[ConstanteParametros::CHAVE_ITEM] = $idOuObjeto;

            if ($this->itemFranqueadaBO->podeSalvar($parametros, $mensagemErro) === true) {
                $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\ItemFranqueada::class, true, $parametros);
                self::criarRegistro($objetoORM, $mensagemErro);
            } else {
                $mensagemErro = "Houve um erro ao atualizar o valor de venda";
            }
        } else {
            $parametros[ConstanteParametros::CHAVE_ITEM] = $idOuObjeto;

            if ($this->itemFranqueadaBO->podeSalvar($parametros, $mensagemErro) === true) {
                self::getFctHelper()->setParams($parametros, $objetoORM);
                self::flushSeguro($mensagemErro);
            } else {
                $mensagemErro = "Houve um erro ao atualizar o valor de venda";
            }
        }

        return empty($mensagemErro);
    }

    public function gerarDadosRelatorioEstoque($filtros)
    {
        return $this->itemRepository->buscarDadosRelatorioEstoque($filtros);
    }
    
    public function buscarDadosRelatorioItensDeEstoque($filtros)
    {
        return $this->itemRepository->gerarDadosRelatorioItensDeEstoque($filtros);
    }

    public function buscarDadosRelatorioServicosSolicitados($parametros)
    {
        return $this->itemRepository->gerarDadosRelatorioServicosSolicitados($parametros);
    }

    public function fetchPairsItem($filtros, $query = null)
    {
        return $this->itemRepository->fetchPairsItem($filtros, $query);
    }
}
