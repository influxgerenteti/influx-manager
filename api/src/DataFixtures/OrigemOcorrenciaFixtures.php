<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\OrigemOcorrencia;
use App\Helper\SituacoesSistema;

class OrigemOcorrenciaFixtures extends Fixture
{


    public function load(ObjectManager $manager)
    {
        $origemAvaliacaoParcial7 = new OrigemOcorrencia();
        $origemAvaliacaoParcial7->setDescricao("1ª Avaliação Parcial");
        $origemAvaliacaoParcial7->setTipoOrigem(SituacoesSistema::ORIGEM_OCORRENCIA_AVALIACAO_PARCIAL_7);
        $manager->persist($origemAvaliacaoParcial7);

        $origemAvaliacaoParcial23 = new OrigemOcorrencia();
        $origemAvaliacaoParcial23->setDescricao("2ª Avaliação Parcial");
        $origemAvaliacaoParcial23->setTipoOrigem(SituacoesSistema::ORIGEM_OCORRENCIA_AVALIACAO_PARCIAL_23);
        $manager->persist($origemAvaliacaoParcial23);

        $origemMidTerm = new OrigemOcorrencia();
        $origemMidTerm->setDescricao("Mid-Term");
        $origemMidTerm->setTipoOrigem(SituacoesSistema::ORIGEM_OCORRENCIA_MID_TERM);
        $manager->persist($origemMidTerm);

        $origemFinalTest = new OrigemOcorrencia();
        $origemFinalTest->setDescricao("Final Test");
        $origemFinalTest->setTipoOrigem(SituacoesSistema::ORIGEM_OCORRENCIA_FINAL_TEST);
        $manager->persist($origemFinalTest);

        $origemOcorrenciaFaltas = new OrigemOcorrencia();
        $origemOcorrenciaFaltas->setDescricao("Faltas Seguidas");
        $origemOcorrenciaFaltas->setTipoOrigem(SituacoesSistema::ORIGEM_OCORRENCIA_FALTAS_SEGUIDAS);
        $manager->persist($origemOcorrenciaFaltas);

        $origemOcorrenciaHomeworkCA = new OrigemOcorrencia();
        $origemOcorrenciaHomeworkCA->setDescricao("Homework CA");
        $origemOcorrenciaHomeworkCA->setTipoOrigem(SituacoesSistema::ORIGEM_OCORRENCIA_HOMEWORK_CA);
        $manager->persist($origemOcorrenciaHomeworkCA);

        $origemOcorrenciaHomeworkCE = new OrigemOcorrencia();
        $origemOcorrenciaHomeworkCE->setDescricao("Homework CE");
        $origemOcorrenciaHomeworkCE->setTipoOrigem(SituacoesSistema::ORIGEM_OCORRENCIA_HOMEWORK_CE);
        $manager->persist($origemOcorrenciaHomeworkCE);

        $origemOcorrenciaTransferenciaTurma = new OrigemOcorrencia();
        $origemOcorrenciaTransferenciaTurma->setDescricao("Transferencia de turma");
        $origemOcorrenciaTransferenciaTurma->setTipoOrigem(SituacoesSistema::ORIGEM_OCORRENCIA_TRANSFERENCIA_TURMA);
        $manager->persist($origemOcorrenciaTransferenciaTurma);

        $origemOcorrenciaReagendamentoPersonal = new OrigemOcorrencia();
        $origemOcorrenciaReagendamentoPersonal->setDescricao("Reagendamento Personal");
        $origemOcorrenciaReagendamentoPersonal->setTipoOrigem(SituacoesSistema::ORIGEM_OCORRENCIA_REAGENDAMENTO_PERSONAL);
        $manager->persist($origemOcorrenciaReagendamentoPersonal);

        $origemOcorrenciaBonusClass = new OrigemOcorrencia();
        $origemOcorrenciaBonusClass->setDescricao("Bonus Class");
        $origemOcorrenciaBonusClass->setTipoOrigem(SituacoesSistema::ORIGEM_OCORRENCIA_BONUS_CLASS);
        $manager->persist($origemOcorrenciaBonusClass);

        $manager->flush();
    }


}
