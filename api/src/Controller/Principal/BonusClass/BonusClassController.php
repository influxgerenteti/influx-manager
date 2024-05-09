<?php

namespace App\Controller\Principal\BonusClass;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use App\Facade\Principal\BonusClassFacade;
use App\Facade\Principal\AlunosBonusClassFacade;
use App\Helper\ConstanteParametros;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\Request;


/**
 *
 * @author        Dayan Fretias
 * @Route("/api")
 */
class BonusClassController extends GenericController
{
    /**
     *
     * @var \App\Facade\Principal\BonusClassFacade
     */
    private $bonusClassFacade;

    /**
     *
     * @var \App\Facade\Principal\AlunosBonusClassFacade
     */
    private $alunosBonusClassFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->bonusClassFacade       = new BonusClassFacade(self::getManagerRegistry());
        $this->alunosBonusClassFacade = new AlunosBonusClassFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/bonus_class/listar",
     *     summary="Listar bonus_class",
     *     description="Lista as bonus_class do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os bonus_class"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina",               strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     * @FOSRest\QueryParam(name="franqueada",           strict=true, nullable=false, description="ID da franqueada", requirements="\d+")
     * @FOSRest\QueryParam(name="funcionario",          strict=false, nullable=true, description="ID do funcionario", requirements="\d+")
     * @FOSRest\QueryParam(name="situacao",             strict=false, nullable=true, description="situação")
     * @FOSRest\QueryParam(name="data_agendamento_de",  strict=false, nullable=true, description="filtro data agendamento de")
     * @FOSRest\QueryParam(name="data_agendamento_ate", strict=false, nullable=true, description="filtro data agendamento ate")
     *
     * @FOSRest\Get("/bonus_class/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->bonusClassFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/bonus_class/{id}",
     *     summary="Buscar a bonus_class",
     *     description="Busca a bonus_class através da ID",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a bonus_class"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/bonus_class/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagemErro = "";
        $objetoORM    = $this->bonusClassFacade->buscarPorId($mensagemErro, $id);
        ;
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], "OBJETO ORM não encontrada.");
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/bonus_class/criar",
     *     summary="Cria uma bonus_class",
     *     description="Cria uma bonus_class no banco",
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
     * @FOSRest\RequestParam(name="funcionario",     strict=false, nullable=true, allowBlank=true, description="Id do funcionario", requirements="\d+")
     * @FOSRest\RequestParam(name="sala_franqueada", strict=false, nullable=true, allowBlank=true, description="Id da sala franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="data_aula",       strict=true, nullable=false, allowBlank=false, description="data da aula")
     * @FOSRest\RequestParam(name="horario_inicio",  strict=true, nullable=false, allowBlank=false, description="horario de inicio da aula")
     * @FOSRest\RequestParam(name="horario_termino", strict=true, nullable=false, allowBlank=false, description="horario de termino da aula")
     *
     * @FOSRest\Post("/bonus_class/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $objetoORM  = $this->bonusClassFacade->criar($mensagem, $parametros);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::created(["objetoORM" => $objetoORM->getId()], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/bonus_class/atualizar/{id}",
     *     summary="Atualiza um bonus_class",
     *     description="Atualiza um bonus_class no banco",
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
     * @FOSRest\RequestParam(name="concluido",       strict=false, nullable=false, allowBlank=false, description="flag de concluido")
     * @FOSRest\RequestParam(name="funcionario",     strict=false, nullable=true, allowBlank=true, description="Id do funcionario", requirements="\d+")
     * @FOSRest\RequestParam(name="sala_franqueada", strict=false, nullable=true, allowBlank=true, description="Id da sala franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="data_aula",       strict=true, nullable=false, allowBlank=false, description="data da aula")
     * @FOSRest\RequestParam(name="horario_inicio",  strict=true, nullable=false, allowBlank=false, description="horario de inicio da aula")
     * @FOSRest\RequestParam(name="horario_termino", strict=true, nullable=false, allowBlank=false, description="horario de termino da aula")
     *
     * @FOSRest\Patch("/bonus_class/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $paramFetcher, Request $request)
    {
        $parametros = $paramFetcher->all();
        $mensagem   = "";
        $usuarioID  = $request->headers->get('Authorization-User-ID');
        $retorno    = $this->bonusClassFacade->atualizar($mensagem, $id, $usuarioID, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }

       /**
     *
     * @SWG\Patch(
     *     path="/api/bonus_class/cancelar_aula_bonus/{id}",
     *     summary="Atualiza um bonus_class",
     *     description="Atualiza um bonus_class no banco",
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
     * @FOSRest\Patch("/bonus_class/cancelar_aula_bonus/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function cancelaAulaBonus($id, Request $request)
    {
        // $parametros = $paramFetcher->all();
        // $mensagem   = "";
        $usuarioID  = $request->headers->get('Authorization-User-ID');
       
        $retorno    = $this->bonusClassFacade->cancelarAula($mensagem, $id);
        if ($retorno === false) {
            return ResponseFactory::badRequest("erro ao cancelar Bonus", $mensagem);
        }

        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/bonus_class/atualizar_dados/{bonusClassId}",
     *     summary="Atualiza alunos bonus_class",
     *     description="Atualiza alunos bonus_class no banco",
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
     * @FOSRest\RequestParam(name="concluido",       strict=false, nullable=false, allowBlank=false, description="flag de concluido")
     * @FOSRest\RequestParam(name="funcionario",     strict=false, nullable=true, allowBlank=true, description="Id do funcionario", requirements="\d+")
     * @FOSRest\RequestParam(name="sala_franqueada", strict=false, nullable=true, allowBlank=true, description="Id da sala franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="data_aula",       strict=true, nullable=false, allowBlank=false, description="data da aula")
     * @FOSRest\RequestParam(name="horario_inicio",  strict=true, nullable=false, allowBlank=false, description="horario de inicio da aula")
     * @FOSRest\RequestParam(name="horario_termino", strict=true, nullable=false, allowBlank=false, description="horario de termino da aula")
     * @FOSRest\RequestParam(name="lista_alunos",    strict=false, nullable=true, description="Lista de alunos", map=true)
     *
     * @FOSRest\Patch("/bonus_class/atualizar_dados/{bonusClassId}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizarDados($bonusClassId, ParamFetcher $paramFetcher, Request $request)
    {
        $parametros   = $paramFetcher->all();
        $mensagemErro = "";
        $retorno      = true;
        $usuarioID    = $request->headers->get('Authorization-User-ID');
        $listaAlunos  = $parametros[ConstanteParametros::CHAVE_LISTA_ALUNOS];
        unset($parametros[ConstanteParametros::CHAVE_LISTA_ALUNOS]);
        $alunoIndex = 0;

        $retorno = $this->bonusClassFacade->atualizar($mensagemErro, $bonusClassId, $usuarioID, $parametros);

        if ($retorno !== false) {
            if ((empty($listaAlunos) === false) && (count($listaAlunos) > 0)) {
                foreach ($listaAlunos as $bonusClassMetaData) {
                    $retornoAddAlunoPrincipal = '';
                    $retorno = $this->alunosBonusClassFacade->criaAtualizaAlunosBonusClass($bonusClassMetaData, $mensagemErro, $retornoAddAlunoPrincipal);
                    if (($retorno === false) || (empty($mensagemErro) === false)) {
                        break;
                    }
                    $alunoIndex++;
                }
            }
        }

        if (($retorno === true) && (empty($mensagemErro) === true)) {
            self::getEntityManager()->flush();
            if ($retornoAddAlunoPrincipal == 'RESERVA') {
                 return ResponseFactory::accepted([], "Bonus Class Salvo! MAS.....". 
                                                    "\n - Esse horário foi reservado para outro aluno! \n".
                                                    "Vc ficou na lista de espera. Caso o outro aluno \n".
                                                    "cancele vc poderá ter esse horário, ou escolha outro horário.");           
            } else {
                return ResponseFactory::ok([], "Bonus Class configurado com sucesso!");
            }
        } else {
            $mensagemErro = $mensagemErro . "\nErro nos parametros do indice:" . $alunoIndex;
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagemErro);
        }
    }

     /**
      *
      * @SWG\Get(
      *     path="/api/bonus_class/listar_alunos_bonus_class",
      *     summary="Listar bonus_class",
      *     description="Lista as bonus_class do banco",
      *     produces={"application/json"},
      * @SWG\Response(
      *         response="200",
      *         description="Retorna os bonus_class"
      *     ),
      * @SWG\Response(
      *         response="400",
      *         description="Ocorreu algum erro no servidor",
      *     )
      * )
      *
      * @FOSRest\QueryParam(name="pagina",     strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
      * @FOSRest\QueryParam(name="franqueada", strict=true, nullable=false, description="ID da franqueada", requirements="\d+")
      *
      * @FOSRest\Get("/bonus_class/listar_alunos_bonus_class")
      *
      * @return \Symfony\Component\HttpFoundation\Response
      */
    public function listarAlunosBonusClass(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->alunosBonusClassFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/bonus_class/remover/{id}",
     *     summary="Remove uma bonus_class",
     *     description="Remove uma bonus_class no banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna removido com sucesso"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Delete("/bonus_class/remover/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function excluir($id)
    {
        $mensagem = "";
        $retorno  = false;
        // TODO: True ou False
        if ($retorno === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok([], "Excluido com sucesso");
    }


}
