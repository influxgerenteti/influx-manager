<?php

namespace App\Controller\Principal\Franqueadas;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use App\Facade\Principal\FranqueadaFacade;
use App\Facade\Principal\UsuarioFacade;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\Request;
use App\Helper\ConstanteParametros;
use App\Facade\Principal\ContaFacade;
use App\Helper\SituacoesSistema;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class FranqueadaController extends GenericController
{


    /**
     *
     * @var \App\Facade\Principal\FranqueadaFacade
     */
    private $franqueadaFacade;

    /**
     *
     * @var \App\Facade\Principal\UsuarioFacade
     */
    private $usuarioFacade;

    /**
     *
     * @var \App\Facade\Principal\ContaFacade
     */
    private $contaFacade;

    /**
     *
     * @var \App\Repository\Principal\PapelRepository
     */
    private $papelRepository;

    /**
     *
     * @var \App\Repository\Principal\UsuarioRepository
     */
    private $usuarioRepository;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->franqueadaFacade  = new FranqueadaFacade(self::getManagerRegistry());
        $this->usuarioFacade     = new UsuarioFacade(self::getManagerRegistry());
        $this->contaFacade       = new ContaFacade(self::getManagerRegistry());
        $this->papelRepository   = self::getManagerRegistry()->getEntityManager()->getRepository(\App\Entity\Principal\Papel::class);
        $this->usuarioRepository = self::getManagerRegistry()->getEntityManager()->getRepository(\App\Entity\Principal\Usuario::class);
    }

    /**
     * Cria a conta para a nova franqueada
     *
     * @param array $parametros
     * @param \App\Entity\Principal\Franqueada $franqueadaORM
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function criarContaFranqueadaNova($parametros, $franqueadaORM, &$mensagemErro)
    {
        $parametrosConta = [];
        $parametrosConta[ConstanteParametros::CHAVE_DESCRICAO]          = "Caixa (todos os recebimentos)";
        $parametrosConta[ConstanteParametros::CHAVE_FRANQUEADA]         = $franqueadaORM->getId();
        $parametrosConta[ConstanteParametros::CHAVE_BANCO_EMITE_BOLETO] = false;
        $parametrosConta[ConstanteParametros::CHAVE_CONSIDERA_FLUXO_CAIXA] = true;
        $parametrosConta[ConstanteParametros::CHAVE_SITUACAO] = SituacoesSistema::SITUACAO_ATIVO;
        $parametrosConta[ConstanteParametros::CHAVE_BANCO]    = 1;

        $contaORM = $this->contaFacade->criar($mensagemErro, $parametrosConta, false);
        $franqueadaORM->setContaPadrao($contaORM);
        $this->contaFacade->flush($mensagemErro);

        return empty($mensagemErro) === true;
    }

    /**
     * Cria um novo usuario para a franqueada
     *
     * @param array $parametros
     * @param \App\Entity\Principal\Franqueada $franqueadaORM
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function criarUsuarioFranqueadaNova($parametros, $franqueadaORM, &$mensagemErro)
    {
        $parametrosUsuario = [];
        $usuarioORM        = $this->usuarioRepository->findOneBy([ConstanteParametros::CHAVE_CPF => $parametros[ConstanteParametros::CHAVE_CPF]]);
        if (is_null($usuarioORM) === true) {
            $papelORM = $this->papelRepository->findOneByDescricao('Franqueado');
            $parametrosUsuario[ConstanteParametros::CHAVE_NOME]  = "Administrador " . $franqueadaORM->getNome();
            $parametrosUsuario[ConstanteParametros::CHAVE_EMAIL] = $franqueadaORM->getEmailDirecao();
            $parametrosUsuario[ConstanteParametros::CHAVE_CPF]   = $parametros[ConstanteParametros::CHAVE_CPF];
            $parametrosUsuario[ConstanteParametros::CHAVE_FORCA_TROCA_SENHA] = true;
            $parametrosUsuario[ConstanteParametros::CHAVE_SITUACAO]          = SituacoesSistema::SITUACAO_ATIVO;
            $parametrosUsuario[ConstanteParametros::CHAVE_FRANQUEADA_PADRAO] = $franqueadaORM->getId();
            $parametrosUsuario[ConstanteParametros::CHAVE_FRANQUEADAS][0]    = $franqueadaORM->getId();
            $parametrosUsuario[ConstanteParametros::CHAVE_PAPELS]            = [$papelORM];
            $usuarioORM = $this->usuarioFacade->criarUsuario($mensagemErro, $parametrosUsuario);
            if (is_null($usuarioORM) === false) {
                $moduloPapelAcaosORM = $papelORM->getModuloPapelAcao();
                foreach ($moduloPapelAcaosORM as $mpaORM) {
                    $mua = new \App\Entity\Principal\ModuloUsuarioAcao();
                    $mua->setUsuario($usuarioORM);
                    $mua->setModulo($mpaORM->getModulo());
                    $mua->setAcaoSistema($mpaORM->getAcaoSistema());
                    $this->usuarioFacade->callBackPersist($mua, $mensagemErro);
                }

                $this->usuarioFacade->callBackFlush($mensagemErro);
            } else {
                $mensagemErro .= "Não foi possivel atribuir permissões ao usuario criado.\nFavor atribui-las manualmente.";
            }
        } else {
            $this->usuarioFacade->adicionarFranqueada($mensagemErro, $usuarioORM->getId(), $franqueadaORM);
        }//end if

        return empty($mensagemErro) === true;
    }

    /**
     * Cria dados padrao para a nova franqueada
     *
     * @param array $parametros
     * @param \App\Entity\Principal\Franqueada $franqueadaORM
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function criarDadosDefaultParaFranqueada($parametros, $franqueadaORM, &$mensagemErro)
    {
        $bRetornoConta   = $this->criarContaFranqueadaNova($parametros, $franqueadaORM, $mensagemErro);
        $bRetornoUsuario = $this->criarUsuarioFranqueadaNova($parametros, $franqueadaORM, $mensagemErro);
        return ($bRetornoConta && $bRetornoUsuario);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/franqueada/listar",
     *     summary="Listar franqueadas",
     *     description="Lista as franqueadas do banco",
     *     tags={"Franqueada"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os franqueadas"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina",  strict=true, requirements="\d+", nullable=false, allowBlank=false, default="1", description="Pagina para realizar o scroll")
     * @FOSRest\QueryParam(name="order",   strict=false, nullable=true,  description="Coluna de ordenação")
     * @FOSRest\QueryParam(name="direcao", strict=false, nullable=true,  description="ASC|DESC")
     *
     * @FOSRest\Get("/franqueada/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listaFranqueadas(ParamFetcher $paramFetcher, Request $requestHeader)
    {
        $parametros = $paramFetcher->all();
        $usuario    = (int) $requestHeader->headers->get('Authorization-User-ID');
        $parametros[ConstanteParametros::CHAVE_USUARIO] = $this->usuarioFacade->buscarUsuario($usuario, true);

        $resultados = $this->franqueadaFacade->buscarFranqueadas($parametros);

        if (empty($resultados) === true) {
            return ResponseFactory::badRequest([], 'Nenhuma franqueada encontrada.');
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/franqueada/parametros/{id}",
     *     summary="Buscar os parametros da franqueada",
     *     description="Busca os parametros da franqueada através da ID",
     *     tags={"Franqueada"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a franqueada"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/franqueada/parametros/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarParametrosFranqueada($id)
    {
        $franqueada = $this->franqueadaFacade->buscarParametrosFranqueada($id);
        if (is_null($franqueada) === true) {
            return ResponseFactory::notFound([], "Franqueada não encontrada.");
        }

        return ResponseFactory::ok($franqueada);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/franqueada/{id}",
     *     summary="Buscar as franqueadas",
     *     description="Busca as franqueadas através da ID",
     *     tags={"Franqueada"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a franqueada"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/franqueada/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarFranqueada($id)
    {
        $franqueada = $this->franqueadaFacade->buscarFranqueada($id);
        if (is_null($franqueada) === true) {
            return ResponseFactory::notFound([], "Franqueada não encontrada.");
        }

        return ResponseFactory::ok($franqueada);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/franqueada/criar",
     *     summary="Cria uma Franqueada",
     *     description="Cria uma Franqueada no banco",
     *     tags={"Franqueada"},
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
     * @FOSRest\RequestParam(name="nome",                                  strict=true, nullable=false, allowBlank=true, description="Nome da Franqueada")
     * @FOSRest\RequestParam(name="cnpj",                                  strict=true, nullable=false, allowBlank=true, description="CNPJ da Franqueada")
     * @FOSRest\RequestParam(name="cpf",                                   strict=true, nullable=false, allowBlank=true, description="CPF para o usuario padrao da franqueada")
     * @FOSRest\RequestParam(name="dias_em_abertos_movimentos",            strict=true, nullable=false, allowBlank=false, description="Dias em aberto do movimento", default="0", requirements="\d+")
     * @FOSRest\RequestParam(name="dias_para_negativacao",                 strict=true, nullable=false, allowBlank=false, description="Dias para negativar aluno", requirements="\d+")
     * @FOSRest\RequestParam(name="dias_lembrete_cobranca",                strict=true, nullable=false, allowBlank=false, description="Dias para lembrar o aluno", requirements="\d+")
     * @FOSRest\RequestParam(name="sabado_dia_util",                       strict=true, nullable=false, allowBlank=false, description="Flag de contabilizar Sabado como dia Util", default="1", requirements="(0|1)")
     * @FOSRest\RequestParam(name="razao_social",                          strict=false, nullable=true, description="Razao Social ou Nome Fantasia da Franqueada")
     * @FOSRest\RequestParam(name="estado",                                strict=false, nullable=true, description="Estado", requirements="\d+")
     * @FOSRest\RequestParam(name="cidade",                                strict=false, nullable=true, description="Cidade", requirements="\d+")
     * @FOSRest\RequestParam(name="endereco",                              strict=false, nullable=true, description="Endereco em que a Franqueada se reside")
     * @FOSRest\RequestParam(name="numero_endereco",                       strict=false, nullable=true, description="Número", default="0", requirements="\d+")
     * @FOSRest\RequestParam(name="bairro_endereco",                       strict=false, nullable=true, description="Bairro", default="")
     * @FOSRest\RequestParam(name="complemento_endereco",                  strict=false, nullable=true, description="Complemento", default="")
     * @FOSRest\RequestParam(name="cep_endereco",                          strict=false, nullable=true, description="CEP", default="")
     * @FOSRest\RequestParam(name="inscricao_estadual",                    strict=false, nullable=true, description="Inscricao Estadual")
     * @FOSRest\RequestParam(name="telefone",                              strict=false, nullable=true, description="Telefone principal da Franqueada")
     * @FOSRest\RequestParam(name="telefone_secundario",                   strict=false, nullable=true, description="Telefone secundario da Franqueada")
     * @FOSRest\RequestParam(name="email",                                 strict=true, nullable=true, description="E-Mail da Franqueada")
     * @FOSRest\RequestParam(name="email_comercial",                       strict=true, nullable=false, description="E-Mail Comercial")
     * @FOSRest\RequestParam(name="email_direcao",                         strict=true, nullable=false, description="E-Mail da Direcao")
     * @FOSRest\RequestParam(name="desconto_super_amigos_ativo",           strict=true, nullable=false, description="Situação Desconto do Super Amigos", default="0", requirements="(0|1)")
     * @FOSRest\RequestParam(name="desconto_super_amigos_turbinado_ativo", strict=true, nullable=false, description="Situação Desconto do Super Amigos Turbinado", default="0", requirements="(0|1)")
     * @FOSRest\RequestParam(name="tipo_movimento_conta_receber",          strict=false, nullable=true, description="Tipo do Movimento da Contas a Receber", default="0", requirements="\d+")
     * @FOSRest\RequestParam(name="tipo_movimento_conta_pagar",            strict=false, nullable=true, description="Tipo do Movimento da Contas a Pagar", default="0", requirements="\d+")
     * @FOSRest\RequestParam(name="percentual_multa",                      strict=false, nullable=true, description="Percentual de Multa aplicado pela franqueada", requirements="^\d{0,5}+\.?\d{0,2}?$", default="0")
     * @FOSRest\RequestParam(name="percentual_juro_dia",                   strict=false, nullable=true, description="Percentual de Juros por dia aplicado pela franqueada", requirements="^\d{0,5}+\.?\d{0,4}?$", default="0")
     * @FOSRest\RequestParam(name="percentual_desconto_a_vista",           strict=false, nullable=true, description="Percentual do desconto a vista",  requirements="\d+")
     * @FOSRest\RequestParam(name="limite_dias_alteracao_documento",       strict=false, nullable=true, description="Percentual de Juros por dia aplicado pela franqueada", requirements="^\d{0,5}+\.?\d{0,2}?$", default="0")
     *
     * @FOSRest\Post("/franqueada/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criarFranqueada(ParamFetcher $paramFetcher, Request $request)
    {
        $parametros = $paramFetcher->all();
        $usuarioID  = $request->headers->get('Authorization-User-ID');
        $mensagem   = "";

        $franqueadaOBJ = $this->franqueadaFacade->criarFranqueada($mensagem, $parametros);
        if (empty($mensagem) === false) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        if ($this->usuarioFacade->adicionarFranqueada($mensagem, $usuarioID, $franqueadaOBJ) === false) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        if ($this->criarDadosDefaultParaFranqueada($parametros, $franqueadaOBJ, $mensagem) === false) {
            return ResponseFactory::created([], "Registro criado com sucesso, porém os dados padrão para login não foi criado. Pois ocorreu o seguinte erro:" . $mensagem);
        }

        return ResponseFactory::created(["franqueada" => $franqueadaOBJ->getId()], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/franqueada/atualizar/{id}",
     *     summary="Atualiza um franqueada",
     *     tags={"Franqueada"},
     *     description="Atualiza um franqueada no banco",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="204",
     *         description="Retorna criado com sucesso"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\RequestParam(name="nome",                                  strict=true, nullable=false, allowBlank=true, description="Nome da Franqueada")
     * @FOSRest\RequestParam(name="cnpj",                                  strict=true, nullable=false, allowBlank=true, description="CNPJ da Franqueada")
     * @FOSRest\RequestParam(name="dias_em_abertos_movimentos",            strict=true, nullable=false, allowBlank=false, description="Dias em aberto do movimento", default="0", requirements="\d+")
     * @FOSRest\RequestParam(name="dias_para_negativacao",                 strict=true, nullable=false, allowBlank=false, description="Dias para negativar aluno", requirements="\d+")
     * @FOSRest\RequestParam(name="dias_lembrete_cobranca",                strict=true, nullable=false, allowBlank=false, description="Dias para lembrar o aluno", requirements="\d+")
     * @FOSRest\RequestParam(name="sabado_dia_util",                       strict=true, nullable=false, allowBlank=false, description="Flag de contabilizar Sabado como dia Util", default="1", requirements="(0|1)")
     * @FOSRest\RequestParam(name="razao_social",                          strict=false, nullable=true, description="Razao Social ou Nome Fantasia da Franqueada")
     * @FOSRest\RequestParam(name="endereco",                              strict=false, nullable=true, description="Endereco em que a Franqueada se reside")
     * @FOSRest\RequestParam(name="numero_endereco",                       strict=false, nullable=true, description="Número", default="0", requirements="\d+")
     * @FOSRest\RequestParam(name="bairro_endereco",                       strict=false, nullable=true, description="Bairro")
     * @FOSRest\RequestParam(name="complemento_endereco",                  strict=false, nullable=true, description="Complemento")
     * @FOSRest\RequestParam(name="cep_endereco",                          strict=false, nullable=true, description="CEP")
     * @FOSRest\RequestParam(name="estado",                                strict=false, nullable=true, description="Estado", requirements="\d+")
     * @FOSRest\RequestParam(name="cidade",                                strict=false, nullable=true, description="Cidade", requirements="\d+")
     * @FOSRest\RequestParam(name="inscricao_estadual",                    strict=false, nullable=true, description="Inscricao Estadual")
     * @FOSRest\RequestParam(name="telefone",                              strict=false, nullable=true, description="Telefone principal da Franqueada")
     * @FOSRest\RequestParam(name="telefone_secundario",                   strict=false, nullable=true, description="Telefone secundario da Franqueada")
     * @FOSRest\RequestParam(name="email",                                 strict=false, nullable=true, description="E-Mail da Franqueada")
     * @FOSRest\RequestParam(name="email_comercial",                       strict=true, nullable=true, description="E-Mail Comercial")
     * @FOSRest\RequestParam(name="email_direcao",                         strict=true, nullable=true, description="E-Mail da Direcao")
     * @FOSRest\RequestParam(name="desconto_super_amigos_ativo",           strict=true, nullable=false, description="Situação Desconto do Super Amigos", default="0", requirements="(0|1)")
     * @FOSRest\RequestParam(name="desconto_super_amigos_turbinado_ativo", strict=true, nullable=false, description="Situação Desconto do Super Amigos Turbinado", default="0", requirements="(0|1)")
     * @FOSRest\RequestParam(name="tipo_movimento_conta_receber",          strict=false, nullable=true, description="Tipo do Movimento da Contas a Receber", default="0", requirements="\d+")
     * @FOSRest\RequestParam(name="tipo_movimento_conta_pagar",            strict=false, nullable=true, description="Tipo do Movimento da Contas a Pagar", default="0", requirements="\d+")
     * @FOSRest\RequestParam(name="situacao",                              strict=false, nullable=false, description="Situação do registro", requirements="[A|I]")
     * @FOSRest\RequestParam(name="percentual_multa",                      strict=false, nullable=true, description="Percentual de Multa aplicado pela franqueada", requirements="^\d{0,5}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="percentual_juro_dia",                   strict=false, nullable=true, description="Percentual de Juros por dia aplicado pela Franqueada", requirements="^\d{0,5}+\.?\d{0,4}?$")
     * @FOSRest\RequestParam(name="percentual_desconto_a_vista",           strict=false, nullable=true, description="Percentual do desconto a vista",  requirements="\d+")
     * @FOSRest\RequestParam(name="limite_dias_alteracao_documento",       strict=false, nullable=true, description="Percentual de Juros por dia aplicado pela franqueada", requirements="^\d{0,5}+\.?\d{0,2}?$", default="0")
     *
     * @FOSRest\Patch("/franqueada/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizarFranqueada($id, ParamFetcher $paramFetcher)
    {
        $parametros = $paramFetcher->all();
        $mensagem   = "";
        $retorno    = $this->franqueadaFacade->atualizarFranqueada($mensagem, $id, $parametros);
        if ($retorno === false)
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/franqueada/remover/{id}",
     *     summary="Remove uma franqueada",
     *     description="Remove uma franqueada no banco",
     *     tags={"Franqueada"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna criado com sucesso"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Delete("/franqueada/remover/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function excluirFranqueada($id)
    {
        $mensagem = "";
        $retorno  = $this->franqueadaFacade->excluirFranqueada($mensagem, $id);
        if ($retorno === false)
            return ResponseFactory::badRequest([], $mensagem);
        return ResponseFactory::ok([], "Excluido com sucesso");
    }


}
