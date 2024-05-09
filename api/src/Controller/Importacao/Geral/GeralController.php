<?php

namespace App\Controller\Importacao\Geral;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Importacao\GeralImportacaoFacade;
use App\Helper\ConstanteParametros;

/**
 *
 * @author        Luiz Antonio Costa
 * @Route("/api")
 */
class GeralController extends GenericController
{
    /**
     *
     * @var \App\Facade\Importacao\GeralImportacaoFacade
     */
    private $geralImportacaoFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        parent::constroiFacades();
        $this->geralImportacaoFacade = new GeralImportacaoFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/importacao_tabela/importar",
     *     summary="Realiza a importacao de dados",
     *     description="Realiza a importacao de dados",
     *     tags={"Importacao de tabela"},
     *     consumes={"application/form-data"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="201",
     *         description="Retorna criado com sucesso"
     *     ),
     * @SWG\Response(
     *         response="202",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\FileParam(name="arquivo",                     requirements={"mimeTypes"={"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", "application/vnd.ms-excel"}}, strict=true)
     * @FOSRest\RequestParam(name="franqueada",               strict=true, nullable=false, allowBlank=false, description="ID da Franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="importacoes_selecionadas", strict=true, nullable=false, allowBlank=false, description="Array de Flags selecionadas", map=true)
     *
     * @FOSRest\Post("/importacao_tabela/importar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request)
    {
        $time_init      = microtime(true);
        $caminhoArquivo = $request->get("arquivo")->getPathName();
        $parametros     = $request->all();
        $mensagemErro   = "";
        $retorno        = $this->geralImportacaoFacade->importar($mensagemErro, $parametros, $parametros[ConstanteParametros::CHAVE_FRANQUEADA], $caminhoArquivo);
        if ($retorno === false) {
            return ResponseFactory::internalServerError(["Caminho arquivo" => $caminhoArquivo], "Ocorreu erro na hora de carregar o arquivo para importação.");
        }

        if (empty($mensagemErro) === false) {
            return ResponseFactory::created([ $mensagemErro], "Ocorreram erros durante a importação.");
        }

        $time_end = microtime(true);
        return ResponseFactory::created([], "Registros importados com sucesso!<br>Levou " . ($time_end - $time_init));
    }


}
