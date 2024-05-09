<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\Modulo;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ModuloFixtures extends Fixture implements DependentFixtureInterface
{
    public const MODULO_CONFIGURACOES = "modulo_configuracoes";
    public const MODULO_CADASTROS     = "modulo_cadastros";
    public const MODULO_FINANCEIRO    = "modulo_financeiro";
    public const MODULO_CONFIGURACOES_FINANCEIRO = "modulo_configuracoes_financeiro";
    public const MODULO_REAGENDAMENTO_PERSONAL   = "modulo_reagendamento_personal";
    public const MODULO_RELATORIOS = "modulo_relatorios";
    public const MODULO_COMERCIAL  = "modulo_comercial";
    public const MODULO_ACADEMICO  = "modulo_academico";
    public const MODULO_DASHBOARD  = "modulo_dashboard";
    public const MODULO_PERSONAL   = "modulo_personal";
    public const MODULO_LOG        = "modulo_log";
    public const MODULO_PAPEL      = "modulo_papel";
    public const MODULO_USUARIO    = "modulo_usuario";
    public const MODULO_PLANEJAMENTO_LICAO = "modulo_planejamento_licao";
    public const MODULO_MODULO     = "modulo_modulo";
    public const MODULO_FRANQUEADA = "modulo_franqueada";
    public const MODULO_CONFIGURACOES_FRANQUEADA = "modulo_configuracoe_franqueada";
    public const MODULO_PESSOA           = "modulo_pessoa";
    public const MODULO_OPERADORA_CARTAO = "modulo_operadora_cartao";
    public const MODULO_SEGMENTOS_EMPRESA_CONVENIO = "modulo_segmentos_empresa_convenio";
    public const MODULO_CONVENIO = "modulo_convenio";
    public const MODULO_CADASTROS_LISTAGEM_CONVENIO_REGIONAL_REFERENCE = "modulo_cadastrados_listagem_convenio_regional";
    public const MODULO_ALUNO_REFERENCE  = "modulo_aluno";
    public const MODULO_INTERESSADO      = "modulo_interessado";
    public const MODULO_HORARIO          = "modulo_horario";
    public const MODULO_CONTRATO         = "modulo_contrato";
    public const MODULO_ENTREGA_MATERIAL = "modulo_entrega_material";
    public const MODULO_FORMULARIO_FOLLOWUP = "modulo_formulario_followup";
    public const MODULO_FUNCIONARIO         = "modulo_funcionario";
    public const MODULO_CURSOS_REFERENCE    = "modulo_cursos";
    public const MODULO_TURMAS = "modulo_turmas";
    public const MODULO_PARAMETROS_FRANQUEADORA = "modulo_parametros_franqueadora";
    public const MODULO_META_FRANQUEADA         = "modulo_meta_franqueada";
    public const MODULO_META_FRANQUEADORA       = "modulo_meta_franqueadora";
    public const MODULO_TIPO_MOVIMENTO_ESTOQUE  = "modulo_tipo_movimento_estoque";
    public const MODULO_CLASSIFICACAO_ALUNO     = "modulo_classificacao_aluno";
    public const MODULO_DATA_CALENDARIO         = "modulo_data_calendario";
    public const MODULO_BANCO = "modulo_banco";
    public const MODULO_ITEM  = "modulo_item";
    public const MODULO_TIPO_MOVIMENTO_CONTA = "modulo_tipo_movimento_conta";
    public const MODULO_CONTA            = "modulo_conta";
    public const MODULO_MOTIVOS          = "modulo_motivos";
    public const MODULO_MOTIVOS_CONVENIO = "modulo_motivos_convenio";
    public const MODULO_PLANO_CONTA      = "modulo_plano_conta";
    public const MODULO_FORMA_PAGAMENTO  = "modulo_forma_pagamento";
    public const MODULO_CONTAS_PAGAR_REFERENCE   = "modulo_contas_pagar";
    public const MODULO_CONTAS_RECEBER_REFERENCE = "modulo_contas_receber";
    public const MODULO_EXTRATO           = "modulo_extrato";
    public const MODULO_CHEQUE_REFERENCE  = "modulo_cheque";
    public const MODULO_CARTOES_REFERENCE = "modulo_cartoes";
    public const MODULO_IMPORTACAO_BOLETO_REFERENCE = "modulo_importacao_boleto";
    public const MODULO_LIVROS          = "modulo_livros";
    public const MODULO_SALA            = "modulo_sala";
    public const MODULO_SALA_FRANQUEADA = "modulo_sala_franqueada";
    public const MODULO_VALOR_HORA      = "modulo_valor_hora";
    public const MODULO_IMPORTADOR      = "modulo_importador";
    public const MODULO_MODELO_CONTRATO = "modulo_modelo_contrato";
    public const MODULO_RELATORIO_CHEQUES      = "modulo_relatorio_cheques";
    public const MODULO_RELATORIO_CONTAS_PAGAR = "modulo_relatorio_contas_pagar";
    public const MODULO_FUNIL_VENDAS           = "modulo_funil_vendas";
    public const MODULO_OCORRENCIA_ACADEMICA   = "modulo_ocorrencia_academica";
    // public const MODULO_SERVICO           = "modulo_servico";
    public const MODULO_GERAR_MALA_DIRETA           = "modulo_gerar_mala_direta";
    public const MODULO_REPOSICAO_DE_AULA_AVALIACAO = "modulo_reposicao_de_aula_avaliacao";
    public const MODULO_RELATORIO_DE_BALANCETE_FINANCEIRO = "modulo_relatorio_de_balancete_financeiro";
    public const MODULO_ATIVIDADE_EXTRA       = "modulo_atividade_extra";
    public const MODULO_NIVELAMENTO           = "modulo_nivelamento";
    public const MODULO_CRONOGRAMA            = "modulo_cronograma";
    public const MODULO_RELATORIO_FUNCIONARIO = "modulo_relatorio_funcionario";
    public const MODULO_DIARIO_CLASSE         = "modulo_diario_classe";
    public const MODULO_AGENDA_COMPROMISSO    = "modulo_agenda_compromisso";
    public const MODULO_BONUS_CLASS           = "modulo_bonus_class";
    public const MODULO_FOLLOW_UP            = "modulo_follow_up";
    public const MODULO_FOLLOWUP_COMERCIAL   = "modulo_follow_up_comercial";
    public const MODULO_INDICADORES          = "modulo_indicadores";
    public const MODULO_INDICADORES_FRANQUIA = "modulo_indicadores_franquia";
    public const MODULO_PARAMETROS_FINANCEIROS = "modulo_parametros_financeiros";
    public const MODULO_BIBLIOTECA            = "modulo_biblioteca";
    public const MODULO_EMPRESTIMO_BIBLIOTECA = "modulo_emprestimo_biblioteca";
    public const MODULO_RENEGOCIACAO          = "modulo_renegociacao";
    public const MODULO_SERVICOS = "modulo_servicos";
    public const MODULO_RELATORIO_INADIMPLENCIA = "modulo_relatorio_inadimplencia";
    public const MODULO_PESQUISA_VISIBILIDADE   = "modulo_pesquisa_vibilidade";
    public const MODULO_MIDIA = "modulo_midia";

    public function load(ObjectManager $manager)
    {
        $modulos = [];

        $config = new Modulo();
        $config->setNome("Configurações");
        $config->setUrl("/configuracoes");
        $modulos[] = $config;
        $manager->persist($config);
        $this->addReference(self::MODULO_CONFIGURACOES, $config);

        $cadastros = new Modulo();
        $cadastros->setNome("Cadastros");
        $cadastros->setUrl("/cadastros");
        $modulos[] = $cadastros;
        $manager->persist($cadastros);
        $this->addReference(self::MODULO_CADASTROS, $cadastros);

        $financeiro = new Modulo();
        $financeiro->setNome("Financeiro");
        $financeiro->setUrl("/financeiro");
        $modulos[] = $financeiro;
        $manager->persist($financeiro);
        $this->addReference(self::MODULO_FINANCEIRO, $financeiro);

        $configFranqueadora = new Modulo();
        $configFranqueadora->setNome("Franqueadora");
        $configFranqueadora->setUrl("/franqueadora");
        $modulos[] = $configFranqueadora;
        $manager->persist($configFranqueadora);
        $this->addReference(self::MODULO_CONFIGURACOES_FRANQUEADA, $configFranqueadora);

        $relatorios = new Modulo();
        $relatorios->setNome("Relatórios");
        $relatorios->setUrl("/relatorios");
        $modulos[] = $relatorios;
        $manager->persist($relatorios);
        $this->addReference(self::MODULO_RELATORIOS, $relatorios);

        $comercial = new Modulo();
        $comercial->setNome("Comercial");
        $comercial->setUrl("/comercial");
        $modulos[] = $comercial;
        $manager->persist($comercial);
        $this->addReference(self::MODULO_COMERCIAL, $comercial);

        $academico = new Modulo();
        $academico->setNome("Pedagógico");
        $academico->setUrl("/academico");
        $modulos[] = $academico;
        $manager->persist($academico);
        $this->addReference(self::MODULO_ACADEMICO, $academico);

        $manager->flush();

        $dashboard = new Modulo();
        $dashboard->setNome("Dashboard");
        $dashboard->setUrl("/dashboard");
        $dashboard->setOrdem("2");
        // $dashboard->setapenasFranqueadora(true);
        $modulos[] = $dashboard;
        $manager->persist($dashboard);
        $this->addReference(self::MODULO_DASHBOARD, $dashboard);

        $log = new Modulo();
        $log->setNome("Logs");
        $log->setUrl("/configuracoes/log");
        $log->setModuloPai($config);
        $log->setEntity('App\\Entity\\Log\\Log');
        $modulos[] = $log;
        $manager->persist($log);
        $this->addReference(self::MODULO_LOG, $log);

        $papel = new Modulo();
        $papel->setNome("Permissões");
        $papel->setUrl("/configuracoes/papel");
        // $papel->setapenasFranqueadora(true);
        $papel->setModuloPai($config);
        $papel->setEntity('App\\Entity\\Principal\\Papel');
        $modulos[] = $papel;
        $manager->persist($papel);
        $this->addReference(self::MODULO_PAPEL, $papel);

        $usuario = new Modulo();
        $usuario->setNome("Usuários");
        $usuario->setUrl("/configuracoes/usuario");
        $usuario->setModuloPai($config);
        $usuario->setEntity('App\\Entity\\Principal\\Usuario');
        $modulos[] = $usuario;
        $manager->persist($usuario);
        $this->addReference(self::MODULO_USUARIO, $usuario);

        $planejamentoLicao = new Modulo();
        $planejamentoLicao->setNome("Programação de lições");
        $planejamentoLicao->setUrl("/configuracoes/planejamento-licao");
        // $planejamentoLicao->setapenasFranqueadora(true);
        $planejamentoLicao->setModuloPai($config);
        $planejamentoLicao->setEntity('App\\Entity\\Principal\\PlanejamentoLicao');
        $modulos[] = $planejamentoLicao;
        $manager->persist($planejamentoLicao);
        $this->addReference(self::MODULO_PLANEJAMENTO_LICAO, $planejamentoLicao);

        $modulo = new Modulo();
        $modulo->setNome("Módulos");
        $modulo->setUrl("/configuracoes/modulo");
        $modulo->setModuloPai($config);
        $modulo->setEntity('App\\Entity\\Principal\\Modulo');
        $modulo->setExibirNoMenu(0);
        $modulos[] = $modulo;
        $manager->persist($modulo);
        $this->addReference(self::MODULO_MODULO, $modulo);

        $franqueada = new Modulo();
        $franqueada->setNome("Franquias");
        $franqueada->setUrl("/franqueadora/franqueada");
        $franqueada->setModuloPai($configFranqueadora);
        $franqueada->setEntity('App\\Entity\\Principal\\Franqueada');
        // $franqueada->setapenasFranqueadora(true);
        $modulos[] = $franqueada;
        $manager->persist($franqueada);
        $this->addReference(self::MODULO_FRANQUEADA, $franqueada);

        $pessoa = new Modulo();
        $pessoa->setNome("Responsáveis e Fornecedores");
        $pessoa->setUrl("/cadastros/pessoa");
        $pessoa->setModuloPai($cadastros);
        $pessoa->setEntity('App\\Entity\\Principal\\Pessoa');
        $modulos[] = $pessoa;
        $manager->persist($pessoa);
        $this->addReference(self::MODULO_PESSOA, $pessoa);

        $operadoraCartao = new Modulo();
        $operadoraCartao->setNome("Operadoras de cartão");
        $operadoraCartao->setUrl("/cadastros/operadora-cartao");
        $operadoraCartao->setModuloPai($cadastros);
        $operadoraCartao->setEntity('App\\Entity\\Principal\\OperadoraCartao');
        $modulos[] = $operadoraCartao;
        $manager->persist($operadoraCartao);
        $this->addReference(self::MODULO_OPERADORA_CARTAO, $operadoraCartao);

        $segmentosEmpresaConvenio = new Modulo();
        $segmentosEmpresaConvenio->setNome("Segmentos empresa convênio");
        $segmentosEmpresaConvenio->setUrl("/cadastros/segmento-empresa-convenio");
        $segmentosEmpresaConvenio->setModuloPai($cadastros);
        $segmentosEmpresaConvenio->setEntity('App\\Entity\\Principal\\SegmentoEmpresaConvenio');
        // $segmentosEmpresaConvenio->setapenasFranqueadora(true);
        $modulos[] = $segmentosEmpresaConvenio;
        $manager->persist($segmentosEmpresaConvenio);
        $this->addReference(self::MODULO_SEGMENTOS_EMPRESA_CONVENIO, $segmentosEmpresaConvenio);

        $convenio = new Modulo();
        $convenio->setNome("Negociação de parcerias");
        $convenio->setUrl("/cadastros/convenio");
        $convenio->setModuloPai($cadastros);
        $convenio->setEntity('App\\Entity\\Principal\\Convenio');
        $modulos[] = $convenio;
        $manager->persist($convenio);
        $this->addReference(self::MODULO_CONVENIO, $convenio);

        $convenioNacional = new Modulo();
        $convenioNacional->setNome("Lista de convênios");
        $convenioNacional->setUrl("/cadastros/lista-convenio-nacional");
        $convenioNacional->setModuloPai($cadastros);
        $convenioNacional->setEntity('App\\Entity\\Principal\\Convenio');
        // $convenioNacional->setapenasFranqueadora(true);
        $modulos[] = $convenioNacional;
        $manager->persist($convenioNacional);
        $this->addReference(self::MODULO_CADASTROS_LISTAGEM_CONVENIO_REGIONAL_REFERENCE, $convenioNacional);

        $aluno = new Modulo();
        $aluno->setNome("Alunos");
        $aluno->setUrl("/academico/aluno");
        $aluno->setModuloPai($academico);
        $aluno->setEntity('App\\Entity\\Principal\\Aluno');
        $modulos[] = $aluno;
        $manager->persist($aluno);
        $this->addReference(self::MODULO_ALUNO_REFERENCE, $aluno);

        $interessado = new Modulo();
        $interessado->setNome("Interessados");
        $interessado->setUrl("/cadastros/interessados");
        $interessado->setModuloPai($cadastros);
        $interessado->setEntity('App\\Entity\\Principal\\Interessado');
        $modulos[] = $interessado;
        $manager->persist($interessado);
        $this->addReference(self::MODULO_INTERESSADO, $interessado);

        $horario = new Modulo();
        $horario->setNome("Horário das turmas");
        $horario->setUrl("/configuracoes/horario");
        $horario->setModuloPai($config);
        $horario->setEntity('App\\Entity\\Principal\\Horario');
        $modulos[] = $horario;
        $manager->persist($horario);
        $this->addReference(self::MODULO_HORARIO, $horario);

        $contrato = new Modulo();
        $contrato->setNome("Contratos");
        $contrato->setUrl("/cadastros/contrato");
        $contrato->setModuloPai($cadastros);
        $contrato->setEntity('App\\Entity\\Principal\\Contrato');
        $modulos[] = $contrato;
        $manager->persist($contrato);
        $this->addReference(self::MODULO_CONTRATO, $contrato);

        $entregaMaterial = new Modulo();
        $entregaMaterial->setNome("Entrega de itens");
        $entregaMaterial->setUrl("/cadastros/entrega-material");
        $entregaMaterial->setModuloPai($cadastros);
        $entregaMaterial->setEntity('App\\Entity\\Principal\\Item');
        $modulos[] = $entregaMaterial;
        $manager->persist($entregaMaterial);
        $this->addReference(self::MODULO_ENTREGA_MATERIAL, $entregaMaterial);

        $formularioFollowUp = new Modulo();
        $formularioFollowUp->setNome("Formulario follow-Up");
        $formularioFollowUp->setUrl("/cadastros/formulario-follow-up");
        // $formularioFollowUp->setapenasFranqueadora(true);
        $formularioFollowUp->setModuloPai($cadastros);
        $formularioFollowUp->setEntity('App\\Entity\\Principal\\FormularioFollowUp');
        $modulos[] = $formularioFollowUp;
        $manager->persist($formularioFollowUp);
        $this->addReference(self::MODULO_FORMULARIO_FOLLOWUP, $formularioFollowUp);

        $funcionario = new Modulo();
        $funcionario->setNome("Colaboradores");
        $funcionario->setUrl("/cadastros/funcionario");
        $funcionario->setModuloPai($cadastros);
        $funcionario->setEntity('App\\Entity\\Principal\\Funcionario');
        $modulos[] = $funcionario;
        $manager->persist($funcionario);
        $this->addReference(self::MODULO_FUNCIONARIO, $funcionario);

        $curso = new Modulo();
        $curso->setNome("Cursos");
        $curso->setUrl("/cadastros/curso");
        // $curso->setApenasFranqueadora(true);
        $curso->setModuloPai($cadastros);
        $curso->setEntity('App\\Entity\\Principal\\Curso');
        $modulos[] = $curso;
        $manager->persist($curso);
        $this->addReference(self::MODULO_CURSOS_REFERENCE, $curso);

        $turmas = new Modulo();
        $turmas->setNome("Turmas");
        $turmas->setUrl("/academico/turma");
        $turmas->setModuloPai($academico);
        $turmas->setEntity('App\\Entity\\Principal\\Turma');
        $modulos[] = $turmas;
        $manager->persist($turmas);
        $this->addReference(self::MODULO_TURMAS, $turmas);

        $parametrosFranqueadora = new Modulo();
        $parametrosFranqueadora->setNome("Parâmetros de franqueadora");
        $parametrosFranqueadora->setUrl("/franqueadora/parametros-franqueadora");
        $parametrosFranqueadora->setModuloPai($configFranqueadora);
        $parametrosFranqueadora->setApenasFranqueadora(false);
        $parametrosFranqueadora->setEntity('App\\Entity\\Principal\\ParametrosFranqueadora');
        $modulos[] = $parametrosFranqueadora;
        $manager->persist($parametrosFranqueadora);
        $this->addReference(self::MODULO_PARAMETROS_FRANQUEADORA, $parametrosFranqueadora);

        $metaFranqueada = new Modulo();
        $metaFranqueada->setNome("Definição de metas");
        $metaFranqueada->setUrl("/configuracoes/meta-franqueada");
        $metaFranqueada->setModuloPai($config);
        // $parametrosFranqueadora->setApenasFranqueadora(true);
        $metaFranqueada->setEntity('App\\Entity\\Principal\\MetaFranqueada');
        $modulos[] = $metaFranqueada;
        $manager->persist($metaFranqueada);
        $this->addReference(self::MODULO_META_FRANQUEADA, $metaFranqueada);

        $metaFranqueadora = new Modulo();
        $metaFranqueadora->setNome("Definição de metas");
        $metaFranqueadora->setUrl("/franqueadora/meta-franqueada");
        $metaFranqueadora->setModuloPai($configFranqueadora);
        $parametrosFranqueadora->setApenasFranqueadora(true);
        $metaFranqueadora->setEntity('App\\Entity\\Principal\\MetaFranqueada');
        $modulos[] = $metaFranqueadora;
        $manager->persist($metaFranqueadora);
        $this->addReference(self::MODULO_META_FRANQUEADORA, $metaFranqueadora);

        $tipoMovimentoEstoque = new Modulo();
        $tipoMovimentoEstoque->setNome("Tipo de movimento em estoque");
        $tipoMovimentoEstoque->setUrl("/configuracoes/tipo-movimento-estoque");
        // $tipoMovimentoEstoque->setapenasFranqueadora(true);
        $tipoMovimentoEstoque->setModuloPai($config);
        $tipoMovimentoEstoque->setExibirNoMenu(0);
        $tipoMovimentoEstoque->setEntity('App\\Entity\\Principal\\TipoMovimentoEstoque');
        $modulos[] = $tipoMovimentoEstoque;
        $manager->persist($tipoMovimentoEstoque);
        $this->addReference(self::MODULO_TIPO_MOVIMENTO_ESTOQUE, $tipoMovimentoEstoque);

        $classificacaoAluno = new Modulo();
        $classificacaoAluno->setNome("Classificação de aluno");
        $classificacaoAluno->setUrl("/configuracoes/classificacao-aluno");
        $classificacaoAluno->setModuloPai($config);
        $classificacaoAluno->setEntity('App\\Entity\\Principal\\ClassificacaoAluno');
        $modulos[] = $classificacaoAluno;
        $manager->persist($classificacaoAluno);
        $this->addReference(self::MODULO_CLASSIFICACAO_ALUNO, $classificacaoAluno);

        $dataCalendario = new Modulo();
        $dataCalendario->setNome("Calendários");
        $dataCalendario->setUrl("/configuracoes/calendario");
        $dataCalendario->setModuloPai($config);
        $dataCalendario->setEntity('App\\Entity\\Principal\\Calendario');
        $modulos[] = $classificacaoAluno;
        $modulos[] = $dataCalendario;
        $manager->persist($dataCalendario);
        $this->addReference(self::MODULO_DATA_CALENDARIO, $dataCalendario);

        $banco = new Modulo();
        $banco->setNome("Banco");
        $banco->setUrl("/configuracoes/banco");
        // $banco->setApenasFranqueadora(true);
        $banco->setModuloPai($config);
        $banco->setEntity('App\\Entity\\Principal\\Banco');
        $modulos[] = $banco;
        $manager->persist($banco);
        $this->addReference(self::MODULO_BANCO, $banco);

        $item = new Modulo();
        $item->setNome("Item");
        $item->setUrl("/configuracoes/item");
        $item->setModuloPai($config);
        $item->setEntity('App\\Entity\\Principal\\Item');
        // $item->setapenasFranqueadora(true);
        $modulos[] = $item;
        $manager->persist($item);
        $this->addReference(self::MODULO_ITEM, $item);

        $tipoMovimentoConta = new Modulo();
        $tipoMovimentoConta->setNome("Tipo de movimento em conta");
        $tipoMovimentoConta->setUrl("/configuracoes/tipo-movimento-conta");
        // $tipoMovimentoConta->setapenasFranqueadora(true);
        $tipoMovimentoConta->setModuloPai($config);
        $tipoMovimentoConta->setExibirNoMenu(0);
        $tipoMovimentoConta->setEntity('App\\Entity\\Principal\\TipoMovimentoConta');
        $modulos[] = $tipoMovimentoConta;
        $manager->persist($tipoMovimentoConta);
        $this->addReference(self::MODULO_TIPO_MOVIMENTO_CONTA, $tipoMovimentoConta);

        $conta = new Modulo();
        $conta->setNome("Conta");
        $conta->setUrl("/configuracoes/conta");
        $conta->setModuloPai($config);
        $conta->setEntity('App\\Entity\\Principal\\Conta');
        $modulos[] = $conta;
        $manager->persist($conta);
        $this->addReference(self::MODULO_CONTA, $conta);

        $motivos = new Modulo();
        $motivos->setNome("Motivos de matrícula perdida");
        $motivos->setUrl("/configuracoes/motivos-matricula-perdida");
        // $motivos->setApenasFranqueadora(true);
        $motivos->setModuloPai($config);
        $motivos->setEntity('App\\Entity\\Principal\\MotivoNaoFechamentoMatricula');
        $modulos[] = $motivos;
        $manager->persist($motivos);
        $this->addReference(self::MODULO_MOTIVOS, $motivos);

        $motivosConvenio = new Modulo();
        $motivosConvenio->setNome("Motivos de convênio perdido");
        $motivosConvenio->setUrl("/configuracoes/motivos-convenio-perdido");
        // $motivosConvenio->setApenasFranqueadora(true);
        $motivosConvenio->setModuloPai($config);
        $motivosConvenio->setEntity('App\\Entity\\Principal\\MotivoNaoFechamentoConvenio');
        $modulos[] = $motivosConvenio;
        $manager->persist($motivosConvenio);
        $this->addReference(self::MODULO_MOTIVOS_CONVENIO, $motivosConvenio);

        $planoConta = new Modulo();
        $planoConta->setNome("Plano de conta");
        $planoConta->setUrl("/configuracoes/plano-conta");
        $planoConta->setModuloPai($config);
        $planoConta->setEntity('App\\Entity\\Principal\\PlanoConta');
        $modulos[] = $planoConta;
        $manager->persist($planoConta);
        $this->addReference(self::MODULO_PLANO_CONTA, $planoConta);

        $formaPagamento = new Modulo();
        $formaPagamento->setNome("Forma de pagamento");
        $formaPagamento->setUrl("/configuracoes/forma-pagamento");
        // $formaPagamento->setapenasFranqueadora(true);
        $formaPagamento->setModuloPai($config);
        $formaPagamento->setEntity('App\\Entity\\Principal\\FormaPagamento');
        $modulos[] = $formaPagamento;
        $manager->persist($formaPagamento);
        $this->addReference(self::MODULO_FORMA_PAGAMENTO, $formaPagamento);

        $contasPagar = new Modulo();
        $contasPagar->setNome("Contas a pagar");
        $contasPagar->setUrl("/financeiro/contas-pagar");
        $contasPagar->setModuloPai($financeiro);
        $contasPagar->setEntity('App\\Entity\\Principal\\ContaPagar');
        $modulos[] = $contasPagar;
        $manager->persist($contasPagar);
        $this->addReference(self::MODULO_CONTAS_PAGAR_REFERENCE, $contasPagar);

        $contasReceber = new Modulo();
        $contasReceber->setNome("Contas a receber");
        $contasReceber->setUrl("/financeiro/contas-receber");
        $contasReceber->setModuloPai($financeiro);
        $contasReceber->setEntity('App\\Entity\\Principal\\ContaReceber');
        $modulos[] = $contasReceber;
        $manager->persist($contasReceber);
        $this->addReference(self::MODULO_CONTAS_RECEBER_REFERENCE, $contasReceber);

        $extrato = new Modulo();
        $extrato->setNome("Extrato");
        $extrato->setUrl("/financeiro/extrato");
        $extrato->setModuloPai($financeiro);
        $extrato->setEntity('App\\Entity\\Principal\\MovimentoConta');
        $modulos[] = $extrato;
        $manager->persist($extrato);
        $this->addReference(self::MODULO_EXTRATO, $extrato);

        $chequesPagarReceber = new Modulo();
        $chequesPagarReceber->setNome("Controle de cheques");
        $chequesPagarReceber->setUrl("/financeiro/cheques-pagar-receber");
        $chequesPagarReceber->setModuloPai($financeiro);
        $chequesPagarReceber->setEntity('App\\Entity\\Principal\\Cheque');
        $modulos[] = $chequesPagarReceber;
        $manager->persist($chequesPagarReceber);
        $this->addReference(self::MODULO_CHEQUE_REFERENCE, $chequesPagarReceber);

        $cartao = new Modulo();
        $cartao->setNome("Controle de cartões");
        $cartao->setUrl("/financeiro/cartao");
        $cartao->setModuloPai($financeiro);
        $cartao->setEntity('App\\Entity\\Principal\\TransacaoCartao');
        $modulos[] = $cartao;
        $manager->persist($cartao);
        $this->addReference(self::MODULO_CARTOES_REFERENCE, $cartao);

        $importacaoBoleto = new Modulo();
        $importacaoBoleto->setNome("Boletos");
        $importacaoBoleto->setUrl("/financeiro/importacao-boleto");
        $importacaoBoleto->setModuloPai($financeiro);
        $modulos[] = $importacaoBoleto;
        $manager->persist($importacaoBoleto);
        $this->addReference(self::MODULO_IMPORTACAO_BOLETO_REFERENCE, $importacaoBoleto);

        $livros = new Modulo();
        $livros->setNome("Livros");
        $livros->setUrl("/configuracoes/livro");
        $livros->setModuloPai($config);
        $livros->setEntity('App\\Entity\\Principal\\Livro');
        // $livros->setapenasFranqueadora(true);
        $modulos[] = $livros;
        $manager->persist($livros);
        $this->addReference(self::MODULO_LIVROS, $livros);

        $salaFranqueada = new Modulo();
        $salaFranqueada->setNome("Salas");
        $salaFranqueada->setUrl("/configuracoes/sala-franqueada");
        $salaFranqueada->setModuloPai($config);
        $salaFranqueada->setEntity('App\\Entity\\Principal\\SalaFranqueada');
        $modulos[] = $salaFranqueada;
        $manager->persist($salaFranqueada);
        $this->addReference(self::MODULO_SALA_FRANQUEADA, $salaFranqueada);

        $valorHora = new Modulo();
        $valorHora->setNome("Valores por hora");
        $valorHora->setUrl("/configuracoes/valor-hora");
        $valorHora->setModuloPai($config);
        $valorHora->setEntity('App\\Entity\\Principal\\ValorHora');
        $modulos[] = $valorHora;
        $manager->persist($valorHora);
        $this->addReference(self::MODULO_VALOR_HORA, $valorHora);

        $importador = new Modulo();
        $importador->setNome("Importador");
        $importador->setUrl("/configuracoes/importador");
        $importador->setModuloPai($config);
        $modulos[] = $importador;
        $manager->persist($importador);
        $this->addReference(self::MODULO_IMPORTADOR, $importador);

        $modeloContrato = new Modulo();
        $modeloContrato->setNome("Modelo de documentos");
        $modeloContrato->setUrl("/configuracoes/modelo-template");
        $modeloContrato->setModuloPai($config);
        $modeloContrato->setEntity('App\\Entity\\Principal\\ModeloTemplate');
        // $modeloContrato->setapenasFranqueadora(true);
        $modulos[] = $modeloContrato;
        $manager->persist($modeloContrato);
        $this->addReference(self::MODULO_MODELO_CONTRATO, $modeloContrato);

        $relatorioCheques = new Modulo();
        $relatorioCheques->setNome("Relatório de cheques");
        $relatorioCheques->setUrl("/relatorios/relatorio-cheques");
        $relatorioCheques->setModuloPai($relatorios);
        $relatorioCheques->setEntity('App\\Entity\\Principal\\Relatorio');
        $modulos[] = $relatorioCheques;
        $manager->persist($relatorioCheques);
        $this->addReference(self::MODULO_RELATORIO_CHEQUES, $relatorioCheques);

        $relatorioContasPagar = new Modulo();
        $relatorioContasPagar->setNome("Relatório de contas a pagar");
        $relatorioContasPagar->setUrl("/relatorios/relatorio-contas-pagar");
        $relatorioContasPagar->setModuloPai($relatorios);
        $relatorioContasPagar->setEntity('App\\Entity\\Principal\\Relatorio');
        $modulos[] = $relatorioContasPagar;
        $manager->persist($relatorioContasPagar);
        $this->addReference(self::MODULO_RELATORIO_CONTAS_PAGAR, $relatorioContasPagar);

        $funilVendas = new Modulo();
        $funilVendas->setNome("Funil de vendas");
        $funilVendas->setUrl("/comercial/funil-vendas");
        $funilVendas->setModuloPai($comercial);
        $funilVendas->setEntity('App\\Entity\\Principal\\Interessado');
        $modulos[] = $funilVendas;
        $manager->persist($funilVendas);
        $this->addReference(self::MODULO_FUNIL_VENDAS, $funilVendas);

        $ocorrenciaAcademica = new Modulo();
        $ocorrenciaAcademica->setNome("Ocorrências");
        $ocorrenciaAcademica->setOrdem("1");
        $ocorrenciaAcademica->setUrl("/ocorrencia-academica");
        $modulos[] = $ocorrenciaAcademica;
        $manager->persist($ocorrenciaAcademica);
        $this->addReference(self::MODULO_OCORRENCIA_ACADEMICA, $ocorrenciaAcademica);

        /*
            $servico = new Modulo();
            $servico->setNome("Serviço");
            $servico->setUrl("/academico/servico");
            $servico->setModuloPai($academico);
            $modulos[] = $servico;
            $manager->persist($servico);
            $this->addReference(self::MODULO_SERVICO, $servico);
        */

        $agendaCompromisso = new Modulo();
        $agendaCompromisso->setNome("Agenda");
        $agendaCompromisso->setUrl("/academico/agenda-compromisso");
        $agendaCompromisso->setModuloPai($academico);
        $modulos[] = $agendaCompromisso;
        $manager->persist($agendaCompromisso);
        $this->addReference(self::MODULO_AGENDA_COMPROMISSO, $agendaCompromisso);

        $gerarMalaDireta = new Modulo();
        $gerarMalaDireta->setNome("Geração de mala direta");
        $gerarMalaDireta->setUrl("/relatorios/mala-direta-aluno");
        $gerarMalaDireta->setModuloPai($relatorios);
        $gerarMalaDireta->setEntity('App\\Entity\\Principal\\Relatorio');
        $modulos[] = $gerarMalaDireta;
        $manager->persist($gerarMalaDireta);
        $this->addReference(self::MODULO_GERAR_MALA_DIRETA, $gerarMalaDireta);

        $reposicaoDeAulaAvaliacao = new Modulo();
        $reposicaoDeAulaAvaliacao->setNome("Make-up class & test");
        $reposicaoDeAulaAvaliacao->setUrl("/academico/reposicao-aula-avaliacao");
        $reposicaoDeAulaAvaliacao->setModuloPai($academico);
        $reposicaoDeAulaAvaliacao->setEntity('App\\Entity\\Principal\\ReposicaoAula');
        $modulos[] = $reposicaoDeAulaAvaliacao;
        $manager->persist($reposicaoDeAulaAvaliacao);
        $this->addReference(self::MODULO_REPOSICAO_DE_AULA_AVALIACAO, $reposicaoDeAulaAvaliacao);

        $relatorioDeBalanceteFinanceiro = new Modulo();
        $relatorioDeBalanceteFinanceiro->setNome("Relatórios de balancete");
        $relatorioDeBalanceteFinanceiro->setUrl("/relatorios/relatorio-balancete-financeiro");
        $relatorioDeBalanceteFinanceiro->setModuloPai($relatorios);
        $relatorioDeBalanceteFinanceiro->setEntity('App\\Entity\\Principal\\Relatorio');
        $modulos[] = $relatorioDeBalanceteFinanceiro;
        $manager->persist($relatorioDeBalanceteFinanceiro);
        $this->addReference(self::MODULO_RELATORIO_DE_BALANCETE_FINANCEIRO, $relatorioDeBalanceteFinanceiro);

        $atividadeExtra = new Modulo();
        $atividadeExtra->setNome("Atividade extra e retake");
        $atividadeExtra->setUrl("/academico/atividade-extra");
        $atividadeExtra->setModuloPai($academico);
        $atividadeExtra->setEntity('App\\Entity\\Principal\\AtividadeExtra');
        $modulos[] = $atividadeExtra;
        $manager->persist($atividadeExtra);
        $this->addReference(self::MODULO_ATIVIDADE_EXTRA, $atividadeExtra);

        $nivelamento = new Modulo();
        $nivelamento->setNome("Nivelamento");
        $nivelamento->setUrl("/academico/nivelamento");
        $nivelamento->setModuloPai($academico);
        $nivelamento->setEntity('App\\Entity\\Principal\\OcorrenciaAcademica');
        $modulos[] = $nivelamento;
        $manager->persist($nivelamento);
        $this->addReference(self::MODULO_NIVELAMENTO, $nivelamento);

        $cronograma = new Modulo();
        $cronograma->setNome("Impressão de class record");
        $cronograma->setUrl("/relatorios/impressao-class-record");
        $cronograma->setModuloPai($relatorios);
        $cronograma->setEntity('App\\Entity\\Principal\\Relatorio');
        $modulos[] = $cronograma;
        $manager->persist($cronograma);
        $this->addReference(self::MODULO_CRONOGRAMA, $cronograma);

        $relatorioFuncionario = new Modulo();
        $relatorioFuncionario->setNome("Relatório de funcionário");
        $relatorioFuncionario->setUrl("/relatorios/relatorio-funcionario");
        $relatorioFuncionario->setModuloPai($relatorios);
        $relatorioFuncionario->setEntity('App\\Entity\\Principal\\Relatorio');
        $modulos[] = $relatorioFuncionario;
        $manager->persist($relatorioFuncionario);
        $this->addReference(self::MODULO_RELATORIO_FUNCIONARIO, $relatorioFuncionario);

        $relatorioIna = new Modulo();
        $relatorioIna->setNome("Relatório de inadimplência");
        $relatorioIna->setUrl("/relatorios/relatorio-inadimplencia");
        $relatorioIna->setModuloPai($relatorios);
        $modulos[] = $relatorioIna;
        $manager->persist($relatorioIna);
        $this->addReference(self::MODULO_RELATORIO_INADIMPLENCIA, $relatorioIna);

        $diarioClasse = new Modulo();
        $diarioClasse->setNome("Diario de classe");
        $diarioClasse->setUrl("/cadastros/diario-classe");
        $diarioClasse->setEntity('App\\Entity\\Principal\\AlunoDiario');
        $diarioClasse->setExibirNoMenu(0);
        $diarioClasse->setModuloPai($academico);
        $modulos[] = $diarioClasse;
        $manager->persist($diarioClasse);
        $this->addReference(self::MODULO_DIARIO_CLASSE, $diarioClasse);

        $bonusClass = new Modulo();
        $bonusClass->setNome("Bônus class");
        $bonusClass->setUrl("/academico/bonus-class");
        $bonusClass->setModuloPai($academico);
        $modulos[] = $bonusClass;
        $manager->persist($bonusClass);
        $this->addReference(self::MODULO_BONUS_CLASS, $bonusClass);

        $followUp = new Modulo();
        $followUp->setNome("Follow-up");
        $followUp->setUrl("/comercial/follow-up");
        $followUp->setModuloPai($comercial);
        $followUp->setEntity('App\\Entity\\Principal\\FollowupComercial');
        $modulos[] = $followUp;
        $manager->persist($followUp);
        $this->addReference(self::MODULO_FOLLOW_UP, $followUp);
        $this->addReference(self::MODULO_FOLLOWUP_COMERCIAL, $followUp);

        $personal = new Modulo();
        $personal->setNome("Diário de aulas personal");
        $personal->setUrl("/academico/personal");
        $personal->setModuloPai($academico);
        $modulos[] = $personal;
        $manager->persist($personal);
        $this->addReference(self::MODULO_PERSONAL, $personal);

        $paramFinanceiros = new Modulo();
        $paramFinanceiros->setNome("Parâmetros financeiros");
        $paramFinanceiros->setUrl("/financeiro/parametros-financeiros");
        $paramFinanceiros->setModuloPai($financeiro);
        $paramFinanceiros->setApenasFranqueadora(true);
        $modulos[] = $paramFinanceiros;
        $manager->persist($paramFinanceiros);
        $this->addReference(self::MODULO_PARAMETROS_FINANCEIROS, $paramFinanceiros);

        $reagendamentoPersonal = new Modulo();
        $reagendamentoPersonal->setNome("Reagendamento Personal");
        $reagendamentoPersonal->setUrl("/academico/reagendamento-personal");
        $reagendamentoPersonal->setModuloPai($academico);
        $modulos[] = $reagendamentoPersonal;
        $manager->persist($reagendamentoPersonal);
        $this->addReference(self::MODULO_REAGENDAMENTO_PERSONAL, $reagendamentoPersonal);

        $indicadores = new Modulo();
        $indicadores->setNome("Indicadores");
        $indicadores->setUrl("/franqueadora/indicadores");
        $indicadores->setModuloPai($configFranqueadora);
        $modulos[] = $indicadores;
        $manager->persist($indicadores);
        $this->addReference(self::MODULO_INDICADORES, $indicadores);

        $indicadoresFranquia = new Modulo();
        $indicadoresFranquia->setNome("Indicadores");
        $indicadoresFranquia->setUrl("/comercial/indicadores");
        $indicadoresFranquia->setModuloPai($comercial);
        $modulos[] = $indicadoresFranquia;
        $manager->persist($indicadoresFranquia);
        $this->addReference(self::MODULO_INDICADORES_FRANQUIA, $indicadoresFranquia);

        $renegociacao = new Modulo();
        $renegociacao->setNome("Renegociação");
        $renegociacao->setUrl("/m/Renegociacao");
        $renegociacao->setExibirNoMenu(false);
        $renegociacao->setEntity('App\\Entity\\Principal\\Renegociacao');
        $renegociacao->setModuloPai($financeiro);
        $modulos[] = $renegociacao;
        $manager->persist($renegociacao);
        $this->addReference(self::MODULO_RENEGOCIACAO, $renegociacao);

        $tituloReceber = new Modulo();
        $tituloReceber->setNome("Títulos a receber");
        $tituloReceber->setUrl("/m/TituloReceber");
        $tituloReceber->setExibirNoMenu(false);
        $tituloReceber->setEntity('App\\Entity\\Principal\\TituloReceber');
        $tituloReceber->setSituacao('I');
        $modulos[] = $tituloReceber;
        $manager->persist($tituloReceber);

        $notificacoes = new Modulo();
        $notificacoes->setNome("Notificações");
        $notificacoes->setUrl("/m/Notificacoes");
        $notificacoes->setExibirNoMenu(false);
        $notificacoes->setEntity('App\\Entity\\Principal\\Notificacoes');
        $notificacoes->setSituacao("I");
        $modulos[] = $notificacoes;
        $manager->persist($notificacoes);

        $idioma = new Modulo();
        $idioma->setNome("Idioma");
        $idioma->setUrl("/m/Idioma");
        $idioma->setExibirNoMenu(false);
        $idioma->setEntity('App\\Entity\\Principal\\Idioma');
        $idioma->setModuloPai($config);
        $modulos[] = $idioma;
        $manager->persist($idioma);

        $livroBiblioteca = new Modulo();
        $livroBiblioteca->setNome("Biblioteca");
        $livroBiblioteca->setUrl("/m/LivroBiblioteca");
        $livroBiblioteca->setExibirNoMenu(true);
        $livroBiblioteca->setEntity('App\\Entity\\Principal\\LivroBiblioteca');
        $livroBiblioteca->setModuloPai($cadastros);
        $livroBiblioteca->setSituacao('I');
        $modulos[] = $livroBiblioteca;
        $manager->persist($livroBiblioteca);
        $this->addReference(self::MODULO_BIBLIOTECA, $livroBiblioteca);

        $exemplarLivroBiblioteca = new Modulo();
        $exemplarLivroBiblioteca->setNome("Exemplares biblioteca");
        $exemplarLivroBiblioteca->setUrl("/m/LivroBibliotecaExemplar");
        $exemplarLivroBiblioteca->setExibirNoMenu(false);
        $exemplarLivroBiblioteca->setEntity('App\\Entity\\Principal\\LivroBibliotecaExemplar');
        $exemplarLivroBiblioteca->setModuloPai($academico);
        $exemplarLivroBiblioteca->setSituacao('I');
        $modulos[] = $exemplarLivroBiblioteca;
        $manager->persist($exemplarLivroBiblioteca);

        $emprestimoLivroBiblioteca = new Modulo();
        $emprestimoLivroBiblioteca->setNome("Empréstimos biblioteca");
        $emprestimoLivroBiblioteca->setUrl("/m/EmprestimoBiblioteca");
        $emprestimoLivroBiblioteca->setExibirNoMenu(true);
        $emprestimoLivroBiblioteca->setEntity('App\\Entity\\Principal\\EmprestimoBiblioteca');
        $emprestimoLivroBiblioteca->setModuloPai($academico);
        $emprestimoLivroBiblioteca->setSituacao('I');
        $modulos[] = $emprestimoLivroBiblioteca;
        $manager->persist($emprestimoLivroBiblioteca);
        $this->addReference(self::MODULO_EMPRESTIMO_BIBLIOTECA, $emprestimoLivroBiblioteca);

        $cadastrosServico = new Modulo();
        $cadastrosServico->setNome("Serviços");
        $cadastrosServico->setUrl("/franqueadora/cadastro-servico");
        $cadastrosServico->setExibirNoMenu(true);
        $cadastrosServico->setModuloPai($configFranqueadora);
        $modulos[] = $cadastrosServico;
        $manager->persist($cadastrosServico);
        $this->addReference(self::MODULO_SERVICOS, $cadastrosServico);

        $moduloMidia = new Modulo();
        $moduloMidia->setNome("Mídia pesquisa visibilidade");
        $moduloMidia->setUrl("/configuracoes/midia");
        $moduloMidia->setExibirNoMenu(true);
        $moduloMidia->setModuloPai($config);
        $modulos[] = $moduloMidia;
        $manager->persist($moduloMidia);
        $this->addReference(self::MODULO_MIDIA, $moduloMidia);

        foreach ($modulos as $modulo) {
            $modulo->addAcaoSistema($this->getReference(AcaoSistemaFixtures::ACAO_LISTAR));
            $modulo->addAcaoSistema($this->getReference(AcaoSistemaFixtures::ACAO_CRIAR));
            $modulo->addAcaoSistema($this->getReference(AcaoSistemaFixtures::ACAO_EDITAR));
            $modulo->addAcaoSistema($this->getReference(AcaoSistemaFixtures::ACAO_EXCLUIR));
        }

        $aluno->addAcaoSistema($this->getReference(AcaoSistemaFixtures::ACAO_TRANSFERENCIA_TURMA));
        $interessado->addAcaoSistema($this->getReference(AcaoSistemaFixtures::ACAO_CONSULTOR_FUNCIONARIO));
        $contrato->addAcaoSistema($this->getReference(AcaoSistemaFixtures::ACAO_DESCONTO_SUPERVISIONADO));
        $entregaMaterial->addAcaoSistema($this->getReference(AcaoSistemaFixtures::ACAO_ENTREGA_ITEM_PERMISSAO));
        $reagendamentoPersonal->addAcaoSistema($this->getReference(AcaoSistemaFixtures::ACAO_SEGUNDO_REAGENDAMENTO));

        $manager->flush();
    }


    public function getDependencies()
    {
        return [
            AcaoSistemaFixtures::class,
        ];
    }


}
