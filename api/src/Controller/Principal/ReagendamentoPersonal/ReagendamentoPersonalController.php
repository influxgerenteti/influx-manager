<?php

namespace App\Controller\Principal\ReagendamentoPersonal;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\ReagendamentoPersonalFacade;
use App\Facade\Principal\OcorrenciaAcademicaFacade;
use App\Facade\Principal\OcorrenciaAcademicaDetalhesFacade;
use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;
use App\Helper\VariaveisCompartilhadas;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class ReagendamentoPersonalController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\ReagendamentoPersonalFacade
     */
    private $reagendamentoPersonalFacade;

    /**
     *
     * @var \App\Facade\Principal\OcorrenciaAcademicaFacade
     */
    private $ocorrenciaAcademicaFacade;

    /**
     *
     * @var \App\Facade\Principal\OcorrenciaAcademicaDetalhesFacade
     */
    private $ocorrenciaAcademicaDetalhesFacade;

    /**
     *
     * @var \App\Repository\Principal\AgendamentoPersonalRepository
     */
    private $agendamentoPersonalRepository;

    /**
     *
     * @var \App\Repository\Principal\TipoOcorrenciaRepository
     */
    private $tipoOcorrenciaRepository;


    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->reagendamentoPersonalFacade       = new ReagendamentoPersonalFacade(self::getManagerRegistry());
        $this->ocorrenciaAcademicaFacade         = new OcorrenciaAcademicaFacade(self::getManagerRegistry());
        $this->ocorrenciaAcademicaDetalhesFacade = new OcorrenciaAcademicaDetalhesFacade(self::getManagerRegistry());
        $this->agendamentoPersonalRepository     = self::getEntityManager()->getRepository(\App\Entity\Principal\AgendamentoPersonal::class);
        $this->tipoOcorrenciaRepository          = self::getEntityManager()->getRepository(\App\Entity\Principal\TipoOcorrencia::class);
    }

    /**
     * Gera ocorrencia academica
     *
     * @param string $mensagemErro
     * @param int $usuarioId
     * @param string $tipoOcorrencia
     * @param string $origemOcorrencia
     * @param int $alunoId
     * @param int $contratoId
     * @param string $observacaoTexto
     *
     * @return boolean
     */
    private function gerarOcorrenciaAcademica(&$mensagemErro, $usuarioId, $tipoOcorrencia, $origemOcorrencia, $alunoId, $contratoId, $observacaoTexto)
    {
        $bRetorno          = true;
        $tipoOcorrenciaORM = null;
        $retornaIdDoTipoOcorrencia     = null;
        $retornaFuncionarioIdDoUsuario = $this->ocorrenciaAcademicaFacade->retornaFuncionarioIdDoUsuario($usuarioId);
        $tipoOcorrenciaORM = $this->tipoOcorrenciaRepository->findOneBy([ConstanteParametros::CHAVE_TIPO => $tipoOcorrencia]);
        if (is_null($retornaFuncionarioIdDoUsuario) === true) {
            $mensagemErro .= "Não foi encontrado um funcionario cadastrado para o usuario informado.\n";
        }

        if (is_null($tipoOcorrenciaORM) === true) {
            $mensagemErro .= "Não foi encontrado uma Ocorrencia para o tipo informado.\n";
        } else {
            $retornaIdDoTipoOcorrencia = $tipoOcorrenciaORM->getId();
        }

        if (($bRetorno = empty($mensagemErro)) === true) {
            $parametrosOcorrenciaAcademica = [
                ConstanteParametros::CHAVE_FRANQUEADA             => VariaveisCompartilhadas::$franqueadaID,
                ConstanteParametros::CHAVE_ALUNO                  => $alunoId,
                ConstanteParametros::CHAVE_CONTRATO               => $contratoId,
                ConstanteParametros::CHAVE_ORIGEM_OCORRENCIA_TIPO => $origemOcorrencia,
                ConstanteParametros::CHAVE_USUARIO                => $usuarioId,
                ConstanteParametros::CHAVE_FUNCIONARIO            => $retornaFuncionarioIdDoUsuario,
                ConstanteParametros::CHAVE_TIPO_OCORRENCIA        => $retornaIdDoTipoOcorrencia,
                ConstanteParametros::CHAVE_DATA_CONCLUSAO         => new \DateTime(),
                ConstanteParametros::CHAVE_SITUACAO               => SituacoesSistema::OCORRENCIA_ABERTA,
                ConstanteParametros::CHAVE_TEXTO                  => $observacaoTexto,
            ];
            $ocorrenciaAcademicaORM        = $this->ocorrenciaAcademicaFacade->criar($mensagemErro, $parametrosOcorrenciaAcademica);
            if ((is_null($ocorrenciaAcademicaORM) === true) || (empty($mensagemErro) === false)) {
                $bRetorno = false;
            } else {
                $ocorrenciaAcademicaDetalhesORM = $this->ocorrenciaAcademicaDetalhesFacade->criar($mensagemErro, $ocorrenciaAcademicaORM, $parametrosOcorrenciaAcademica, false);
                if ((is_null($ocorrenciaAcademicaDetalhesORM) === true) || (empty($mensagemErro) === false)) {
                    $bRetorno = false;
                }
            }
        }//end if

        return $bRetorno;
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/reagendamento_personal/criar",
     *     summary="Cria um reagendamento_personal",
     *     description="Cria um reagendamento_personal no banco",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="204",
     *         description="Retorna Criado com sucesso"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\RequestParam(name="aluno",                strict=true, nullable=true, allowBlank=false, description="ID Do Aluno Personal", requirements="\d+")
     * @FOSRest\RequestParam(name="contrato",             strict=true, nullable=true, allowBlank=false, description="ID Do Contrato", requirements="\d+")
     * @FOSRest\RequestParam(name="agendamento_personal", strict=true, nullable=true, allowBlank=false, description="ID Do Agendamento Personal", requirements="\d+")
     * @FOSRest\RequestParam(name="data_reagendada",      strict=true, nullable=true, allowBlank=false, description="Data de reagendamento selecionada")
     * @FOSRest\RequestParam(name="funcionario",          strict=true, nullable=true, description="ID do Funcionario que aplicou a aula", requirements="\d+")
     * @FOSRest\RequestParam(name="sala_franqueada",      strict=true, nullable=true, description="ID da sala", requirements="\d+")
     * @FOSRest\RequestParam(name="franqueada",      strict=true, nullable=true, description="ID franqueada", requirements="\d+")
     *
     * @FOSRest\Post("/reagendamento_personal/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criarReagendamento(ParamFetcher $paramFetcher, Request $request)
    {          
        
        $parametros        = $paramFetcher->all();
        
        $mensagem          = "";
        $usuarioID         = $request->headers->get('Authorization-User-ID');
        $agendamentoAntigo = $this->agendamentoPersonalRepository->find($parametros[ConstanteParametros::CHAVE_AGENDAMENTO_PERSONAL]);
        $objetoORM         = $this->reagendamentoPersonalFacade->criar($mensagem, $parametros);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        $textoOcorrencia  = "****PERSONAL SEMANAL****\nAluno(a) desmarcou sua aula de \"" . $agendamentoAntigo->getInicio()->format("d/m/Y H:i") . "\" e remarcou para \"" . $objetoORM->getDataReagendada()->format("d/m/Y H:i") . "\".";
        $bGerarOcorrencia = $this->gerarOcorrenciaAcademica($mensagem, $usuarioID, SituacoesSistema::TIPO_OCORRENCIA_TIPO_ITEM_REAGENDAMENTO_PERSONAL, SituacoesSistema::ORIGEM_OCORRENCIA_REAGENDAMENTO_PERSONAL, $parametros[ConstanteParametros::CHAVE_ALUNO], $parametros[ConstanteParametros::CHAVE_CONTRATO], $textoOcorrencia);
        if (($bGerarOcorrencia === false) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], "Erro ao gerar a ocorrencia.\n" . $mensagem);
        }

        self::getEntityManager()->flush();

        return ResponseFactory::created(["objetoORM" => 45], "Registro criado com sucesso!");

    }


}
