<?php

namespace App\Controller\Principal\Boleto;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\BoletoFacade;
use Symfony\Component\HttpFoundation\Response;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Jurosh\PDFMerge\PDFMerger;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class BoletoController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\BoletoFacade
     */
    private $boletoFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->boletoFacade = new BoletoFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/boleto/listar",
     *     summary="Listar boleto",
     *     description="Lista as boleto do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os boleto"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina",            strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     * @FOSRest\QueryParam(name="nosso_numero",      strict=false, nullable=true, description="Nosso numero")
     * @FOSRest\QueryParam(name="situacao_cobranca", strict=false, nullable=true, description="Situacao da cobranca")
     * @FOSRest\QueryParam(name="sacado",            strict=false, nullable=true, description="Pesssoa sacado para filtro")
     * @FOSRest\QueryParam(name="pessoa_aluno",      strict=false, nullable=true, description="Pesssoa do aluno para filtro")
     * @FOSRest\QueryParam(name="vencimento_de",     strict=false, nullable=true, description="Data de vencimento boleto de")
     * @FOSRest\QueryParam(name="vencimento_ate",    strict=false, nullable=true, description="Data de vencimento boleto ate")
     * @FOSRest\QueryParam(name="data_emissao_de",   strict=false, nullable=true, description="Data de emissão do titulo de")
     * @FOSRest\QueryParam(name="data_emissao_ate",  strict=false, nullable=true, description="Data de emissão do titulo ate")
     *
     * @FOSRest\Get("/boleto/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->boletoFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/boleto/imprimir/{id}",
     *     summary="Imprimir boleto do título passado por parametro",
     *     description="Imprime um boleto",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o boleto"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/boleto/imprimir/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function imprimir ($id)
    {
        $mensagem   = "";
        $boletoHTML = $this->boletoFacade->imprimirBoleto($id, $mensagem);

        if (empty($mensagem) === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        $pdf = $this->get('knp_snappy.pdf')->getOutputFromHtml($boletoHTML);
        return new PdfResponse($pdf, "boleto.pdf");
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/boleto/imprimir-boletos",
     *     summary="Imprimir os boletos passados por parametro",
     *     description="Imprimir os boletos passados por parametro",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o boleto"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/boleto/imprimir-boletos")
     *
     * @FOSRest\QueryParam(name="boletos", strict=true, allowBlank=false, description="Boletos a serem impressos", map=true)
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function imprimirBoletos (ParamFetcher $request)
    {
        $mensagem = "";
        $host     = '';
        $ids      = $request->get('boletos');
        if (($ids !== null) && (count($ids) > 0)) {
            $diretorioArquivo = $this->container->getParameter("kernel.project_dir") . "/public/boletos/";
            
            if (file_exists($diretorioArquivo) === false) {
                mkdir($diretorioArquivo, 0777, true);
            }
            
            $arquivos = [];
            $boletos  = new PDFMerger;
           
            foreach ($ids as $id) {
                $boletoHTML = $this->boletoFacade->imprimirBoleto($id, $mensagem, $host);

                if (empty($mensagem) === false) {
                    $response = new Response("<h2>Houve um erro</h2><p>$mensagem</p>");
                    $response->headers->set('Content-Type', 'text/html');
                    return $response;
                }
                
                //precisou ser feito aqui pois fazer igual fiz nas outras imagens do boleto, estava bugando o Boleto
                //Altera as linhas que usan o arquivo p.png e substitui prlo arquivo base64
                // Caminho para o arquivo de imagem   api/public/images/boleto/b.png
                $caminho_imagem_p = $this->container->getParameter("kernel.project_dir") . "/public/images/boleto/p.png";
                // Verifica se o arquivo existe
                if (file_exists($caminho_imagem_p)) {
                   // echo('achou');
                   // die;
                    // Lê o conteúdo do arquivo
                    $conteudo_imagem = file_get_contents($caminho_imagem_p);
                    
                    // Converte o conteúdo do arquivo para base64
                    $Base64p_image = base64_encode($conteudo_imagem);
                }                
                
                $boletoHTML = str_replace('src="http://' . $host . '/images/boleto/p.png"', 
                                          'src="data:image/png;base64,' . $Base64p_image . '"' , $boletoHTML);
                
              
                //Altera as linhas que usan o arquivo b.png e substitui prlo arquivo base64
                // Caminho para o arquivo de imagem
                $caminho_imagem_b = $this->container->getParameter("kernel.project_dir") . "/public/images/boleto/b.png";
                // Verifica se o arquivo existe
                if (file_exists($caminho_imagem_b)) {
                    // Lê o conteúdo do arquivo
                    $conteudo_imagem = file_get_contents($caminho_imagem_b);
                    
                    // Converte o conteúdo do arquivo para base64
                    $Base64b_image = base64_encode($conteudo_imagem);
                } 
                $boletoHTML = str_replace('src="http://' . $host . '/images/boleto/b.png"', 
                                          'src="data:image/png;base64,' . $Base64b_image . '"' , $boletoHTML);

              $publicDir  = '/app/public/';                
              $boletoHTML = str_replace('http://' . $host . '/', $publicDir, $boletoHTML);
                                          
            // Para testar no local, temos que dar um echo do HTML na tela
               // echo('234');
              //  echo $boletoHTML;
               // die();
                //  api/public/images/boleto/p.png
                $nomeArquivo            = uniqid();
                $caminhoCompletoArquivo = "$diretorioArquivo$nomeArquivo.pdf";

                $this->get('knp_snappy.pdf')->generateFromHtml(
                    $boletoHTML,
                    $caminhoCompletoArquivo,
                    ['page-size' => 'A4']
                );
                $boletos->addPDF($caminhoCompletoArquivo);
                $arquivos[] = $caminhoCompletoArquivo;
            }//end foreach

            $boletos->merge("download", "boletos.pdf");

            foreach ($arquivos as $arquivo) {
                unlink($arquivo);
            }
        } else {
            $response = new Response("<h2>Houve um erro:</h2><pre>Não foram passados as ID's do boleto para realizar a impressão.</pre>");
            $response->headers->set('Content-Type', 'text/html');
            return $response;
        }//end if
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/boleto/{id}",
     *     summary="Buscar a boleto",
     *     description="Busca a boleto através da ID",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a boleto"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/boleto/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagemErro = "";
        $objetoORM    = $this->boletoFacade->buscarPorId($mensagemErro, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagemErro);
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/boleto/criar",
     *     summary="Cria uma boleto",
     *     description="Cria uma boleto no banco",
     *     consumes={"application/x-www-form-urlencoded"},
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
     * @FOSRest\RequestParam(name="franqueada",     strict=true, nullable=false, description="ID da franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="conta",          strict=true, nullable=false, description="Conta", requirements="\d+")
     * @FOSRest\RequestParam(name="titulo_receber", strict=true, nullable=false, description="Titulo Receber", requirements="\d+")
     * @FOSRest\RequestParam(name="nosso_numero",   strict=true, nullable=false, description="Nosso numero", requirements="\d+")
     * @FOSRest\RequestParam(name="valor_desconto", strict=true, nullable=true, allowBlank=false, description="valor do desconto", requirements="^\d{0,7}+\.?\d{0,2}?$")
     *
     * @FOSRest\Post("/boleto/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $objetoORM  = $this->boletoFacade->criar($mensagem, $parametros);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::created(["objetoORM" => $objetoORM->getId()], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/boleto/atualizar/{id}",
     *     summary="Atualiza um boleto",
     *     description="Atualiza um boleto no banco",
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
     * @FOSRest\RequestParam(name="situacao_cobranca", strict=true, nullable=false, description="Situacao da cobranca", requirements="^.{0,3}")
     *
     * @FOSRest\Patch("/boleto/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $retorno    = $this->boletoFacade->atualizar($mensagem, $id, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/boleto/receber/{id}",
     *     summary="Atualiza um boleto",
     *     description="Atualiza um boleto no banco",
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
     * @FOSRest\RequestParam(name="situacao_cobranca", strict=true, nullable=false, description="Situacao da cobranca", requirements="^.{0,3}")
     *
     * @FOSRest\Patch("/boleto/receber/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function receber($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";

        /*
            $retorno    = $this->boletoFacade->receber($mensagem, $id, $parametros);
            if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
            }

            return ResponseFactory::noContent([]);
        */

        dd('Em produção');
    }


}
