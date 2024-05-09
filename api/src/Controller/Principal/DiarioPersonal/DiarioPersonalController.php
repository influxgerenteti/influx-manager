<?php

namespace App\Controller\Principal\DiarioPersonal;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\DiarioPersonalFacade;
use Symfony\Component\HttpFoundation\Request;
use App\Helper\ConstanteParametros;
use App\Helper\VariaveisCompartilhadas;
use App\Facade\Principal\AlunoAvaliacaoFacade;
use App\Facade\Principal\AlunoAvaliacaoConceitualFacade;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class DiarioPersonalController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\DiarioPersonalFacade
     */
    protected $diarioPersonalFacade;

    /**
     *
     * @var \App\Facade\Principal\AlunoAvaliacaoFacade
     */
    private $alunoAvaliacaoFacade;

    /**
     *
     * @var \App\Facade\Principal\AlunoAvaliacaoConceitualFacade
     */
    private $alunoAvaliacaoConceitualFacade;

    /**
     *
     * @var \App\Repository\Principal\AlunoRepository
     */
    private $alunoRepository;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->diarioPersonalFacade           = new DiarioPersonalFacade(self::getManagerRegistry());
        $this->alunoAvaliacaoFacade           = new AlunoAvaliacaoFacade(self::getManagerRegistry());
        $this->alunoAvaliacaoConceitualFacade = new AlunoAvaliacaoConceitualFacade(self::getManagerRegistry());
        $this->alunoRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\Aluno::class);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/diario_personal/buscar_diario_por_contrato/{contratoId}",
     *     summary="Buscar a diario_personal",
     *     description="Busca a diario_personal através da ID",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a diario_personal"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="agendamento_personal", strict=false, description="AgendamentoPersonal ID", requirements="\d+")
     *
     * @FOSRest\Get("/diario_personal/buscar_diario_por_contrato/{contratoId}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarDiarioPorContrato($contratoId, ParamFetcher $paramFetcher)
    {
        $parametros = $paramFetcher->all();

        $resultados = $this->diarioPersonalFacade->buscarDiarioPersonal($contratoId, $parametros);

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/diario_personal/buscar_historico_por_contrato/{contratoId}",
     *     summary="Buscar a diario_personal",
     *     description="Busca a diario_personal através da ID",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a diario_personal"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/diario_personal/buscar_historico_por_contrato/{contratoId}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarHistoricoPersonalPorContrato($contratoId)
    {
        $resultados = $this->diarioPersonalFacade->buscarHistoricoPersonalPorContrato($contratoId);

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/diario_personal/buscar_licoes_aplicadas_por_contrato/{contratoId}",
     *     summary="Buscar a diario_personal",
     *     description="Busca a diario_personal através da ID",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a diario_personal"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/diario_personal/buscar_licoes_aplicadas_por_contrato/{contratoId}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarLicoesAplicadasPorContrato($contratoId)
    {
        $resultados = $this->diarioPersonalFacade->buscarLicoesAplicadasPorContrato($contratoId);

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/diario_personal/buscar_avaliacoes_por_contrato/{contratoId}",
     *     summary="Buscar a diario_personal",
     *     description="Busca a diario_personal através da ID",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a diario_personal"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="livro", strict=true, nullable=false, description="ID do livro", requirements="\d+")
     *
     * @FOSRest\Get("/diario_personal/buscar_avaliacoes_por_contrato/{contratoId}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarAvaliacoesPorContrato($contratoId, ParamFetcher $paramFetcher)
    {
        $parametros = $paramFetcher->all();
        $resultados = $this->diarioPersonalFacade->buscarAvaliacoesPorContratoId($contratoId, $parametros);
        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/diario_personal/lancar_atualizar_frequencia",
     *     summary="Cria uma diario_personal",
     *     description="Cria uma diario_personal no banco",
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
     * @FOSRest\RequestParam(name="id",                   strict=false, nullable=false, description="ID do AlunoDiarioPersonal", requirements="\d+")
     * @FOSRest\RequestParam(name="aluno",                strict=true, nullable=false, description="ID do Aluno", requirements="\d+")
     * @FOSRest\RequestParam(name="funcionario",          strict=true, nullable=false, description="ID do Funcionario que aplicou a aula", requirements="\d+")
     * @FOSRest\RequestParam(name="sala_franqueada",      strict=true, nullable=false, description="ID da sala", requirements="\d+")
     * @FOSRest\RequestParam(name="agendamento_personal", strict=true, nullable=false, description="ID do AgendamentoPersonal", requirements="\d+")
     * @FOSRest\RequestParam(name="livro",                strict=true, nullable=false, description="ID do livro", requirements="\d+")
     * @FOSRest\RequestParam(name="creditos_personal",    strict=true, nullable=false, description="ID do creditosPersonal", requirements="\d+")
     * @FOSRest\RequestParam(name="franqueada",           strict=true, nullable=false, description="ID da franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="observacao",           strict=true, nullable=true, description="Observacao")
     * @FOSRest\RequestParam(name="presenca",             strict=true, nullable=false, description="Presenca", requirements="(P|F)")
     * @FOSRest\RequestParam(name="atividade_ca",         strict=true, nullable=true, description="Atividade CA", requirements="(E|EA|NE)")
     * @FOSRest\RequestParam(name="atividade_ce",         strict=true, nullable=true, description="Atividade CE", requirements="(E|EA|NE)")
     * @FOSRest\RequestParam(name="data_aula",            strict=false, nullable=true, description="Data da aula")
     *
     * @FOSRest\RequestParam(name="licaos", strict=true, nullable=false, description="ID da franqueada", requirements="\d+", map=true)
     *
     * @FOSRest\Post("/diario_personal/lancar_atualizar_frequencia")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lancarAtualizarFrequencia(ParamFetcher $paramFetcher, Request $request)
    {
        $parametros   = $paramFetcher->all();
        $mensagemErro = "";

        if ((isset($parametros[ConstanteParametros::CHAVE_ID]) === true) && (empty($parametros[ConstanteParametros::CHAVE_ID]) === false)) {
            $id = $parametros[ConstanteParametros::CHAVE_ID];
            unset($parametros[ConstanteParametros::CHAVE_ID]);
            if ($this->diarioPersonalFacade->atualizar($mensagemErro, $id, $parametros) === false) {
                return ResponseFactory::conflict(["parametros" => $parametros], $mensagemErro);
            }
        } else {
            $this->diarioPersonalFacade->criar($mensagemErro, $parametros);
            if (empty($mensagemErro) === false) {
                return ResponseFactory::conflict(["parametros" => $parametros], $mensagemErro);
            }
        }

        return ResponseFactory::created([], "Registro no diário de classe lançado com sucesso!");
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/diario_personal/lancar_atualizar_notas",
     *     summary="Atualiza um diario_personal",
     *     description="Atualiza um diario_personal no banco",
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
     * @FOSRest\RequestParam(name="alunos_avaliacao",            strict=true, nullable=true, allowBlank=false, description="Array Alunos Avaliacao", map=true)
     * @FOSRest\RequestParam(name="alunos_avaliacao_conceitual", strict=true, nullable=true, allowBlank=false, description="Array Alunos Avaliacao Conceitual", map=true)
     * @FOSRest\RequestParam(name="franqueada",                  strict=true, nullable=false, description="ID da franqueada", requirements="\d+")
     *
     * @FOSRest\Post("/diario_personal/lancar_atualizar_notas")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lancarAtualizarNotas(ParamFetcher $paramFetcher, Request $request)
    {
        $parametros   = $paramFetcher->all();
        $mensagemErro = "";
        $bRetorno     = false;

        if ((isset($parametros[ConstanteParametros::CHAVE_ALUNOS_AVALIACAO]) === true)&&(count($parametros[ConstanteParametros::CHAVE_ALUNOS_AVALIACAO]) > 0)) {
            $alunosAvaliacao = $parametros[ConstanteParametros::CHAVE_ALUNOS_AVALIACAO];
            foreach ($alunosAvaliacao as &$alunoAvaliacaoMetaData) {
                $alunoAvaliacaoMetaData[ConstanteParametros::CHAVE_PERSONAL]   = true;
                $alunoAvaliacaoMetaData[ConstanteParametros::CHAVE_FRANQUEADA] = VariaveisCompartilhadas::$franqueadaID;
                $bRetorno = $this->alunoAvaliacaoFacade->lancarAtualizarNotas($mensagemErro, $alunoAvaliacaoMetaData);
                if ($bRetorno === false) {
                    break;
                }
            }//end foreach
        }//end if

        if ((isset($parametros[ConstanteParametros::CHAVE_ALUNOS_AVALIACAO_CONCEITUAL]) === true)&&(count($parametros[ConstanteParametros::CHAVE_ALUNOS_AVALIACAO_CONCEITUAL]) > 0) && ($bRetorno === true)) {
            $alunosAvaliacaoConceitual = $parametros[ConstanteParametros::CHAVE_ALUNOS_AVALIACAO_CONCEITUAL];
            foreach ($alunosAvaliacaoConceitual as $alunoAvaliacaoConceitualMetaData) {
                $alunoAvaliacaoConceitualMetaData[ConstanteParametros::CHAVE_PERSONAL]   = true;
                $alunoAvaliacaoConceitualMetaData[ConstanteParametros::CHAVE_FRANQUEADA] = VariaveisCompartilhadas::$franqueadaID;
                $bRetorno = $this->alunoAvaliacaoConceitualFacade->lancarAtualizarNotas($mensagemErro, $alunoAvaliacaoConceitualMetaData);
                if ($bRetorno === false) {
                    break;
                }
            }
        }

        if ($bRetorno === false) {
            $mensagemErro = "Não foi possivel prosseguir com o lançamento/Atualização.\nErro inesperado.\n" . $mensagemErro;
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagemErro);
        }

        self::getEntityManager()->flush();
        return ResponseFactory::ok([], "Notas aplicadas com sucesso!");
    }


}
