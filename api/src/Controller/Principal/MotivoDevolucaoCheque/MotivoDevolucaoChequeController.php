<?php

namespace App\Controller\Principal\MotivoDevolucaoCheque;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\MotivoDevolucaoChequeFacade;

/**
 *
 * @author        Rodrigo de Souza Fernandes (GATI labs)
 * @Route("/api")
 */
class MotivoDevolucaoChequeController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\MotivoDevolucaoChequeFacade
     */
    private $motivoDevolucaoChequeFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->motivoDevolucaoChequeFacade = new MotivoDevolucaoChequeFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/motivo_devolucao_cheque/listar",
     *     summary="Listar motivo_devolucao_cheque",
     *     description="Lista os motivo_devolucao_cheque do banco",
     *     tags={"Motivo Devolucao Cheque"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os motivo_devolucao_cheque"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina",         strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d{0,2}")
     * @FOSRest\QueryParam(name="itensPorPagina", strict=false, allowBlank=false, default="50", description="Quantidade de itens a ser exibido", requirements="\d{0,2}")
     *
     * @FOSRest\Get("/motivo_devolucao_cheque/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->motivoDevolucaoChequeFacade->listar($parametros);
        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/motivo_devolucao_cheque/{id}",
     *     summary="Buscar o Motivo Devolução do Cheque",
     *     description="Busca o Motivo Devolução do Cheque através da ID",
     *     tags={"Motivo Devolucao Cheque"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o Motivo Devolução do Cheque"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/motivo_devolucao_cheque/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagem  = "";
        $objetoORM = $this->motivoDevolucaoChequeFacade->buscarPorId($mensagem, $id);
        if (is_null($objetoORM) === true)
            return ResponseFactory::notFound([], "Motivo Devolução do Cheque não encontrada.");
        return ResponseFactory::ok($objetoORM);
    }


}
