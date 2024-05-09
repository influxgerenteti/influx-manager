<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\TipoItem;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TipoItemFixtures extends Fixture implements DependentFixtureInterface
{
    public const TIPO_ITEM_PRODUTO_REFFERENCE           = "tipoItemProdutoP";
    public const TIPO_ITEM_TAXA_MATRICULA_REFFERENCE    = "tipoItemTaxaMatriculaM";
    public const TIPO_ITEM_VALOR_CURSO_REFFERENCE       = "tipoItemValorCursoV";
    public const TIPO_ITEM_ATIVIDADES_EXTRAS_REFFERENCE = "tipoItemAtivdadesExtrasAE";
    public const TIPO_ITEM_REPOSICAO_AULA_REFFERENCE    = "tipoItemReposicaoAulaR";
    public const TIPO_MAKE_UP_TEST_REFFERENCE           = "tipoItemMakeUpTest";
    public const TIPO_ITEM_REPOSICAO_AVALIACAO_MAKE_UP_MID_TERM_REFFERENCE = "tipoItemReposicaoAvaliacaoMT";
    public const TIPO_ITEM_REPOSICAO_AVALIACAO_MAKE_UP_FINAL_REFFERENCE    = "tipoItemReposicaoAvaliacaoMF";
    public const TIPO_ITEM_REPOSICAO_AVALIACAO_RETAKE_MID_TERM_REFFERENCE  = "tipoItemReposicaoAvaliacaoRM";
    public const TIPO_ITEM_REPOSICAO_AVALIACAO_RETAKE_FINAL_REFFERENCE     = "tipoItemReposicaoAvaliacaoRF";
    public const TIPO_ITEM_SOLICITACAO_NIVELAMENTO_REFFERENCE = "tipoItemSolicitacaoNivelamentoSN";
    public const TIPO_ITEM_COBRANCA_REFFERENCE = "tipoItemCobrancasCB";
    public const TIPO_ITEM_SERVICO_REFFERENCE  = "tipoItemServicoS";
    public const TIPO_ITEM_TRANSFERENCIA_TURMA_REFFERENCE = "tipoItemTransferenciaTurma";
    public const TIPO_ITEM_VC_PERS_32_REFFERENCE          = "tipoItemValorCurso32Creditos";
    public const TIPO_ITEM_VC_PERS_48_REFFERENCE          = "tipoItemValorCurso48Creditos";
    public const TIPO_ITEM_VC_PERS_64_REFFERENCE          = "tipoItemValorCurso64Creditos";
    public const TIPO_ITEM_FALTA     = "tipoItemFalta";
    public const TIPO_ITEM_AVALIACAO = "tipoItemAvaliacao";
    public const TIPO_ITEM_ACOMPANHAMENTO_PEDAGOGICO = "tipoItemAcompanhamentoPedagogico";
    public const TIPO_ITEM_VC_PERS_AVULSO_REFFERENCE = "tipoItemValorCursoAvulsoCreditos";

    public function load(ObjectManager $manager)
    {
        $tipoItemProduto = new TipoItem();
        $tipoItemProduto->setDescricao("Produto(s)");
        $tipoItemProduto->setTipo("P");
        $manager->persist($tipoItemProduto);
        $this->addReference(self::TIPO_ITEM_PRODUTO_REFFERENCE, $tipoItemProduto);

        $tipoItemMatricula = new TipoItem();
        $tipoItemMatricula->setDescricao("Taxa de Matrícula");
        $tipoItemMatricula->setTipo("M");
        $manager->persist($tipoItemMatricula);
        $this->addReference(self::TIPO_ITEM_TAXA_MATRICULA_REFFERENCE, $tipoItemMatricula);

        $tipoItemValorCurso = new TipoItem();
        $tipoItemValorCurso->setDescricao("Valor do Curso");
        $tipoItemValorCurso->setTipo("V");
        $manager->persist($tipoItemValorCurso);
        $this->addReference(self::TIPO_ITEM_VALOR_CURSO_REFFERENCE, $tipoItemValorCurso);

        $tipoItemSolicitacaoNivelamento = new TipoItem();
        $tipoItemSolicitacaoNivelamento->setDescricao("Solicitação de Nivelamento");
        $tipoItemSolicitacaoNivelamento->setTipo("SN");
        $manager->persist($tipoItemSolicitacaoNivelamento);
        $this->addReference(self::TIPO_ITEM_SOLICITACAO_NIVELAMENTO_REFFERENCE, $tipoItemSolicitacaoNivelamento);

        $tipoItemAvaliacoes = new TipoItem();
        $tipoItemAvaliacoes->setDescricao("Avaliações");
        $tipoItemAvaliacoes->setTipo("AV");
        $tipoItemAvaliacoes->setTipoOcorrencia($this->getReference(TipoOcorrenciaFixtures::TIPO_OCORRENCIA_AVALIACOES));
        $manager->persist($tipoItemAvaliacoes);

        $tipoItemFaltas = new TipoItem();
        $tipoItemFaltas->setDescricao("Faltas");
        $tipoItemFaltas->setTipo("F");
        $tipoItemFaltas->setTipoOcorrencia($this->getReference(TipoOcorrenciaFixtures::TIPO_OCORRENCIA_FALTA));
        $manager->persist($tipoItemFaltas);

        $tipoItemAtividadesExtras = new TipoItem();
        $tipoItemAtividadesExtras->setDescricao("Atividades Extras");
        $tipoItemAtividadesExtras->setTipo("AE");
        $tipoItemAtividadesExtras->setTipoOcorrencia($this->getReference(TipoOcorrenciaFixtures::TIPO_OCORRENCIA_ATIVIDADES_EXTRAS));
        $manager->persist($tipoItemAtividadesExtras);
        $this->addReference(self::TIPO_ITEM_ATIVIDADES_EXTRAS_REFFERENCE, $tipoItemAtividadesExtras);

        $tipoItemMakeUpClass = new TipoItem();
        $tipoItemMakeUpClass->setDescricao("Make up class");
        $tipoItemMakeUpClass->setTipo("MC");
        $tipoItemMakeUpClass->setTipoOcorrencia($this->getReference(TipoOcorrenciaFixtures::TIPO_ACOMPANHAMENTO_PEDAGOGICO));
        $manager->persist($tipoItemMakeUpClass);
        $this->addReference(self::TIPO_ITEM_REPOSICAO_AULA_REFFERENCE, $tipoItemMakeUpClass);

        $tipoItemMakeUpTest = new TipoItem();
        $tipoItemMakeUpTest->setDescricao("Make Up Test");
        $tipoItemMakeUpTest->setTipo("MT");
        $tipoItemMakeUpTest->setTipoOcorrencia($this->getReference(TipoOcorrenciaFixtures::TIPO_ACOMPANHAMENTO_PEDAGOGICO));
        $manager->persist($tipoItemMakeUpTest);
        $this->addReference(self::TIPO_MAKE_UP_TEST_REFFERENCE, $tipoItemMakeUpTest);

        $tipoItemReposicaoMakeUpFinal = new TipoItem();
        $tipoItemReposicaoMakeUpFinal->setDescricao("Make-up Final");
        $tipoItemReposicaoMakeUpFinal->setTipo("MF");
        $tipoItemReposicaoMakeUpFinal->setTipoOcorrencia($this->getReference(TipoOcorrenciaFixtures::TIPO_OCORRENCIA_REPOSICOES));
        $manager->persist($tipoItemReposicaoMakeUpFinal);
        $this->addReference(self::TIPO_ITEM_REPOSICAO_AVALIACAO_MAKE_UP_FINAL_REFFERENCE, $tipoItemReposicaoMakeUpFinal);

        $tipoItemReposicaoRetakeMidTerm = new TipoItem();
        $tipoItemReposicaoRetakeMidTerm->setDescricao("Retake Mid-Term");
        $tipoItemReposicaoRetakeMidTerm->setTipo("RM");
        $tipoItemReposicaoRetakeMidTerm->setTipoOcorrencia($this->getReference(TipoOcorrenciaFixtures::TIPO_OCORRENCIA_REPOSICOES));
        $manager->persist($tipoItemReposicaoRetakeMidTerm);
        $this->addReference(self::TIPO_ITEM_REPOSICAO_AVALIACAO_RETAKE_MID_TERM_REFFERENCE, $tipoItemReposicaoRetakeMidTerm);

        $tipoItemReposicaoRetakeFinal = new TipoItem();
        $tipoItemReposicaoRetakeFinal->setDescricao("Retake FINAL");
        $tipoItemReposicaoRetakeFinal->setTipo("RF");
        $tipoItemReposicaoRetakeFinal->setTipoOcorrencia($this->getReference(TipoOcorrenciaFixtures::TIPO_OCORRENCIA_REPOSICOES));
        $manager->persist($tipoItemReposicaoRetakeFinal);
        $this->addReference(self::TIPO_ITEM_REPOSICAO_AVALIACAO_RETAKE_FINAL_REFFERENCE, $tipoItemReposicaoRetakeFinal);

        $tipoItemTransferenciaTurma = new TipoItem();
        $tipoItemTransferenciaTurma->setDescricao("Transferência de Turmas");
        $tipoItemTransferenciaTurma->setTipo("TT");
        $tipoItemTransferenciaTurma->setTipoOcorrencia($this->getReference(TipoOcorrenciaFixtures::TIPO_TRANSFERENCIA_TURMA));
        $manager->persist($tipoItemTransferenciaTurma);
        $this->addReference(self::TIPO_ITEM_TRANSFERENCIA_TURMA_REFFERENCE, $tipoItemTransferenciaTurma);

        $tipoItemCobrancas = new TipoItem();
        $tipoItemCobrancas->setDescricao("Cobranças");
        $tipoItemCobrancas->setTipo("CB");
        $tipoItemCobrancas->setTipoOcorrencia($this->getReference(TipoOcorrenciaFixtures::TIPO_OCORRENCIA_COBRANCA));
        $manager->persist($tipoItemCobrancas);
        $this->addReference(self::TIPO_ITEM_COBRANCA_REFFERENCE, $tipoItemCobrancas);

        $tipoItemServico = new TipoItem();
        $tipoItemServico->setDescricao("Serviço");
        $tipoItemServico->setTipo("S");
        $manager->persist($tipoItemServico);
        $this->addReference(self::TIPO_ITEM_SERVICO_REFFERENCE, $tipoItemServico);

        $tipoItemValorCurso32Creditos = new TipoItem();
        $tipoItemValorCurso32Creditos->setDescricao("Modalidade Personal 32 créditos");
        $tipoItemValorCurso32Creditos->setTipo("VP32");
        $manager->persist($tipoItemValorCurso32Creditos);
        $this->addReference(self::TIPO_ITEM_VC_PERS_32_REFFERENCE, $tipoItemValorCurso32Creditos);

        $tipoItemValorCurso48Creditos = new TipoItem();
        $tipoItemValorCurso48Creditos->setDescricao("Modalidade Personal 48 créditos");
        $tipoItemValorCurso48Creditos->setTipo("VP48");
        $manager->persist($tipoItemValorCurso48Creditos);
        $this->addReference(self::TIPO_ITEM_VC_PERS_48_REFFERENCE, $tipoItemValorCurso48Creditos);

        $tipoItemValorCurso64Creditos = new TipoItem();
        $tipoItemValorCurso64Creditos->setDescricao("Modalidade Personal 64 créditos");
        $tipoItemValorCurso64Creditos->setTipo("VP64");
        $manager->persist($tipoItemValorCurso64Creditos);
        $this->addReference(self::TIPO_ITEM_VC_PERS_64_REFFERENCE, $tipoItemValorCurso64Creditos);

        $tipoItemFalta = new TipoItem();
        $tipoItemFalta->setDescricao("Falta");
        $tipoItemFalta->setTipo("F");
        $tipoItemFalta->setTipoOcorrencia($this->getReference(TipoOcorrenciaFixtures::TIPO_OCORRENCIA_FALTA));
        $manager->persist($tipoItemFalta);
        $this->addReference(self::TIPO_ITEM_FALTA, $tipoItemFalta);

        $tipoItemAvaliacao = new TipoItem();
        $tipoItemAvaliacao->setDescricao("Avaliação");
        $tipoItemAvaliacao->setTipo("AV");
        $tipoItemAvaliacao->setTipoOcorrencia($this->getReference(TipoOcorrenciaFixtures::TIPO_OCORRENCIA_AVALIACOES));
        $manager->persist($tipoItemAvaliacao);
        $this->addReference(self::TIPO_ITEM_AVALIACAO, $tipoItemAvaliacao);

        $tipoItemAcompanhamentoPedagogico = new TipoItem();
        $tipoItemAcompanhamentoPedagogico->setDescricao("Acompanhamento Pedagógico");
        $tipoItemAcompanhamentoPedagogico->setTipo("AP");
        $tipoItemAcompanhamentoPedagogico->setTipoOcorrencia($this->getReference(TipoOcorrenciaFixtures::TIPO_ACOMPANHAMENTO_PEDAGOGICO));
        $manager->persist($tipoItemAcompanhamentoPedagogico);
        $this->addReference(self::TIPO_ITEM_ACOMPANHAMENTO_PEDAGOGICO, $tipoItemAcompanhamentoPedagogico);

        $tipoItemValorCursoAvulsoCreditos = new TipoItem();
        $tipoItemValorCursoAvulsoCreditos->setDescricao("Modalidade Personal Avulso");
        $tipoItemValorCursoAvulsoCreditos->setTipo("VPA");
        $manager->persist($tipoItemValorCursoAvulsoCreditos);
        $this->addReference(self::TIPO_ITEM_VC_PERS_AVULSO_REFFERENCE, $tipoItemValorCursoAvulsoCreditos);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [TipoOcorrenciaFixtures::class];
    }


}
