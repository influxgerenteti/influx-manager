<?php

namespace App\Controller\Principal\ItemContaReceber;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Helper\ConstanteParametros;
use Symfony\Component\HttpFoundation\Request;
use App\Facade\Principal\ItemContaReceberFacade;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class ItemContaReceberController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\ItemContaReceberFacade
     */
    private $itemContaReceberFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->itemContaReceberFacade = new ItemContaReceberFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/item_conta_receber/listar",
     *     summary="Listar item_conta_receber",
     *     description="Lista as item_conta_receber do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os item_conta_receber"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina",              strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     * @FOSRest\QueryParam(name="franqueada",          strict=true, nullable=false, description="ID da franqueada", requirements="\d+")
     * @FOSRest\QueryParam(name="aluno",               strict=false, nullable=false, description="Aluno ID", requirements="\d+")
     * @FOSRest\QueryParam(name="item",                strict=false, nullable=false, description="Item ID", requirements="\d+")
     * @FOSRest\QueryParam(name="usuario",             strict=false, nullable=false, description="Usuario ID", requirements="\d+")
     * @FOSRest\QueryParam(name="valor_inicial",       strict=false, nullable=true, description="Valor Inicial", requirements="^\d{0,7}+\.?\d{0,2}?$")
     * @FOSRest\QueryParam(name="valor_fim",           strict=false, nullable=true, description="Valor Final", requirements="^\d{0,7}+\.?\d{0,2}?$")
     * @FOSRest\QueryParam(name="data_aula_inicio",    strict=false, nullable=true, description="Data Inicial da Aula")
     * @FOSRest\QueryParam(name="data_aula_fim",       strict=false, nullable=true, description="Data Final da Aula")
     * @FOSRest\QueryParam(name="data_saida_inicio",   strict=false, nullable=true, description="Data Inicial de saida")
     * @FOSRest\QueryParam(name="data_saida_fim",      strict=false, nullable=true, description="Data Final de saida")
     * @FOSRest\QueryParam(name="data_entrega_inicio", strict=false, nullable=true, description="Data Inicial de entrega")
     * @FOSRest\QueryParam(name="data_entrega_fim",    strict=false, nullable=true, description="Data Final de entrega")
     * @FOSRest\QueryParam(name="item_entregue",       strict=false, nullable=true, description="Situacao", map=true)
     * @FOSRest\QueryParam(name="modalidade_turma",    strict=false, nullable=true, allowBlank=false, description="Modalidade da turma", requirements="\d+")
     * @FOSRest\QueryParam(name="turma",               strict=false, nullable=false, allowBlank=false, description="Turma", requirements="\d+")
     *
     * @FOSRest\Get("/item_conta_receber/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->itemContaReceberFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }



    /**
     *
     * @SWG\Post(
     *     path="/api/item_conta_receber/entrega_material",
     *     summary="Atualiza um conta_receber",
     *     description="Atualiza um conta_receber no banco",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="204",
     *         description="Retorna atualizado com sucesso"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\RequestParam(name="usuario",             strict=false, nullable=true, allowBlank=true, description="Usuario ID", requirements="\d+")
     * @FOSRest\RequestParam(name="usuario_autorizacao", strict=false, nullable=true, allowBlank=true, description="Usuario ID Que autorizou", requirements="\d+")
     * @FOSRest\RequestParam(name="situacao_entrega",    strict=true, nullable=false, allowBlank=false, description="Situação da entrega", requirements="(E|N|C)")
     * @FOSRest\RequestParam(name="data_entrega",        strict=false, nullable=true, allowBlank=true, description="Data de Entrega")
     *
     * @FOSRest\RequestParam(name="lista_id", strict=true, nullable=false, allowBlank=false, description="Lista de ID", requirements="\d+", map=true)
     *
     * @FOSRest\Post("/item_conta_receber/entrega_material")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizarEntregaMaterial(ParamFetcher $request, Request $requestHeader)
    {
        $parametros = $request->all();
        $mensagem   = "";

        if ((isset($parametros[ConstanteParametros::CHAVE_USUARIO]) === false)&&(empty($parametros[ConstanteParametros::CHAVE_USUARIO]) === true)) {
            $parametros[ConstanteParametros::CHAVE_USUARIO] = $requestHeader->headers->get('Authorization-User-ID');
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_LISTA_ID]) === true)&&(count($parametros[ConstanteParametros::CHAVE_LISTA_ID]) > 0)) {
            $bEncontrouErro = false;
            $listaIds       = $parametros[ConstanteParametros::CHAVE_LISTA_ID];
            unset($parametros[ConstanteParametros::CHAVE_LISTA_ID]);
            foreach ($listaIds as $idItemContaReceber) {
                $retorno = $this->itemContaReceberFacade->atualizarEntrega($mensagem, $idItemContaReceber, $parametros, false);
                if ($retorno === false) {
                    $bEncontrouErro = true;
                    break;
                }
            }

            if ($bEncontrouErro === false) {
                self::getEntityManager()->flush();
            }

            if ($bEncontrouErro === true) {
                return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
            }
        }

        return ResponseFactory::noContent([]);
    }

}
