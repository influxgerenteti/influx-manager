<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\AcaoSistema;

class AcaoSistemaFixtures extends Fixture
{
    public const ACAO_CRIAR   = "acccriar";
    public const ACAO_ACESSAR = "accacessar";
    public const ACAO_LISTAR  = "acclistar";
    public const ACAO_EDITAR  = "acceditar";
    public const ACAO_EXCLUIR = "accexcluir";
    public const ACAO_DESCONTO_SUPERVISIONADO = "accdescontosupervisionado";
    public const ACAO_TRANSFERENCIA_TURMA     = "acctransferenciaturma";
    public const ACAO_CONSULTOR_FUNCIONARIO   = "accconsultorfuncionario";

    public const ACAO_SEGUNDO_REAGENDAMENTO  = "accsegundoreagendamento";
    public const ACAO_ENTREGA_ITEM_PERMISSAO = "accentregaitempermissao";

    public function load(ObjectManager $manager)
    {

        $acaoSistemaAcessar = new AcaoSistema();
        $acaoSistemaAcessar->setDescricao("ACESSAR");
        $acaoSistemaAcessar->setPermissaoDescricao("Acessar Menu");
        $manager->persist($acaoSistemaAcessar);
        $this->addReference(self::ACAO_ACESSAR, $acaoSistemaAcessar);

        $acaoSistemaListar = new AcaoSistema();
        $acaoSistemaListar->setDescricao("LISTAR");
        $acaoSistemaListar->setPermissaoDescricao("Listar");
        $manager->persist($acaoSistemaListar);
        $this->addReference(self::ACAO_LISTAR, $acaoSistemaListar);

        $acaoSistemaCriar = new AcaoSistema();
        $acaoSistemaCriar->setDescricao("CRIAR");
        $acaoSistemaCriar->setPermissaoDescricao("Adicionar");
        $manager->persist($acaoSistemaCriar);
        $this->addReference(self::ACAO_CRIAR, $acaoSistemaCriar);

        $acaoSistemaEditar = new AcaoSistema();
        $acaoSistemaEditar->setDescricao("EDITAR");
        $acaoSistemaEditar->setPermissaoDescricao("Atualizar");
        $manager->persist($acaoSistemaEditar);
        $this->addReference(self::ACAO_EDITAR, $acaoSistemaEditar);

        $acaoSistemaExcluir = new AcaoSistema();
        $acaoSistemaExcluir->setDescricao("EXCLUIR");
        $acaoSistemaExcluir->setPermissaoDescricao("Remover");
        $manager->persist($acaoSistemaExcluir);
        $this->addReference(self::ACAO_EXCLUIR, $acaoSistemaExcluir);

        // NOTA: NUNCA ALTERAR A ORDEM DAS FIXTURES ACIMA DESTE COMENTARIO
        // NUNCA MUDAR O CAMPO DESCRICAO POIS ELES ESTÃO FIXOS NO FRONT-END, CASO ALTERE AQUI,
        // TERÁ QUE ALTERAR NO FRONT! O PADRÃO DE CONSTANTE É "NOME_PERMISSAO"(Upper-case com '_')
        // QUALQUER NOVA ACAO DO SISTEMA(PERMISSAO) DEVE SER COLOCADO UMA ABAIXO DA OUTRA
        // APÓS ESSA LINHA
        $acaoSistemaDescontoSupervisionado = new AcaoSistema();
        $acaoSistemaDescontoSupervisionado->setDescricao("DESCONTO_SUPERVISIONADO");
        $acaoSistemaDescontoSupervisionado->setPermissaoDescricao("Solicita um login que permita a realização de desbloqueio de campos referentes a descontos.");
        $acaoSistemaDescontoSupervisionado->setSolicitaLoginSuperior(true);
        $manager->persist($acaoSistemaDescontoSupervisionado);
        $this->addReference(self::ACAO_DESCONTO_SUPERVISIONADO, $acaoSistemaDescontoSupervisionado);

        $acaoSistemaTransferenciaTurma = new AcaoSistema();
        $acaoSistemaTransferenciaTurma->setDescricao("TRANSFERENCIA_TURMA");
        $acaoSistemaTransferenciaTurma->setPermissaoDescricao("Ignora turma cheia, permitindo adicionar o aluno que deseja ser transferido.");
        $acaoSistemaTransferenciaTurma->setSolicitaLoginSuperior(true);
        $manager->persist($acaoSistemaTransferenciaTurma);
        $this->addReference(self::ACAO_TRANSFERENCIA_TURMA, $acaoSistemaTransferenciaTurma);

        $acaoSistemaConsultorFuncionario = new AcaoSistema();
        $acaoSistemaConsultorFuncionario->setDescricao("CONSULTOR_FUNCIONARIO");
        $acaoSistemaConsultorFuncionario->setPermissaoDescricao("Permite alterar o consultor do primeiro atendimento.");
        $acaoSistemaConsultorFuncionario->setSolicitaLoginSuperior(true);
        $manager->persist($acaoSistemaConsultorFuncionario);
        $this->addReference(self::ACAO_CONSULTOR_FUNCIONARIO, $acaoSistemaConsultorFuncionario);

        $acaoSistemaSegundoReagendamento = new AcaoSistema();
        $acaoSistemaSegundoReagendamento->setDescricao("SEGUNDO_REAGENDAMENTO");
        $acaoSistemaSegundoReagendamento->setPermissaoDescricao("Permite reagendar um reagendamento novamente.");
        $acaoSistemaSegundoReagendamento->setSolicitaLoginSuperior(true);
        $manager->persist($acaoSistemaSegundoReagendamento);
        $this->addReference(self::ACAO_SEGUNDO_REAGENDAMENTO, $acaoSistemaSegundoReagendamento);

        $acaoSistemaEntregaItemPermissao = new AcaoSistema();
        $acaoSistemaEntregaItemPermissao->setDescricao("ENTREGA_ITEM_PERMISSAO");
        $acaoSistemaEntregaItemPermissao->setPermissaoDescricao("Permite realizar a entrega de um item já cancelado, ou, o cancelamento de um item já entregue.");
        $acaoSistemaEntregaItemPermissao->setSolicitaLoginSuperior(true);
        $manager->persist($acaoSistemaEntregaItemPermissao);
        $this->addReference(self::ACAO_ENTREGA_ITEM_PERMISSAO, $acaoSistemaEntregaItemPermissao);

        $manager->flush();
    }


}
