<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Principal\ModuloPapelAcao;

class ModuloPapelAcaoFixtures extends Fixture implements DependentFixtureInterface
{
    public const PAPEL_ACAO_ATENDENTE = "papel_acao_atendente";
    public const PAPEL_ACAO_COORDENADOR_PEDAGOGICO = "PAPEL_COORDENADOR";
    public const PAPEL_ACAO_USUARIO = "papel_acao_usuario";

    public function load(ObjectManager $manager)
    {
        $moduloRepository = $manager->getRepository(\App\Entity\Principal\Modulo::class);
        $todosModulos     = $moduloRepository->findAll();

        // Papel Consultor
        $PAPEL_CONSULTOR = $this->getReference(PapelFixtures::PAPEL_CONSULTOR);

        $listaAcoes[] = $this->getReference(AcaoSistemaFixtures::ACAO_CRIAR);
        $listaAcoes[] = $this->getReference(AcaoSistemaFixtures::ACAO_ACESSAR);
        $listaAcoes[] = $this->getReference(AcaoSistemaFixtures::ACAO_EDITAR);
        // $listaAcoes[] = $this->getReference(AcaoSistemaFixtures::ACAO_DESCONTO_SUPERVISIONADO);
        // $listaAcoes[] = $this->getReference(AcaoSistemaFixtures::ACAO_TRANSFERENCIA_TURMA);
        // $listaAcoes[] = $this->getReference(AcaoSistemaFixtures::ACAO_CONSULTOR_FUNCIONARIO);
        $listaModulosConsultor[] = $this->getReference(ModuloFixtures::MODULO_DASHBOARD);
        $listaModulosConsultor[] = $this->getReference(ModuloFixtures::MODULO_ACADEMICO);
        $listaModulosConsultor[] = $this->getReference(ModuloFixtures::MODULO_CADASTROS);
        $listaModulosConsultor[] = $this->getReference(ModuloFixtures::MODULO_COMERCIAL);
        $listaModulosConsultor[] = $this->getReference(ModuloFixtures::MODULO_CONFIGURACOES);
        $listaModulosConsultor[] = $this->getReference(ModuloFixtures::MODULO_FINANCEIRO);
        $listaModulosConsultor[] = $this->getReference(ModuloFixtures::MODULO_RELATORIOS);
        $listaModulosConsultor[] = $this->getReference(ModuloFixtures::MODULO_ATIVIDADE_EXTRA);
        $listaModulosConsultor[] = $this->getReference(ModuloFixtures::MODULO_NIVELAMENTO);
        $listaModulosConsultor[] = $this->getReference(ModuloFixtures::MODULO_OCORRENCIA_ACADEMICA);
        $listaModulosConsultor[] = $this->getReference(ModuloFixtures::MODULO_REPOSICAO_DE_AULA_AVALIACAO);
        $listaModulosConsultor[] = $this->getReference(ModuloFixtures::MODULO_ALUNO_REFERENCE);
        $listaModulosConsultor[] = $this->getReference(ModuloFixtures::MODULO_CONTRATO);
        $listaModulosConsultor[] = $this->getReference(ModuloFixtures::MODULO_ENTREGA_MATERIAL);
        $listaModulosConsultor[] = $this->getReference(ModuloFixtures::MODULO_INTERESSADO);
        $listaModulosConsultor[] = $this->getReference(ModuloFixtures::MODULO_CONVENIO);
        $listaModulosConsultor[] = $this->getReference(ModuloFixtures::MODULO_PESSOA);
        $listaModulosConsultor[] = $this->getReference(ModuloFixtures::MODULO_TURMAS);
        $listaModulosConsultor[] = $this->getReference(ModuloFixtures::MODULO_FOLLOWUP_COMERCIAL);
        $listaModulosConsultor[] = $this->getReference(ModuloFixtures::MODULO_SALA_FRANQUEADA);
        $listaModulosConsultor[] = $this->getReference(ModuloFixtures::MODULO_CONTAS_RECEBER_REFERENCE);
        $listaModulosConsultor[] = $this->getReference(ModuloFixtures::MODULO_GERAR_MALA_DIRETA);

        $listaModulosConsultorAcessar[] = $this->getReference(ModuloFixtures::MODULO_CADASTROS_LISTAGEM_CONVENIO_REGIONAL_REFERENCE);
        $listaModulosConsultorAcessar[] = $this->getReference(ModuloFixtures::MODULO_CONTA);
        $listaModulosConsultorAcessar[] = $this->getReference(ModuloFixtures::MODULO_FORMA_PAGAMENTO);
        $listaModulosConsultorAcessar[] = $this->getReference(ModuloFixtures::MODULO_HORARIO);

        for ($i = 0; $i < count($listaModulosConsultor); $i++) {
            for ($p = 0; $p < count($listaAcoes);$p++) {
                $config = new ModuloPapelAcao();
                $config->setModulo($listaModulosConsultor[$i]);
                $config->setPapel($PAPEL_CONSULTOR);
                $config->setAcaoSistema($listaAcoes[$p]);
                $manager->persist($config);
            }
        }

        for ($i = 0; $i < count($listaModulosConsultorAcessar); $i++) {
            $config = new ModuloPapelAcao();
            $config->setModulo($listaModulosConsultorAcessar[$i]);
            $config->setPapel($PAPEL_CONSULTOR);
            $config->setAcaoSistema($this->getReference(AcaoSistemaFixtures::ACAO_ACESSAR));
            $manager->persist($config);
        }

        $manager->flush();

        // Papel Coordenador
        $PAPEL_COORDENADOR = $this->getReference(PapelFixtures::PAPEL_COORDENADOR);

        $listaAcoesCoordenador[] = $this->getReference(AcaoSistemaFixtures::ACAO_CRIAR);
        $listaAcoesCoordenador[] = $this->getReference(AcaoSistemaFixtures::ACAO_ACESSAR);
        $listaAcoesCoordenador[] = $this->getReference(AcaoSistemaFixtures::ACAO_EDITAR);
        // $listaAcoesCoordenador[] = $this->getReference(AcaoSistemaFixtures::ACAO_EXCLUIR);
        // $listaAcoesCoordenador[] = $this->getReference(AcaoSistemaFixtures::ACAO_DESCONTO_SUPERVISIONADO);
        // $listaAcoesCoordenador[] = $this->getReference(AcaoSistemaFixtures::ACAO_TRANSFERENCIA_TURMA);
        // $listaAcoesCoordenador[] = $this->getReference(AcaoSistemaFixtures::ACAO_CONSULTOR_FUNCIONARIO);
        $listaModulosCoordenador[] = $this->getReference(ModuloFixtures::MODULO_DASHBOARD);
        $listaModulosCoordenador[] = $this->getReference(ModuloFixtures::MODULO_ACADEMICO);
        $listaModulosCoordenador[] = $this->getReference(ModuloFixtures::MODULO_CADASTROS);
        $listaModulosCoordenador[] = $this->getReference(ModuloFixtures::MODULO_COMERCIAL);
        $listaModulosCoordenador[] = $this->getReference(ModuloFixtures::MODULO_CONFIGURACOES);
        $listaModulosCoordenador[] = $this->getReference(ModuloFixtures::MODULO_FINANCEIRO);
        $listaModulosCoordenador[] = $this->getReference(ModuloFixtures::MODULO_RELATORIOS);
        $listaModulosCoordenador[] = $this->getReference(ModuloFixtures::MODULO_ATIVIDADE_EXTRA);
        $listaModulosCoordenador[] = $this->getReference(ModuloFixtures::MODULO_NIVELAMENTO);
        $listaModulosCoordenador[] = $this->getReference(ModuloFixtures::MODULO_OCORRENCIA_ACADEMICA);
        $listaModulosCoordenador[] = $this->getReference(ModuloFixtures::MODULO_REPOSICAO_DE_AULA_AVALIACAO);
        $listaModulosCoordenador[] = $this->getReference(ModuloFixtures::MODULO_ALUNO_REFERENCE);
        $listaModulosCoordenador[] = $this->getReference(ModuloFixtures::MODULO_CONTRATO);
        $listaModulosCoordenador[] = $this->getReference(ModuloFixtures::MODULO_ENTREGA_MATERIAL);
        $listaModulosCoordenador[] = $this->getReference(ModuloFixtures::MODULO_FUNCIONARIO);
        $listaModulosCoordenador[] = $this->getReference(ModuloFixtures::MODULO_INTERESSADO);
        $listaModulosCoordenador[] = $this->getReference(ModuloFixtures::MODULO_PESSOA);
        $listaModulosCoordenador[] = $this->getReference(ModuloFixtures::MODULO_TURMAS);
        $listaModulosCoordenador[] = $this->getReference(ModuloFixtures::MODULO_FOLLOWUP_COMERCIAL);
        $listaModulosCoordenador[] = $this->getReference(ModuloFixtures::MODULO_DATA_CALENDARIO);
        $listaModulosCoordenador[] = $this->getReference(ModuloFixtures::MODULO_CONTA);
        $listaModulosCoordenador[] = $this->getReference(ModuloFixtures::MODULO_HORARIO);
        $listaModulosCoordenador[] = $this->getReference(ModuloFixtures::MODULO_SALA_FRANQUEADA);
        $listaModulosCoordenador[] = $this->getReference(ModuloFixtures::MODULO_VALOR_HORA);
        $listaModulosCoordenador[] = $this->getReference(ModuloFixtures::MODULO_DIARIO_CLASSE);
        $listaModulosCoordenador[] = $this->getReference(ModuloFixtures::MODULO_PERSONAL);
        $listaModulosCoordenador[] = $this->getReference(ModuloFixtures::MODULO_CONTAS_RECEBER_REFERENCE);
        $listaModulosCoordenador[] = $this->getReference(ModuloFixtures::MODULO_GERAR_MALA_DIRETA);
        $listaModulosCoordenador[] = $this->getReference(ModuloFixtures::MODULO_CRONOGRAMA);
        // $listaModulosCoordenador[] = $this->getReference(ModuloFixtures::MODULO_SERVICO);
        for ($i = 0; $i < count($listaModulosCoordenador); $i++) {
            for ($p = 0; $p < count($listaAcoesCoordenador);$p++) {
                $config = new ModuloPapelAcao();
                $config->setModulo($listaModulosCoordenador[$i]);
                $config->setPapel($PAPEL_COORDENADOR);
                $config->setAcaoSistema($listaAcoesCoordenador[$p]);
                $manager->persist($config);
            }
        }

        // Modulos que só tem listar para Coordenador
        $listaModulosCoordenadorAcessar[] = $this->getReference(ModuloFixtures::MODULO_CADASTROS_LISTAGEM_CONVENIO_REGIONAL_REFERENCE);
        $listaModulosCoordenadorAcessar[] = $this->getReference(ModuloFixtures::MODULO_FORMA_PAGAMENTO);
        $listaModulosCoordenadorAcessar[] = $this->getReference(ModuloFixtures::MODULO_LOG);
        $listaModulosCoordenadorAcessar[] = $this->getReference(ModuloFixtures::MODULO_USUARIO);

        for ($i = 0; $i < count($listaModulosCoordenadorAcessar); $i++) {
            $config = new ModuloPapelAcao();
            $config->setModulo($listaModulosCoordenadorAcessar[$i]);
            $config->setPapel($PAPEL_COORDENADOR);
            $config->setAcaoSistema($this->getReference(AcaoSistemaFixtures::ACAO_ACESSAR));
            $manager->persist($config);
        }

        // Modulos que só tem remover para Coordenador
        $listaModulosCoordenadorRemover[] = $this->getReference(ModuloFixtures::MODULO_ATIVIDADE_EXTRA);
        $listaModulosCoordenadorRemover[] = $this->getReference(ModuloFixtures::MODULO_REPOSICAO_DE_AULA_AVALIACAO);
        $listaModulosCoordenadorRemover[] = $this->getReference(ModuloFixtures::MODULO_ALUNO_REFERENCE);
        $listaModulosCoordenadorRemover[] = $this->getReference(ModuloFixtures::MODULO_CONTRATO);
        $listaModulosCoordenadorRemover[] = $this->getReference(ModuloFixtures::MODULO_FUNCIONARIO);
        $listaModulosCoordenadorRemover[] = $this->getReference(ModuloFixtures::MODULO_TURMAS);
        $listaModulosCoordenadorRemover[] = $this->getReference(ModuloFixtures::MODULO_HORARIO);
        $listaModulosCoordenadorRemover[] = $this->getReference(ModuloFixtures::MODULO_SALA_FRANQUEADA);
        $listaModulosCoordenadorRemover[] = $this->getReference(ModuloFixtures::MODULO_VALOR_HORA);
        $listaModulosCoordenadorRemover[] = $this->getReference(ModuloFixtures::MODULO_DIARIO_CLASSE);
        $listaModulosCoordenadorRemover[] = $this->getReference(ModuloFixtures::MODULO_CRONOGRAMA);

        for ($i = 0; $i < count($listaModulosCoordenadorRemover); $i++) {
            $config = new ModuloPapelAcao();
            $config->setModulo($listaModulosCoordenadorRemover[$i]);
            $config->setPapel($PAPEL_COORDENADOR);
            $config->setAcaoSistema($this->getReference(AcaoSistemaFixtures::ACAO_EXCLUIR));
            $manager->persist($config);
        }

        $manager->flush();

        // Papel Gerente
        $PAPEL_GERENTE = $this->getReference(PapelFixtures::PAPEL_GERENTE);

        $listaAcoesGerente[] = $this->getReference(AcaoSistemaFixtures::ACAO_CRIAR);
        $listaAcoesGerente[] = $this->getReference(AcaoSistemaFixtures::ACAO_ACESSAR);
        $listaAcoesGerente[] = $this->getReference(AcaoSistemaFixtures::ACAO_EDITAR);
        $listaAcoesGerente[] = $this->getReference(AcaoSistemaFixtures::ACAO_EXCLUIR);
        $listaAcoesGerente[] = $this->getReference(AcaoSistemaFixtures::ACAO_DESCONTO_SUPERVISIONADO);
        // $listaAcoesGerente[] = $this->getReference(AcaoSistemaFixtures::ACAO_TRANSFERENCIA_TURMA);
        // $listaAcoesGerente[] = $this->getReference(AcaoSistemaFixtures::ACAO_CONSULTOR_FUNCIONARIO);
        $listaAcoesGerente[] = $this->getReference(AcaoSistemaFixtures::ACAO_SEGUNDO_REAGENDAMENTO);
        $listaAcoesGerente[] = $this->getReference(AcaoSistemaFixtures::ACAO_ENTREGA_ITEM_PERMISSAO);

        $listaModulosGerente[] = $this->getReference(ModuloFixtures::MODULO_DASHBOARD);
        $listaModulosGerente[] = $this->getReference(ModuloFixtures::MODULO_ACADEMICO);
        $listaModulosGerente[] = $this->getReference(ModuloFixtures::MODULO_CADASTROS);
        $listaModulosGerente[] = $this->getReference(ModuloFixtures::MODULO_COMERCIAL);
        $listaModulosGerente[] = $this->getReference(ModuloFixtures::MODULO_CONFIGURACOES);
        $listaModulosGerente[] = $this->getReference(ModuloFixtures::MODULO_FINANCEIRO);
        $listaModulosGerente[] = $this->getReference(ModuloFixtures::MODULO_RELATORIOS);
        $listaModulosGerente[] = $this->getReference(ModuloFixtures::MODULO_ATIVIDADE_EXTRA);
        $listaModulosGerente[] = $this->getReference(ModuloFixtures::MODULO_NIVELAMENTO);
        $listaModulosGerente[] = $this->getReference(ModuloFixtures::MODULO_OCORRENCIA_ACADEMICA);
        $listaModulosGerente[] = $this->getReference(ModuloFixtures::MODULO_REPOSICAO_DE_AULA_AVALIACAO);
        $listaModulosGerente[] = $this->getReference(ModuloFixtures::MODULO_ALUNO_REFERENCE);
        $listaModulosGerente[] = $this->getReference(ModuloFixtures::MODULO_CONTRATO);
        $listaModulosGerente[] = $this->getReference(ModuloFixtures::MODULO_ENTREGA_MATERIAL);
        $listaModulosGerente[] = $this->getReference(ModuloFixtures::MODULO_FUNCIONARIO);
        $listaModulosGerente[] = $this->getReference(ModuloFixtures::MODULO_INTERESSADO);
        $listaModulosGerente[] = $this->getReference(ModuloFixtures::MODULO_CONVENIO);
        $listaModulosGerente[] = $this->getReference(ModuloFixtures::MODULO_OPERADORA_CARTAO);
        $listaModulosGerente[] = $this->getReference(ModuloFixtures::MODULO_PESSOA);
        $listaModulosGerente[] = $this->getReference(ModuloFixtures::MODULO_TURMAS);
        $listaModulosGerente[] = $this->getReference(ModuloFixtures::MODULO_FOLLOWUP_COMERCIAL);
        $listaModulosGerente[] = $this->getReference(ModuloFixtures::MODULO_BANCO);
        $listaModulosGerente[] = $this->getReference(ModuloFixtures::MODULO_DATA_CALENDARIO);
        $listaModulosGerente[] = $this->getReference(ModuloFixtures::MODULO_CONTA);
        $listaModulosGerente[] = $this->getReference(ModuloFixtures::MODULO_HORARIO);
        $listaModulosGerente[] = $this->getReference(ModuloFixtures::MODULO_IMPORTADOR);
        $listaModulosGerente[] = $this->getReference(ModuloFixtures::MODULO_LOG);
        $listaModulosGerente[] = $this->getReference(ModuloFixtures::MODULO_PLANO_CONTA);
        $listaModulosGerente[] = $this->getReference(ModuloFixtures::MODULO_SALA_FRANQUEADA);
        $listaModulosGerente[] = $this->getReference(ModuloFixtures::MODULO_USUARIO);
        $listaModulosGerente[] = $this->getReference(ModuloFixtures::MODULO_VALOR_HORA);
        $listaModulosGerente[] = $this->getReference(ModuloFixtures::MODULO_DIARIO_CLASSE);
        $listaModulosGerente[] = $this->getReference(ModuloFixtures::MODULO_CARTOES_REFERENCE);
        $listaModulosGerente[] = $this->getReference(ModuloFixtures::MODULO_CHEQUE_REFERENCE);
        $listaModulosGerente[] = $this->getReference(ModuloFixtures::MODULO_CONTAS_PAGAR_REFERENCE);
        $listaModulosGerente[] = $this->getReference(ModuloFixtures::MODULO_CONTAS_RECEBER_REFERENCE);
        $listaModulosGerente[] = $this->getReference(ModuloFixtures::MODULO_EXTRATO);
        $listaModulosGerente[] = $this->getReference(ModuloFixtures::MODULO_GERAR_MALA_DIRETA);
        $listaModulosGerente[] = $this->getReference(ModuloFixtures::MODULO_CRONOGRAMA);
        $listaModulosGerente[] = $this->getReference(ModuloFixtures::MODULO_RELATORIO_CHEQUES);
        $listaModulosGerente[] = $this->getReference(ModuloFixtures::MODULO_RELATORIO_CONTAS_PAGAR);
        $listaModulosGerente[] = $this->getReference(ModuloFixtures::MODULO_RELATORIO_FUNCIONARIO);
        $listaModulosGerente[] = $this->getReference(ModuloFixtures::MODULO_RELATORIO_DE_BALANCETE_FINANCEIRO);
        $listaModulosGerente[] = $this->getReference(ModuloFixtures::MODULO_INDICADORES_FRANQUIA);

        // $listaModulosGerente[] = $this->getReference(ModuloFixtures::MODULO_SERVICO);
        for ($i = 0; $i < count($listaModulosGerente); $i++) {
            for ($p = 0; $p < count($listaAcoesGerente);$p++) {
                $config = new ModuloPapelAcao();
                $config->setModulo($listaModulosGerente[$i]);
                $config->setPapel($PAPEL_GERENTE);
                $config->setAcaoSistema($listaAcoesGerente[$p]);
                $manager->persist($config);
            }
        }

        $manager->flush();

        // Papel Administrador Franqueada
        $PAPEL_FRANQUEADO = $this->getReference(PapelFixtures::PAPEL_ADMINISTRADOR);

        $acaoAcessar = AcaoSistemaFixtures::ACAO_ACESSAR;
        $acaoEditar  = AcaoSistemaFixtures::ACAO_EDITAR;
        $acaoCriar   = AcaoSistemaFixtures::ACAO_CRIAR;
        $acaoExcluir = AcaoSistemaFixtures::ACAO_EXCLUIR;
        $acaoDescontoSupervisionado = AcaoSistemaFixtures::ACAO_DESCONTO_SUPERVISIONADO;
        $acaoTransferenciaTurma     = AcaoSistemaFixtures::ACAO_TRANSFERENCIA_TURMA;
        $acaoConsultorFuncionario   = AcaoSistemaFixtures::ACAO_CONSULTOR_FUNCIONARIO;
        $acaoSegundoReagendamento   = AcaoSistemaFixtures::ACAO_SEGUNDO_REAGENDAMENTO;
        $acaoEntregaItem            = AcaoSistemaFixtures::ACAO_ENTREGA_ITEM_PERMISSAO;

        $permissoesFranqueado = [
            [
                ModuloFixtures::MODULO_CONFIGURACOES,
                [ $acaoAcessar ],
            ],
            [
                ModuloFixtures::MODULO_CADASTROS,
                [ $acaoAcessar ],
            ],
            [
                ModuloFixtures::MODULO_FINANCEIRO,
                [ $acaoAcessar ],
            ],
            [
                ModuloFixtures::MODULO_RELATORIOS,
                [ $acaoAcessar ],
            ],
            [
                ModuloFixtures::MODULO_ACADEMICO,
                [ $acaoAcessar ],
            ],
            [
                ModuloFixtures::MODULO_COMERCIAL,
                [ $acaoAcessar ],
            ],
            [
                ModuloFixtures::MODULO_DASHBOARD,
                [ $acaoAcessar ],
            ],

            [
                ModuloFixtures::MODULO_MODULO,
                [ $acaoAcessar ],
            ],

            [
                ModuloFixtures::MODULO_USUARIO,
                [
                    $acaoAcessar,
                    $acaoCriar,
                    $acaoEditar,
                    $acaoExcluir,
                ],
            ],

            [
                ModuloFixtures::MODULO_PESSOA,
                [
                    $acaoAcessar,
                    $acaoCriar,
                    $acaoEditar,
                    $acaoExcluir,
                ],
            ],

            [
                ModuloFixtures::MODULO_OPERADORA_CARTAO,
                [
                    $acaoAcessar,
                    $acaoCriar,
                    $acaoEditar,
                    $acaoExcluir,
                ],
            ],

            [
                ModuloFixtures::MODULO_CONVENIO,
                [
                    $acaoAcessar,
                    $acaoCriar,
                    $acaoEditar,
                    $acaoExcluir,
                ],
            ],

            [
                ModuloFixtures::MODULO_CADASTROS_LISTAGEM_CONVENIO_REGIONAL_REFERENCE,
                [
                    $acaoAcessar,
                    $acaoCriar,
                    $acaoEditar,
                    $acaoExcluir,
                ],
            ],

            [
                ModuloFixtures::MODULO_ALUNO_REFERENCE,
                [
                    $acaoAcessar,
                    $acaoCriar,
                    $acaoEditar,
                    $acaoExcluir,
                ],
            ],

            [
                ModuloFixtures::MODULO_INTERESSADO,
                [
                    $acaoAcessar,
                    $acaoCriar,
                    $acaoEditar,
                    $acaoExcluir,
                    $acaoConsultorFuncionario,
                ],
            ],

            [
                ModuloFixtures::MODULO_HORARIO,
                [
                    $acaoAcessar,
                    $acaoCriar,
                    $acaoEditar,
                    $acaoExcluir,
                ],
            ],

            [
                ModuloFixtures::MODULO_CONTRATO,
                [
                    $acaoAcessar,
                    $acaoCriar,
                    $acaoEditar,
                    $acaoExcluir,
                    $acaoDescontoSupervisionado,
                ],
            ],
            [
                ModuloFixtures::MODULO_ENTREGA_MATERIAL,
                [
                    $acaoAcessar,
                    $acaoCriar,
                    $acaoEditar,
                    $acaoExcluir,
                    $acaoEntregaItem,
                ],
            ],
            [
                ModuloFixtures::MODULO_FUNCIONARIO,
                [
                    $acaoAcessar,
                    $acaoCriar,
                    $acaoEditar,
                    $acaoExcluir,
                ],
            ],
            [
                ModuloFixtures::MODULO_TURMAS,
                [
                    $acaoAcessar,
                    $acaoCriar,
                    $acaoEditar,
                    $acaoExcluir,
                    $acaoTransferenciaTurma,
                ],
            ],
            [
                ModuloFixtures::MODULO_CLASSIFICACAO_ALUNO,
                [
                    $acaoAcessar,
                    $acaoCriar,
                    $acaoEditar,
                    $acaoExcluir,
                ],
            ],
            [
                ModuloFixtures::MODULO_DATA_CALENDARIO,
                [
                    $acaoAcessar,
                    $acaoCriar,
                    $acaoEditar,
                    $acaoExcluir,
                ],
            ],

            [
                ModuloFixtures::MODULO_TIPO_MOVIMENTO_CONTA,
                [ $acaoAcessar ],
            ],

            [
                ModuloFixtures::MODULO_CONTA,
                [
                    $acaoAcessar,
                    $acaoCriar,
                    $acaoEditar,
                    $acaoExcluir,
                ],
            ],

            [
                ModuloFixtures::MODULO_PLANO_CONTA,
                [
                    $acaoAcessar,
                    $acaoCriar,
                    $acaoEditar,
                    $acaoExcluir,
                ],
            ],

            [
                ModuloFixtures::MODULO_CONTAS_PAGAR_REFERENCE,
                [
                    $acaoAcessar,
                    $acaoCriar,
                    $acaoEditar,
                    $acaoExcluir,
                    $acaoDescontoSupervisionado,
                ],
            ],

            [
                ModuloFixtures::MODULO_CONTAS_RECEBER_REFERENCE,
                [
                    $acaoAcessar,
                    $acaoCriar,
                    $acaoEditar,
                    $acaoExcluir,
                    $acaoDescontoSupervisionado,
                ],
            ],
            [
                ModuloFixtures::MODULO_EXTRATO,
                [
                    $acaoAcessar,
                    $acaoCriar,
                    $acaoEditar,
                    $acaoExcluir,
                    $acaoDescontoSupervisionado,
                ],
            ],
            [
                ModuloFixtures::MODULO_CHEQUE_REFERENCE,
                [
                    $acaoAcessar,
                    $acaoCriar,
                    $acaoEditar,
                    $acaoExcluir,
                    $acaoDescontoSupervisionado,
                ],
            ],
            [
                ModuloFixtures::MODULO_CARTOES_REFERENCE,
                [
                    $acaoAcessar,
                    $acaoCriar,
                    $acaoEditar,
                    $acaoExcluir,
                    $acaoDescontoSupervisionado,
                ],
            ],
            [
                ModuloFixtures::MODULO_SALA_FRANQUEADA,
                [
                    $acaoAcessar,
                    $acaoCriar,
                    $acaoEditar,
                    $acaoExcluir,
                ],
            ],
            [
                ModuloFixtures::MODULO_VALOR_HORA,
                [
                    $acaoAcessar,
                    $acaoCriar,
                    $acaoEditar,
                    $acaoExcluir,
                ],
            ],

            [
                ModuloFixtures::MODULO_RELATORIO_CHEQUES,
                [
                    $acaoAcessar,
                    $acaoCriar,
                    $acaoEditar,
                    $acaoExcluir,
                ],
            ],

            [
                ModuloFixtures::MODULO_RELATORIO_CONTAS_PAGAR,
                [
                    $acaoAcessar,
                    $acaoCriar,
                    $acaoEditar,
                    $acaoExcluir,
                ],
            ],

            [
                ModuloFixtures::MODULO_FUNIL_VENDAS,
                [
                    $acaoAcessar,
                    $acaoCriar,
                    $acaoEditar,
                    $acaoExcluir,
                ],
            ],

            [
                ModuloFixtures::MODULO_OCORRENCIA_ACADEMICA,
                [
                    $acaoAcessar,
                    $acaoCriar,
                    $acaoEditar,
                    $acaoExcluir,
                ],
            ],

            [
                ModuloFixtures::MODULO_GERAR_MALA_DIRETA,
                [
                    $acaoAcessar,
                    $acaoCriar,
                    $acaoEditar,
                    $acaoExcluir,
                ],
            ],
            [
                ModuloFixtures::MODULO_REPOSICAO_DE_AULA_AVALIACAO,
                [
                    $acaoAcessar,
                    $acaoCriar,
                    $acaoEditar,
                    $acaoExcluir,
                ],
            ],
            [
                ModuloFixtures::MODULO_RELATORIO_DE_BALANCETE_FINANCEIRO,
                [
                    $acaoAcessar,
                    $acaoCriar,
                    $acaoEditar,
                    $acaoExcluir,
                ],
            ],
            [
                ModuloFixtures::MODULO_ATIVIDADE_EXTRA,
                [
                    $acaoAcessar,
                    $acaoCriar,
                    $acaoEditar,
                    $acaoExcluir,
                ],
            ],
            [
                ModuloFixtures::MODULO_NIVELAMENTO,
                [
                    $acaoAcessar,
                    $acaoCriar,
                    $acaoEditar,
                    $acaoExcluir,
                ],
            ],
            [
                ModuloFixtures::MODULO_CRONOGRAMA,
                [
                    $acaoAcessar,
                    $acaoCriar,
                    $acaoEditar,
                    $acaoExcluir,
                ],
            ],
            [
                ModuloFixtures::MODULO_RELATORIO_FUNCIONARIO,
                [
                    $acaoAcessar,
                    $acaoCriar,
                    $acaoEditar,
                    $acaoExcluir,
                ],
            ],
            [
                ModuloFixtures::MODULO_DIARIO_CLASSE,
                [
                    $acaoAcessar,
                    $acaoCriar,
                    $acaoEditar,
                    $acaoExcluir,
                ],
            ],
            [
                ModuloFixtures::MODULO_FOLLOWUP_COMERCIAL,
                [
                    $acaoAcessar,
                    $acaoCriar,
                    $acaoEditar,
                    $acaoExcluir,
                    $acaoConsultorFuncionario,
                    $acaoSegundoReagendamento,
                ],
            ],

            [
                ModuloFixtures::MODULO_PARAMETROS_FINANCEIROS,
                [
                    $acaoAcessar,
                    $acaoCriar,
                    $acaoEditar,
                    $acaoExcluir,
                ],
            ],

            [
                ModuloFixtures::MODULO_PERSONAL,
                [
                    $acaoAcessar,
                    $acaoCriar,
                    $acaoEditar,
                    $acaoExcluir,
                    $acaoConsultorFuncionario,
                    $acaoSegundoReagendamento,
                ],
            ],

            [
                ModuloFixtures::MODULO_BIBLIOTECA,
                [
                    $acaoAcessar,
                    $acaoCriar,
                    $acaoEditar,
                    $acaoExcluir,
                ],
            ],

            [
                ModuloFixtures::MODULO_EMPRESTIMO_BIBLIOTECA,
                [
                    $acaoAcessar,
                    $acaoCriar,
                    $acaoEditar,
                    $acaoExcluir,
                ],
            ],

            [
                ModuloFixtures::MODULO_RENEGOCIACAO,
                [
                    $acaoAcessar,
                    $acaoCriar,
                    $acaoEditar,
                    $acaoExcluir,
                ],
            ],

            [
                ModuloFixtures::MODULO_SERVICOS,
                [
                    $acaoAcessar,
                    $acaoCriar,
                    $acaoEditar,
                    $acaoExcluir,
                    $acaoConsultorFuncionario,
                    $acaoSegundoReagendamento,
                ],
            ],

            [
                ModuloFixtures::MODULO_AGENDA_COMPROMISSO,
                [
                    $acaoAcessar,
                    $acaoCriar,
                    $acaoEditar,
                    $acaoExcluir,
                ],
            ],

            [
                ModuloFixtures::MODULO_BONUS_CLASS,
                [
                    $acaoAcessar,
                    $acaoCriar,
                    $acaoEditar,
                    $acaoExcluir,
                ],
            ],

            [
                ModuloFixtures::MODULO_REAGENDAMENTO_PERSONAL,
                [
                    $acaoAcessar,
                    $acaoCriar,
                    $acaoEditar,
                    $acaoExcluir,
                    $acaoConsultorFuncionario,
                    $acaoSegundoReagendamento,
                ],
            ],

            [
                ModuloFixtures::MODULO_RELATORIO_INADIMPLENCIA,
                [
                    $acaoAcessar,
                    $acaoCriar,
                    $acaoEditar,
                    $acaoExcluir,
                    $acaoConsultorFuncionario,
                    $acaoSegundoReagendamento,
                ],
            ],

            [
                ModuloFixtures::MODULO_INDICADORES_FRANQUIA,
                [
                    $acaoAcessar,
                    $acaoCriar,
                    $acaoEditar,
                    $acaoExcluir,
                ],
            ],
        ];

        foreach ($permissoesFranqueado as $permissao) {
            $moduloRef = $permissao[0];
            $acoes     = $permissao[1];

            foreach ($acoes as $acaoRef) {
                $modulo = new ModuloPapelAcao();
                $modulo->setModulo($this->getReference($moduloRef));
                $modulo->setPapel($PAPEL_FRANQUEADO);
                $modulo->setAcaoSistema($this->getReference($acaoRef));
                $manager->persist($modulo);
            }
        }

        $manager->flush();

        $moduloORM = null;

        // Papel Administrador Franqueadora
        foreach ($todosModulos as $moduloORM) {
            $moduloPapelAcaoCriar = new ModuloPapelAcao();
            $moduloORM->addModuloPapelAcao($moduloPapelAcaoCriar);
            $this->getReference(AcaoSistemaFixtures::ACAO_CRIAR)->addModuloPapelAcao($moduloPapelAcaoCriar);
            $this->getReference(PapelFixtures::PAPEL_ADMINISTRADOR_FRANQUEADORA)->addModuloPapelAcao($moduloPapelAcaoCriar);
            $manager->persist($moduloPapelAcaoCriar);

            $moduloPapelAcaoEditar = new ModuloPapelAcao();
            $moduloORM->addModuloPapelAcao($moduloPapelAcaoEditar);
            $this->getReference(AcaoSistemaFixtures::ACAO_EDITAR)->addModuloPapelAcao($moduloPapelAcaoEditar);
            $this->getReference(PapelFixtures::PAPEL_ADMINISTRADOR_FRANQUEADORA)->addModuloPapelAcao($moduloPapelAcaoEditar);
            $manager->persist($moduloPapelAcaoEditar);

            $moduloPapelAcaoExcluir = new ModuloPapelAcao();
            $moduloORM->addModuloPapelAcao($moduloPapelAcaoExcluir);
            $this->getReference(AcaoSistemaFixtures::ACAO_EXCLUIR)->addModuloPapelAcao($moduloPapelAcaoExcluir);
            $this->getReference(PapelFixtures::PAPEL_ADMINISTRADOR_FRANQUEADORA)->addModuloPapelAcao($moduloPapelAcaoExcluir);
            $manager->persist($moduloPapelAcaoExcluir);

            $moduloPapelAcaoListar = new ModuloPapelAcao();
            $moduloORM->addModuloPapelAcao($moduloPapelAcaoListar);
            $this->getReference(AcaoSistemaFixtures::ACAO_ACESSAR)->addModuloPapelAcao($moduloPapelAcaoListar);
            $this->getReference(PapelFixtures::PAPEL_ADMINISTRADOR_FRANQUEADORA)->addModuloPapelAcao($moduloPapelAcaoListar);
            $manager->persist($moduloPapelAcaoListar);

            $moduloPapelAcaoTransferenciaTurma = new ModuloPapelAcao();
            $moduloORM->addModuloPapelAcao($moduloPapelAcaoTransferenciaTurma);
            $this->getReference(AcaoSistemaFixtures::ACAO_TRANSFERENCIA_TURMA)->addModuloPapelAcao($moduloPapelAcaoTransferenciaTurma);
            $this->getReference(PapelFixtures::PAPEL_ADMINISTRADOR_FRANQUEADORA)->addModuloPapelAcao($moduloPapelAcaoTransferenciaTurma);
            $manager->persist($moduloPapelAcaoTransferenciaTurma);

            $moduloPapelAcaoConsultorFuncionario = new ModuloPapelAcao();
            $moduloORM->addModuloPapelAcao($moduloPapelAcaoConsultorFuncionario);
            $this->getReference(AcaoSistemaFixtures::ACAO_CONSULTOR_FUNCIONARIO)->addModuloPapelAcao($moduloPapelAcaoConsultorFuncionario);
            $this->getReference(PapelFixtures::PAPEL_ADMINISTRADOR_FRANQUEADORA)->addModuloPapelAcao($moduloPapelAcaoConsultorFuncionario);
            $manager->persist($moduloPapelAcaoConsultorFuncionario);

            $moduloPapelAcaoDescontoSupervisionado = new ModuloPapelAcao();
            $moduloORM->addModuloPapelAcao($moduloPapelAcaoDescontoSupervisionado);
            $this->getReference(AcaoSistemaFixtures::ACAO_DESCONTO_SUPERVISIONADO)->addModuloPapelAcao($moduloPapelAcaoDescontoSupervisionado);
            $this->getReference(PapelFixtures::PAPEL_ADMINISTRADOR_FRANQUEADORA)->addModuloPapelAcao($moduloPapelAcaoDescontoSupervisionado);
            $manager->persist($moduloPapelAcaoDescontoSupervisionado);

            // Adiciona a permissão de LISTAR para todos os papeis.
            // A ação de listar serve para executar GET nos módulos, a permissão acessar tomou a função de poder acessar o módulo.
            $moduloPapelAcaoAcessar = new ModuloPapelAcao();
            $moduloPapelAcaoAcessar->setModulo($moduloORM);
            $moduloPapelAcaoAcessar->setPapel($this->getReference(PapelFixtures::PAPEL_ADMINISTRADOR_FRANQUEADORA));
            $moduloPapelAcaoAcessar->setAcaoSistema($this->getReference(AcaoSistemaFixtures::ACAO_LISTAR));
            $manager->persist($moduloPapelAcaoAcessar);

            $moduloPapelAcaoAcessar = new ModuloPapelAcao();
            $moduloPapelAcaoAcessar->setModulo($moduloORM);
            $moduloPapelAcaoAcessar->setPapel($PAPEL_FRANQUEADO);
            $moduloPapelAcaoAcessar->setAcaoSistema($this->getReference(AcaoSistemaFixtures::ACAO_LISTAR));
            $manager->persist($moduloPapelAcaoAcessar);

            $moduloPapelAcaoAcessar = new ModuloPapelAcao();
            $moduloPapelAcaoAcessar->setModulo($moduloORM);
            $moduloPapelAcaoAcessar->setPapel($PAPEL_GERENTE);
            $moduloPapelAcaoAcessar->setAcaoSistema($this->getReference(AcaoSistemaFixtures::ACAO_LISTAR));
            $manager->persist($moduloPapelAcaoAcessar);

            $moduloPapelAcaoAcessar = new ModuloPapelAcao();
            $moduloPapelAcaoAcessar->setModulo($moduloORM);
            $moduloPapelAcaoAcessar->setPapel($PAPEL_CONSULTOR);
            $moduloPapelAcaoAcessar->setAcaoSistema($this->getReference(AcaoSistemaFixtures::ACAO_LISTAR));
            $manager->persist($moduloPapelAcaoAcessar);

            $moduloPapelAcaoAcessar = new ModuloPapelAcao();
            $moduloPapelAcaoAcessar->setModulo($moduloORM);
            $moduloPapelAcaoAcessar->setPapel($PAPEL_COORDENADOR);
            $moduloPapelAcaoAcessar->setAcaoSistema($this->getReference(AcaoSistemaFixtures::ACAO_LISTAR));
            $manager->persist($moduloPapelAcaoAcessar);

            $moduloORM->addAcaoSistema($this->getReference(AcaoSistemaFixtures::ACAO_ACESSAR));
        }//end foreach

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            AcaoSistemaFixtures::class,
            PapelFixtures::class,
            ModuloFixtures::class,
        ];
    }


}
