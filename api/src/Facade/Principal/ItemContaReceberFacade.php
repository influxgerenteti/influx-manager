<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\BO\Principal\ParametrosFranqueadoraBO;
use App\Helper\ConstanteParametros;
use App\BO\Principal\ItemContaReceberBO;
use App\Helper\SituacoesSistema;

/**
 *
 * @author Luiz A Costa
 */
class ItemContaReceberFacade extends GenericFacade
{

    /**
     *
     * @var \App\BO\Principal\ParametrosFranqueadoraBO
     */
    private $parametrosFranqueadoraBO;

    /**
     *
     * @var \App\BO\Principal\ItemContaReceberBO
     */
    private $itemContaReceberBO;

    /**
     *
     * @var \App\Facade\Principal\MovimentoEstoqueFacade
     */
    private $movimentoEstoqueFacade;

    /**
     *
     * @var \App\Repository\Principal\ItemContaReceberRepository
     */
    private $itemContaReceberRepository;

    /**
     *
     * @var \App\Repository\Principal\UsuarioRepository
     */
    private $usuarioRepository;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->parametrosFranqueadoraBO   = new ParametrosFranqueadoraBO(self::getEntityManager());
        $this->itemContaReceberBO         = new ItemContaReceberBO(self::getEntityManager());
        $this->itemContaReceberRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\ItemContaReceber::class);
        $this->usuarioRepository          = self::getEntityManager()->getRepository(\App\Entity\Principal\Usuario::class);
        $this->movimentoEstoqueFacade     = new MovimentoEstoqueFacade($managerRegistry);
    }

    /**
     * Cria um registro na tabela de historico
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return bool
     */
    private function criarHistorico($parametros, &$mensagemErro)
    {
        $usuarioAutorizouORM = null;
        if ((isset($parametros[ConstanteParametros::CHAVE_USUARIO_AUTORIZOU]) === true) && (empty($parametros[ConstanteParametros::CHAVE_USUARIO_AUTORIZOU]) === false)) {
            $usuarioAutorizouORM = $this->usuarioRepository->find($parametros[ConstanteParametros::CHAVE_USUARIO_AUTORIZOU]);
        }

        $historicoEntrega = new \App\Entity\Principal\HistoricoEntregaItem();
        $historicoEntrega->setUsuarioLogado($parametros[ConstanteParametros::CHAVE_USUARIO]);
        $historicoEntrega->setDataAcontecimento(new \DateTime());
        $historicoEntrega->setSituacao($parametros[ConstanteParametros::CHAVE_SITUACAO_ENTREGA]);

        if (is_null($usuarioAutorizouORM) === false) {
            $historicoEntrega->setUsuarioAutorizou($usuarioAutorizouORM);
        }

        self::persistSeguro($historicoEntrega, $mensagemErro);

        return empty($mensagemErro) === true;
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
        $retornoRepositorio = $this->itemContaReceberRepository->filtrarItemContaReceberPorPagina($parametros, $parametros[ConstanteParametros::CHAVE_PAGINA]);
        $retorno            = [
            ConstanteParametros::CHAVE_TOTAL => $retornoRepositorio->getTotalItemCount(),
            ConstanteParametros::CHAVE_ITENS => $retornoRepositorio->getItems(),
        ];
        return $retorno;
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return mixed|null|\App\Entity\Principal\ItemContaReceber
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $objetoORM = null;
        if ($this->itemContaReceberBO->podeCriar($parametros, $mensagemErro) === true) {
            if ((isset($parametros[ConstanteParametros::CHAVE_PERCENTUAL_DESCONTO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_PERCENTUAL_DESCONTO]) === true)) {
                $parametros[ConstanteParametros::CHAVE_PERCENTUAL_DESCONTO] = 0;
            }

            if ((isset($parametros[ConstanteParametros::CHAVE_VALOR_DESCONTO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_VALOR_DESCONTO]) === true)) {
                $parametros[ConstanteParametros::CHAVE_VALOR_DESCONTO] = 0;
            }

            if ($this->itemContaReceberBO->configuraDataVencimento($parametros, $mensagemErro) === true) {
                $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\ItemContaReceber::class, true, $parametros);
                self::persistSeguro($objetoORM, $mensagemErro);
            }
        }

        return $objetoORM;
    }

    /**
     * Atualiza a entrega do item
     *
     * @param string $mensagemErro
     * @param int $id
     * @param array $parametros
     * @param boolean $efetuarFlush
     *
     * @return bool
     */
    public function atualizarEntrega(&$mensagemErro, $id, $parametros=[], $efetuarFlush=true)
    {
        $objetoORM = $this->itemContaReceberRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "ItemContaReceber não encontrado na base de dados.";
        } else {
            if ($this->itemContaReceberBO->podeAtualizar($parametros, $mensagemErro) === true) {
                $situacaoAnterior = $objetoORM->getSituacaoEntrega();
                $objetoORM->setSituacaoEntrega($parametros[ConstanteParametros::CHAVE_SITUACAO_ENTREGA]);

                switch ($objetoORM->getSituacaoEntrega()) {
                    case SituacoesSistema::ITEM_ENTREGUE:
                    if ($this->itemContaReceberBO->configuraDataEntrega($parametros, $mensagemErro) === true) {
                        $objetoORM->setDataEntrega($parametros[ConstanteParametros::CHAVE_DATA_ENTREGA]);
                        $objetoORM->setUsuarioEntregue($parametros[ConstanteParametros::CHAVE_USUARIO]);

                        $movimentoEstoqueORM = $this->movimentoEstoqueFacade->criar($mensagemErro, $objetoORM);
                        if ((is_null($movimentoEstoqueORM) === true) || (empty($mensagemErro) === false)) {
                            return false;
                        }
                    }
                        break;
                    case SituacoesSistema::ITEM_CANCELADO:
                    if ($situacaoAnterior !== SituacoesSistema::ITEM_NAO_ENTREGUE) {
                        $movimentoEstoqueORM = $this->movimentoEstoqueFacade->criar($mensagemErro, $objetoORM, false);
                        if ((is_null($movimentoEstoqueORM) === true) || (empty($mensagemErro) === false)) {
                            return false;
                        }
                    }
                        break;
                }//end switch

                $this->criarHistorico($parametros, $mensagemErro);

                if ($efetuarFlush === true) {
                    self::flushSeguro($mensagemErro);
                }
            }//end if
        }//end if

        return empty($mensagemErro);
    }

    public function montarRelatorioControleMaterialDidatico($parametros)
    {
        return $this->itemContaReceberRepository->buscarRelatorioControleMaterialDidatico($parametros);
    }

    public function gerarDadosRelatorioPedidoMaterialDidatico($filtros) {
        return $this->itemContaReceberRepository->obterRelatorioPedidoMaterialDidatico($filtros);
    }
}
