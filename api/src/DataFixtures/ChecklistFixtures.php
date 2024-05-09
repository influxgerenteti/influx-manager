<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\ChecklistAtividade;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Helper\SituacoesSistema;
use App\Entity\Principal\Checklist;

class ChecklistFixtures extends Fixture implements DependentFixtureInterface
{


    public function load(ObjectManager $manager)
    {
        $checklistAtividadeTestePapelAdmin = new ChecklistAtividade();
        $checklistAtividadeTestePapelAdmin->setDescricao("Teste Atividade Admin");
        $checklistAtividadeTestePapelAdmin->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $checklistAtividadeTestePapelAdmin->setTipoAtividade(SituacoesSistema::TIPO_ATIVIDADE_DIARIA);
        $checklistAtividadeTestePapelAdmin->addPapel($this->getReference(PapelFixtures::PAPEL_ADMINISTRADOR));
        $manager->persist($checklistAtividadeTestePapelAdmin);

        $checklistAtividadepapelConsultor = new ChecklistAtividade();
        $checklistAtividadepapelConsultor->setDescricao("Teste Atividade Atendente");
        $checklistAtividadepapelConsultor->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $checklistAtividadepapelConsultor->setTipoAtividade(SituacoesSistema::TIPO_ATIVIDADE_DIARIA);
        $checklistAtividadepapelConsultor->addPapel($this->getReference(PapelFixtures::PAPEL_CONSULTOR));
        $manager->persist($checklistAtividadepapelConsultor);

        $checklistAtividadeFranqueadorapapelConsultor = new ChecklistAtividade();
        $checklistAtividadeFranqueadorapapelConsultor->setDescricao("Pagar contas");
        $checklistAtividadeFranqueadorapapelConsultor->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $checklistAtividadeFranqueadorapapelConsultor->setTipoAtividade(SituacoesSistema::TIPO_ATIVIDADE_MENSAL);
        $checklistAtividadeFranqueadorapapelConsultor->addPapel($this->getReference(PapelFixtures::PAPEL_CONSULTOR));
        $manager->persist($checklistAtividadeFranqueadorapapelConsultor);

        $AtividadesAdminDiaria = [
            "Pagar contas",
            "Quitar boletos pagos no Sponte",
            "Fazer espelhamento do Sponte com a conta bancária",
            "Realizar a baixa no controle de recebimento de cartões",
            "Conferir conta caixa pequeno, caixa e cofre",
            "Acompanhar follow-ups financeiros",
            "Andar pela escola em busca de problemas",
            "Preencher na agenda do Sponte tudo que aconteceu para que o próximo gerente possa dar continuidade",
            "Checar e-mail da direção (e-mails franqueadora, orientações, etc.) (direção, escola e pedag.)",
            "Checar e-mail da direção (assuntos administrativos e financeiros)",
            "Responder e-mails dos alunos no saobernardo@influx.com.br",
            "Acompanhar os follow-ups pedagógicos",
            "Conferir o andamento das atividades no calendário pedagógico/relacionamento",
            "Observar relacionamento entre coordenação e alunos",
            "Distribuir para os consultores os interessados que chegam no e-mail saobernardo@influx.com.br",
            "Observar os atendimentos telefônicos e pessoais dos consultores",
            "Conferir se os follows de interessados estão completos e os retornos feitos na data correta",
            "Fazer controle de contratos e pesquisa de visibilidade no portal inFlux",
            "Conferir contratos de matrículas/rematrículas novas",
            "Fazer os consultores prospectar com a placa magnética diariamente orientando o lugar (mín 3h por consultor)",
        ];

        $checklistFranqueadoraPapelAdmin = new Checklist();
        $checklistFranqueadoraPapelAdmin->setDescricao("Atividades Gerente/Franqueado");

        foreach ($AtividadesAdminDiaria as $atividade) {
            $checklistAtividadeFranqueadoraPapelAdmin = new ChecklistAtividade();
            $checklistAtividadeFranqueadoraPapelAdmin->setDescricao($atividade);
            $checklistAtividadeFranqueadoraPapelAdmin->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
            $checklistAtividadeFranqueadoraPapelAdmin->setTipoAtividade(SituacoesSistema::TIPO_ATIVIDADE_DIARIA);
            $checklistAtividadeFranqueadoraPapelAdmin->addPapel($this->getReference(PapelFixtures::PAPEL_ADMINISTRADOR));
            $manager->persist($checklistAtividadeFranqueadoraPapelAdmin);
            $checklistFranqueadoraPapelAdmin->addChecklistAtividade($checklistAtividadeFranqueadoraPapelAdmin);
        }

        $manager->persist($checklistFranqueadoraPapelAdmin);

        $AtividadesAdminSemanal = [
            "SEG - Reunião para converter as matrículas com vencimento até quarta-feira",
            "QUI - Reunião para converter as matrículas com vencimento até sábado",
            "SEX - Fazer pente fino nos Class Records em busca de possíveis problemas (faltas, tarefas, erros, etc.)",
            "SEX - Reunião com coordenador pedagógico para abordar situações ocorridas durante a semana",
            "SEX - Tirar relatório de contas a receber e passar para o consultor gerar follows e fazer as cobranças",
            "SEX - Conferir com o consultor responsável se o espelhamento do estoque está correto. (Livros e materiais ADM)",
            "SEX - Conferir com consultor responsável se há pendências em empréstimos e devoluções da biblioteca",
            "SEX - Checar com o gerente lista de pendências de manutenção, estrutura e compras",
        ];

        foreach ($AtividadesAdminSemanal as $atividade) {
            $checklistAtividadeFranqueadoraPapelAdmin = new ChecklistAtividade();
            $checklistAtividadeFranqueadoraPapelAdmin->setDescricao($atividade);
            $checklistAtividadeFranqueadoraPapelAdmin->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
            $checklistAtividadeFranqueadoraPapelAdmin->setTipoAtividade(SituacoesSistema::TIPO_ATIVIDADE_SEMANAL);
            $checklistAtividadeFranqueadoraPapelAdmin->addPapel($this->getReference(PapelFixtures::PAPEL_ADMINISTRADOR));
            $manager->persist($checklistAtividadeFranqueadoraPapelAdmin);
            $checklistFranqueadoraPapelAdmin->addChecklistAtividade($checklistAtividadeFranqueadoraPapelAdmin);
        }

        $manager->persist($checklistFranqueadoraPapelAdmin);

        $AtividadesAdminMensal = [
            "Dia 01 - Analisar entradas e saídas futuras para verificar fluxo de caixa",
            "Dia 01 - Alinhar com equipe que cursos devem ser vendidos a cada mês, argumentos e parcelamentos",
            "Dia 01 - Tirar relatório de média de alunos por turma",
            "Dia 01 - Conferir horas de colaboradores e enviar para Contabilidade",
            "Dia 01 - Conferir horas de instrutores horistas e imprimir recibo de pagamento",
            "Dia 05 - Passar as metas de matrícula do mês para a equipe",
            "Dia 05 - Separar contas pagas para a contabilidade",
            "Dia 05 - Efetuar pagamento de funcionários",
            "Dia 10 - Adicionar Pesquisa de Visibilidade no Portal inFlux",
            "Dia 15 - Fazer análise do controle de contatos (checar conversão, retornos agendados e efetivos e qtde contatos)",
            "Dia 15 - Comprar itens de insumo para a escola",
            "Dia 30 - Fazer análise do controle de contatos (checar conversão, retornos agendados e efetivos e qtde contatos)",
            "Dia 30 - Entregar para o consultor a lista de alunos ativos para arquivar os inativos",
            "Datas Múltiplas de 5 - Depositar cheques e dar baixa no Sponte",
        ];

        foreach ($AtividadesAdminMensal as $atividade) {
            $checklistAtividadeFranqueadoraPapelAdmin = new ChecklistAtividade();
            $checklistAtividadeFranqueadoraPapelAdmin->setDescricao($atividade);
            $checklistAtividadeFranqueadoraPapelAdmin->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
            $checklistAtividadeFranqueadoraPapelAdmin->setTipoAtividade(SituacoesSistema::TIPO_ATIVIDADE_MENSAL);
            $checklistAtividadeFranqueadoraPapelAdmin->addPapel($this->getReference(PapelFixtures::PAPEL_ADMINISTRADOR));
            $manager->persist($checklistAtividadeFranqueadoraPapelAdmin);
            $checklistFranqueadoraPapelAdmin->addChecklistAtividade($checklistAtividadeFranqueadoraPapelAdmin);
        }

        $manager->persist($checklistFranqueadoraPapelAdmin);

        $AtividadesAdminAtemporal = [
            "01 Abr e 01 Set - Fazer projeção de rentabilidade do semestre",
            "Jan - Colocar banner do Curso Regular",
            "Abr - Colocar banner de Semi Intensivo de Maio",
            "Mai - Colocar banner de Intensivo de Julho",
            "Jul - Colocar banner do Curso Regular",
            "Set - Colocar banner de Semi Intensivo de Outubro",
            "Out - Colocar banner de Intensivo de Janeiro",
            "Contratar e treinar novos colaboradores",
            "Conversar com qualquer aluno nos procure para desistir do curso",
            "Promover a campanha de renovação",
            "Fazer treinamento contínuo com toda a equipe e dar feedbacks constantes",
            "Auxiliar em vendas onde as consultoras demonstrem alguma dificuldade",
            "Passar nas salas para observar as aulas dos professores (uso do echo, pontualidade, etc)",
        ];

        foreach ($AtividadesAdminAtemporal as $atividade) {
            $checklistAtividadeFranqueadoraPapelAdmin = new ChecklistAtividade();
            $checklistAtividadeFranqueadoraPapelAdmin->setDescricao($atividade);
            $checklistAtividadeFranqueadoraPapelAdmin->setFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
            $checklistAtividadeFranqueadoraPapelAdmin->setTipoAtividade(SituacoesSistema::TIPO_ATIVIDADE_ATEMPORAL);
            $checklistAtividadeFranqueadoraPapelAdmin->addPapel($this->getReference(PapelFixtures::PAPEL_ADMINISTRADOR));
            $manager->persist($checklistAtividadeFranqueadoraPapelAdmin);
            $checklistFranqueadoraPapelAdmin->addChecklistAtividade($checklistAtividadeFranqueadoraPapelAdmin);
        }

        $manager->persist($checklistFranqueadoraPapelAdmin);

        $checklistFranqueadorapapelConsultor = new Checklist();
        $checklistFranqueadorapapelConsultor->setDescricao("Checklist Franqueadora Papel Atendente");
        $checklistFranqueadorapapelConsultor->addChecklistAtividade($checklistAtividadeFranqueadorapapelConsultor);
        $manager->persist($checklistFranqueadorapapelConsultor);

        $checklistpapelConsultor = new Checklist();
        $checklistpapelConsultor->setDescricao("Checklist Papel Atendente");
        $checklistpapelConsultor->addChecklistAtividade($checklistAtividadepapelConsultor);
        $manager->persist($checklistpapelConsultor);

        $checklistPapelAdmin = new Checklist();
        $checklistPapelAdmin->setDescricao("Checklist Papel Admin");
        $checklistPapelAdmin->addChecklistAtividade($checklistAtividadeTestePapelAdmin);
        $manager->persist($checklistPapelAdmin);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            FranqueadaFixtures::class,
            UsuarioFixtures::class,
            PapelFixtures::class,
        ];
    }


}
