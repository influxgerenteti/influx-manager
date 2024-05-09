<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\PlanoConta;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class PlanoContaFixtures extends Fixture implements DependentFixtureInterface
{

    public const DESPESA_REFERENCE            = "despesa";
    public const RECEITA_REFERENCE            = "receita";
    public const MATRICULA_REFERENCE          = "pcMatricula";
    public const VALOR_CURSO_REFERENCE        = "pcValorCurso";
    public const PLANO_CONTA_LIVROS_REFERENCE = "livros";
    public const PLANO_CONTA_ATIVIDADE_EXTRA_REF = "atividade_extraPC";
    public const PLANO_CONTA_MAKE_UP_CLASS       = "makeUpClassPC";
    public const PLANO_CONTA_MAKE_UP_TEST        = "makeUpTestPC";
    public const TAXA_BANCO_REFERENCE            = "taxaBanco";
    public const MATRICULA_PERSONAL_REFERENCE    = "matriculaPersonal";

    public function load(ObjectManager $manager)
    {
        $despesaPai = new PlanoConta();
        $despesaPai->setDescricao('Despesas');
        $despesaPai->setTipoMovimentoNota('E');
        $despesaPai->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $manager->persist($despesaPai);

        $despesaPai2 = new PlanoConta();
        $despesaPai2->setDescricao('Despesas financeiras');
        $despesaPai2->setTipoMovimentoNota('E');
        $despesaPai2->setPai($despesaPai);
        $despesaPai2->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $manager->persist($despesaPai2);

        $despesa = new PlanoConta();
        $despesa->setDescricao('Taxa de cartão');
        $despesa->setTipoMovimentoNota('E');
        $despesa->setPai($despesaPai2);
        $despesa->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $manager->persist($despesa);

        $despesa = new PlanoConta();
        $despesa->setDescricao('Taxa de boleto');
        $despesa->setTipoMovimentoNota('E');
        $despesa->setPai($despesaPai2);
        $despesa->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $manager->persist($despesa);

        $despesa = new PlanoConta();
        $despesa->setDescricao('Taxa de banco');
        $despesa->setTipoMovimentoNota('E');
        $despesa->setPai($despesaPai2);
        $despesa->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $manager->persist($despesa);
        $this->addReference(self::TAXA_BANCO_REFERENCE, $despesa);

        $despesaPai3 = new PlanoConta();
        $despesaPai3->setDescricao('Despesas fixas');
        $despesaPai3->setTipoMovimentoNota('E');
        $despesaPai3->setPai($despesaPai);
        $despesaPai3->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $manager->persist($despesaPai3);

        $despesa = new PlanoConta();
        $despesa->setDescricao('Água');
        $despesa->setTipoMovimentoNota('E');
        $despesa->setPai($despesaPai3);
        $despesa->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $manager->persist($despesa);

        $despesa = new PlanoConta();
        $despesa->setDescricao('Alarme');
        $despesa->setTipoMovimentoNota('E');
        $despesa->setPai($despesaPai3);
        $despesa->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $manager->persist($despesa);

        $despesa = new PlanoConta();
        $despesa->setDescricao('Aluguel e IPTU');
        $despesa->setTipoMovimentoNota('E');
        $despesa->setPai($despesaPai3);
        $despesa->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $manager->persist($despesa);

        $despesa = new PlanoConta();
        $despesa->setDescricao('Contabilidade');
        $despesa->setTipoMovimentoNota('E');
        $despesa->setPai($despesaPai3);
        $despesa->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $manager->persist($despesa);

        $despesa = new PlanoConta();
        $despesa->setDescricao('Energia elétrica');
        $despesa->setTipoMovimentoNota('E');
        $despesa->setPai($despesaPai3);
        $despesa->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $manager->persist($despesa);

        $despesa = new PlanoConta();
        $despesa->setDescricao('Telefone');
        $despesa->setTipoMovimentoNota('E');
        $despesa->setPai($despesaPai3);
        $despesa->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $manager->persist($despesa);

        $despesaPai4 = new PlanoConta();
        $despesaPai4->setDescricao('Despesas gerais');
        $despesaPai4->setTipoMovimentoNota('E');
        $despesaPai4->setPai($despesaPai);
        $despesaPai4->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $manager->persist($despesaPai4);

        $despesa = new PlanoConta();
        $despesa->setDescricao('Acessorias');
        $despesa->setTipoMovimentoNota('E');
        $despesa->setPai($despesaPai4);
        $despesa->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $manager->persist($despesa);

        $despesa = new PlanoConta();
        $despesa->setDescricao('Compra da escola');
        $despesa->setTipoMovimentoNota('E');
        $despesa->setPai($despesaPai4);
        $despesa->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $manager->persist($despesa);

        $despesa = new PlanoConta();
        $despesa->setDescricao('Estacionamento');
        $despesa->setTipoMovimentoNota('E');
        $despesa->setPai($despesaPai4);
        $despesa->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $manager->persist($despesa);

        $despesa = new PlanoConta();
        $despesa->setDescricao('Material de expediente');
        $despesa->setTipoMovimentoNota('E');
        $despesa->setPai($despesaPai4);
        $despesa->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $manager->persist($despesa);

        $despesa = new PlanoConta();
        $despesa->setDescricao('Franchising');
        $despesa->setTipoMovimentoNota('E');
        $despesa->setPai($despesaPai4);
        $despesa->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $manager->persist($despesa);

        $despesa = new PlanoConta();
        $despesa->setDescricao('Jardinagem');
        $despesa->setTipoMovimentoNota('E');
        $despesa->setPai($despesaPai4);
        $despesa->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $manager->persist($despesa);

        $despesa = new PlanoConta();
        $despesa->setDescricao('Manutenção');
        $despesa->setTipoMovimentoNota('E');
        $despesa->setPai($despesaPai4);
        $despesa->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $manager->persist($despesa);

        $despesaPai5 = new PlanoConta();
        $despesaPai5->setDescricao('Pessoal');
        $despesaPai5->setTipoMovimentoNota('E');
        $despesaPai5->setPai($despesaPai);
        $despesaPai5->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $manager->persist($despesaPai5);

        $despesa = new PlanoConta();
        $despesa->setDescricao('Salário');
        $despesa->setTipoMovimentoNota('E');
        $despesa->setPai($despesaPai5);
        $despesa->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $despesa->setChaveConsulta('SAL');
        $manager->persist($despesa);

        $despesa = new PlanoConta();
        $despesa->setDescricao('Décimo terceiro');
        $despesa->setTipoMovimentoNota('E');
        $despesa->setPai($despesaPai5);
        $despesa->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $manager->persist($despesa);

        $despesa = new PlanoConta();
        $despesa->setDescricao('FGTS');
        $despesa->setTipoMovimentoNota('E');
        $despesa->setPai($despesaPai5);
        $despesa->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $manager->persist($despesa);

        $despesa = new PlanoConta();
        $despesa->setDescricao('Férias');
        $despesa->setTipoMovimentoNota('E');
        $despesa->setPai($despesaPai5);
        $despesa->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $manager->persist($despesa);

        $despesa = new PlanoConta();
        $despesa->setDescricao('INSS');
        $despesa->setTipoMovimentoNota('E');
        $despesa->setPai($despesaPai5);
        $despesa->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $manager->persist($despesa);

        $despesa = new PlanoConta();
        $despesa->setDescricao('Vale-refeição');
        $despesa->setTipoMovimentoNota('E');
        $despesa->setPai($despesaPai5);
        $despesa->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $manager->persist($despesa);

        $despesa = new PlanoConta();
        $despesa->setDescricao('Vale-transporte');
        $despesa->setTipoMovimentoNota('E');
        $despesa->setPai($despesaPai5);
        $despesa->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $manager->persist($despesa);

        $despesaPai6 = new PlanoConta();
        $despesaPai6->setDescricao('Tributos');
        $despesaPai6->setTipoMovimentoNota('E');
        $despesaPai6->setPai($despesaPai);
        $despesaPai6->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $manager->persist($despesaPai6);

        // Receitas
        $receitaPai = new PlanoConta();
        $receitaPai->setDescricao('Receitas');
        $receitaPai->setTipoMovimentoNota('S');
        $receitaPai->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $manager->persist($receitaPai);

        $receita = new PlanoConta();
        $receita->setDescricao('Atividades Extra');
        $receita->setTipoMovimentoNota('S');
        $receita->setPai($receitaPai);
        $receita->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $manager->persist($receita);
        $this->addReference(self::PLANO_CONTA_ATIVIDADE_EXTRA_REF, $receita);

        $receita = new PlanoConta();
        $receita->setDescricao('Investimentos');
        $receita->setTipoMovimentoNota('S');
        $receita->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $receita->setPai($receitaPai);
        $manager->persist($receita);

        $receita = new PlanoConta();
        $receita->setDescricao('Aplicação TOIEC & TOEFL');
        $receita->setTipoMovimentoNota('S');
        $receita->setPai($receitaPai);
        $receita->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $manager->persist($receita);

        $receita = new PlanoConta();
        $receita->setDescricao('Estorno de parcelas');
        $receita->setTipoMovimentoNota('S');
        $receita->setPai($receitaPai);
        $receita->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $manager->persist($receita);

        $receita = new PlanoConta();
        $receita->setDescricao('Juros e multas');
        $receita->setTipoMovimentoNota('S');
        $receita->setPai($receitaPai);
        $receita->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $manager->persist($receita);

        $receita = new PlanoConta();
        $receita->setDescricao('Make-up class');
        $receita->setTipoMovimentoNota('S');
        $receita->setPai($receitaPai);
        $receita->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $manager->persist($receita);
        $this->addReference(self::PLANO_CONTA_MAKE_UP_CLASS, $receita);

        $receita = new PlanoConta();
        $receita->setDescricao('Make-up test');
        $receita->setTipoMovimentoNota('S');
        $receita->setPai($receitaPai);
        $receita->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $manager->persist($receita);
        $this->addReference(self::PLANO_CONTA_MAKE_UP_TEST, $receita);

        $matriculaPc = new PlanoConta();
        $matriculaPc->setDescricao('Matrícula');
        $matriculaPc->setTipoMovimentoNota('S');
        $matriculaPc->setPai($receitaPai);
        $matriculaPc->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $manager->persist($matriculaPc);
        $this->addReference(self::MATRICULA_REFERENCE, $matriculaPc);

        $receita = new PlanoConta();
        $receita->setDescricao('Parcela do curso personal');
        $receita->setTipoMovimentoNota('S');
        $receita->setPai($receitaPai);
        $receita->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $manager->persist($receita);
        $this->addReference(self::MATRICULA_PERSONAL_REFERENCE, $receita);

        $receita = new PlanoConta();
        $receita->setDescricao('Parcela do curso VIP');
        $receita->setTipoMovimentoNota('S');
        $receita->setPai($receitaPai);
        $receita->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $manager->persist($receita);

        $vlCursoPc = new PlanoConta();
        $vlCursoPc->setDescricao('Parcela do curso turmas');
        $vlCursoPc->setTipoMovimentoNota('S');
        $vlCursoPc->setPai($receitaPai);
        $vlCursoPc->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $manager->persist($vlCursoPc);
        $this->addReference(self::VALOR_CURSO_REFERENCE, $vlCursoPc);

        $livros = new PlanoConta();
        $livros->setDescricao('Livros');
        $livros->setTipoMovimentoNota('S');
        $livros->setPai($receitaPai);
        $livros->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $manager->persist($livros);
        $this->addReference(self::PLANO_CONTA_LIVROS_REFERENCE, $livros);

        $manager->flush();
        $this->addReference(self::DESPESA_REFERENCE, $despesa);
        $this->addReference(self::RECEITA_REFERENCE, $receita);
    }

    public function getDependencies()
    {
        return [
            FranqueadaFixtures::class,
        ];
    }


}
