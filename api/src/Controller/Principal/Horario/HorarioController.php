<?php

namespace App\Controller\Principal\Horario;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\HorarioFacade;
use App\Facade\Principal\HorarioAulaFacade;
use App\Helper\ConstanteParametros;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class HorarioController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\HorarioFacade
     */
    private $horarioFacade;

    /**
     *
     * @var \App\Facade\Principal\HorarioAulaFacade
     */
    private $horarioAulaFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->horarioFacade     = new HorarioFacade(self::getManagerRegistry());
        $this->horarioAulaFacade = new HorarioAulaFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/horario/listar",
     *     summary="Listar horario",
     *     description="Lista os horário do banco com paginação",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os horario"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina",  strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     * @FOSRest\QueryParam(name="order",   strict=false, nullable=true,  description="Coluna de ordenação")
     * @FOSRest\QueryParam(name="direcao", strict=false, nullable=true,  description="ASC|DESC")
     *
     * @FOSRest\Get("/horario/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->horarioFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/horario/buscar_todos",
     *     summary="Buscar todos horarios",
     *     description="Lista todos os horarios do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os horarios"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="order",   strict=false, nullable=true,  description="Coluna de ordenação")
     * @FOSRest\QueryParam(name="direcao", strict=false, nullable=true,  description="ASC|DESC")
     *
     * @FOSRest\Get("/horario/buscar_todos")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarTodos(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->horarioFacade->buscarTodos($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/horario/{id}",
     *     summary="Buscar a horario",
     *     description="Busca a horario através da ID",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a horario"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/horario/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagem  = "";
        $objetoORM = $this->horarioFacade->buscarPorId($mensagem, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagem);
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/horario/criar",
     *     summary="Cria uma horario",
     *     description="Cria uma horario no banco",
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
     * @FOSRest\RequestParam(name="franqueada",    strict=true, nullable=false, allowBlank=false, description="Franqueada ID")
     * @FOSRest\RequestParam(name="descricao",     strict=true, nullable=false, allowBlank=false, description="descricao")
     * @FOSRest\RequestParam(name="horarios_aula", strict=true, nullable=false, allowBlank=false, description="Array de objetos de horarios de aulas", map=true)
     *
     * @FOSRest\Post("/horario/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $arrayObjetosHorarioAula = [];

        if (count($parametros[ConstanteParametros::CHAVE_HORARIOS_AULA]) < 1) {
            return ResponseFactory::conflict([], "Não foi encontrado indices para cadastrar a chave de HORARIO_AULA na listagem de parametros");
        }

        $horarioORM = $this->horarioFacade->criar($mensagem, $parametros);
        if ((is_null($horarioORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        // Constroi array de objetos
        foreach ($parametros[ConstanteParametros::CHAVE_HORARIOS_AULA] as $arrayHorarioAula) {
            $objetoORM = $this->horarioAulaFacade->criar($mensagem, $horarioORM, $arrayHorarioAula);
            if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
                return ResponseFactory::conflict(["parametros_horarioAula" => $arrayHorarioAula], $mensagem);
            }

            $arrayObjetosHorarioAula[] = $objetoORM;
        }

        // Verifica horas iguais e valida se tem 1 hora de diferenca entre as aulas
        foreach ($arrayObjetosHorarioAula as $indice => $horarioAulaORM) {
            if (\App\BO\Principal\HorarioAulaBO::naoExisteHorarioIgualParaDiaSelecionado($horarioAulaORM, $arrayObjetosHorarioAula, $indice) === true) {
                self::getEntityManager()->persist($horarioAulaORM);
            } else {
                return ResponseFactory::conflict([], "Não foi possivel prosseguir com o cadastramento, devido a já existir uma aula já cadastrada para o dia e hora informado.");
            }
        }

        self::getEntityManager()->flush();

        return ResponseFactory::created(["objetoORM" => $horarioORM->getId()], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/horario/atualizar/{id}",
     *     summary="Atualiza um horario",
     *     description="Atualiza um horario no banco",
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
     * @FOSRest\RequestParam(name="franqueada",    strict=true, nullable=true, allowBlank=true, description="Franqueada ID")
     * @FOSRest\RequestParam(name="descricao",     strict=false, nullable=true, allowBlank=true, description="descricao")
     * @FOSRest\RequestParam(name="situacao",      strict=false, nullable=true, allowBlank=true, description="situacao", requirements="[A|I]")
     * @FOSRest\RequestParam(name="horarios_aula", strict=true, nullable=true, allowBlank=true, description="Array de objetos de horarios de aulas", map=true)
     *
     * @FOSRest\Patch("/horario/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $arrayObjetosHorarioAula = [];
        $horarioORM = null;
        $retorno    = $this->horarioFacade->atualizar($mensagem, $id, $horarioORM, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_HORARIOS_AULA]) === true)&&(count($parametros[ConstanteParametros::CHAVE_HORARIOS_AULA]) >= 1)) {
            // Constroi array de objetos
            foreach ($parametros[ConstanteParametros::CHAVE_HORARIOS_AULA] as $arrayHorarioAula) {
                if (isset($arrayHorarioAula[ConstanteParametros::CHAVE_ID]) === true) {
                    $objetoORM = null;
                    $retorno   = $this->horarioAulaFacade->atualizar($mensagem, $arrayHorarioAula[ConstanteParametros::CHAVE_ID], $arrayHorarioAula, $objetoORM);
                    if ((is_null($retorno) === true) || (empty($mensagem) === false)) {
                        return ResponseFactory::conflict(["parametros_horarioAula" => $arrayHorarioAula], $mensagem);
                    }
                } else {
                    $objetoORM = $this->horarioAulaFacade->criar($mensagem, $horarioORM, $arrayHorarioAula);
                    if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
                        return ResponseFactory::conflict(["parametros_horarioAula" => $arrayHorarioAula], $mensagem);
                    }

                    self::getEntityManager()->persist($objetoORM);
                }

                $arrayObjetosHorarioAula[] = $objetoORM;
            }

            // Verifica horas iguais e valida se tem 1 hora de diferenca entre as aulas
            foreach ($arrayObjetosHorarioAula as $indice => $horarioAulaORM) {
                if ($horarioAulaORM->getHorario()->getId() !== (int) $id) {
                    return ResponseFactory::conflict([], "Só é possivel alterar os registros que pertencem ao cadastro atrelado.");
                }

                if (\App\BO\Principal\HorarioAulaBO::naoExisteHorarioIgualParaDiaSelecionado($horarioAulaORM, $horarioORM->getHorarioAulas(), $indice, true) === false) {
                    return ResponseFactory::conflict([], "Não foi possivel prosseguir com o cadastramento, devido a já existir uma aula já cadastrada para o dia e hora informado.");
                }
            }
        }//end if

        self::getEntityManager()->flush();

        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/horario/remover/{id}",
     *     summary="Remove uma horario",
     *     description="Remove uma horario no banco",
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
     * @FOSRest\Delete("/horario/remover/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function excluir($id)
    {
        $mensagem = "";
        $retorno  = $this->horarioFacade->remover($mensagem, $id);
        if ($retorno === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok([], "Excluido com sucesso");
    }


}
