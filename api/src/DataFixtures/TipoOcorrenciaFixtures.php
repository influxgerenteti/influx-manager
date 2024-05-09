<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\TipoOcorrencia;

class TipoOcorrenciaFixtures extends Fixture
{
    public const TIPO_OCORRENCIA_FALTA      = "tipoOcorrenciaFaltas";
    public const TIPO_OCORRENCIA_REPOSICOES = "tipoOcorrenciaReposicoes";
    public const TIPO_OCORRENCIA_AVALIACOES = "tipoOcorrenciaAvaliacoes";
    public const TIPO_OCORRENCIA_ATIVIDADES_EXTRAS = "tipoOcorrenciaAtividadesExtras";
    public const TIPO_OCORRENCIA_COBRANCA          = "tipoOcorrenciaCobranca";
    public const TIPO_TRANSFERENCIA_TURMA          = "tipoOcorrenciaTransferenciaTurma";
    public const TIPO_ACOMPANHAMENTO_PEDAGOGICO    = "tipoAcompanhamentoPedagogico";

    public function load(ObjectManager $manager)
    {
        $ocorrenciaBonusClasses = new TipoOcorrencia();
        $ocorrenciaBonusClasses->setDescricao("Bônus Classes");
        $ocorrenciaBonusClasses->setTipo("BC");
        $manager->persist($ocorrenciaBonusClasses);

        $ocorrenciaInsatisfacao = new TipoOcorrencia();
        $ocorrenciaInsatisfacao->setDescricao("Insatisfações");
        $ocorrenciaInsatisfacao->setTipo("IN");
        $manager->persist($ocorrenciaInsatisfacao);

        $ocorrenciaSugestoes = new TipoOcorrencia();
        $ocorrenciaSugestoes->setDescricao("Sugestões");
        $ocorrenciaSugestoes->setTipo("S");
        $manager->persist($ocorrenciaSugestoes);

        $outros = new TipoOcorrencia();
        $outros->setDescricao("Outros");
        $outros->setTipo("O");
        $manager->persist($outros);

        $ocorrenciaFalta = new TipoOcorrencia();
        $ocorrenciaFalta->setDescricao("Falta");
        $ocorrenciaFalta->setTipo("F");
        $manager->persist($ocorrenciaFalta);
        $this->addReference(self::TIPO_OCORRENCIA_FALTA, $ocorrenciaFalta);

        $atividadesExtra = new TipoOcorrencia();
        $atividadesExtra->setDescricao("Atividades Extra");
        $atividadesExtra->setTipo("AE");
        $manager->persist($atividadesExtra);
        $this->addReference(self::TIPO_OCORRENCIA_ATIVIDADES_EXTRAS, $atividadesExtra);

        $ocorrenciaReposicoes = new TipoOcorrencia();
        $ocorrenciaReposicoes->setDescricao("Reposições");
        $ocorrenciaReposicoes->setTipo("R");
        $manager->persist($ocorrenciaReposicoes);
        $this->addReference(self::TIPO_OCORRENCIA_REPOSICOES, $ocorrenciaReposicoes);

        $ocorrenciaAvaliacao = new TipoOcorrencia();
        $ocorrenciaAvaliacao->setDescricao("Avaliações");
        $ocorrenciaAvaliacao->setTipo("A");
        $manager->persist($ocorrenciaAvaliacao);
        $this->addReference(self::TIPO_OCORRENCIA_AVALIACOES, $ocorrenciaAvaliacao);

        $ocorrenciaCobranca = new TipoOcorrencia();
        $ocorrenciaCobranca->setDescricao("Cobranças");
        $ocorrenciaCobranca->setTipo("C");
        $manager->persist($ocorrenciaCobranca);
        $this->addReference(self::TIPO_OCORRENCIA_COBRANCA, $ocorrenciaCobranca);

        $ocorrenciaTransferenciaTurma = new TipoOcorrencia();
        $ocorrenciaTransferenciaTurma->setDescricao("Transferência de Turmas");
        $ocorrenciaTransferenciaTurma->setTipo("TR");
        $manager->persist($ocorrenciaTransferenciaTurma);
        $this->addReference(self::TIPO_TRANSFERENCIA_TURMA, $ocorrenciaTransferenciaTurma);

        $ocorrenciaAcompanhamentoPedagogico = new TipoOcorrencia();
        $ocorrenciaAcompanhamentoPedagogico->setDescricao("Acompanhamento Pedagógico");
        $ocorrenciaAcompanhamentoPedagogico->setTipo("AP");
        $manager->persist($ocorrenciaAcompanhamentoPedagogico);
        $this->addReference(self::TIPO_ACOMPANHAMENTO_PEDAGOGICO, $ocorrenciaAcompanhamentoPedagogico);

        $manager->flush();
    }


}
