<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\MotivoDevolucaoCheque;

class MotivoDevolucaoChequeFixtures extends Fixture
{


    public function load(ObjectManager $manager)
    {
        $config = new MotivoDevolucaoCheque();
        $config->setDescricao("Não especificado");
        $config->setCodigo(0);
        $manager->persist($config);

        $config = new MotivoDevolucaoCheque();
        $config->setDescricao("Cheque sem fundos - 1ª apresentação");
        $config->setCodigo(11);
        $manager->persist($config);

        $config = new MotivoDevolucaoCheque();
        $config->setDescricao("Cheque sem fundos - 2ª apresentação");
        $config->setCodigo(12);
        $manager->persist($config);

        $config = new MotivoDevolucaoCheque();
        $config->setDescricao("Conta encerrada");
        $config->setCodigo(13);
        $manager->persist($config);

        $config = new MotivoDevolucaoCheque();
        $config->setDescricao("Prática espúria");
        $config->setCodigo(14);
        $manager->persist($config);

        $config = new MotivoDevolucaoCheque();
        $config->setDescricao("Folha de cheque cancelada por solicitação do correntista");
        $config->setCodigo(20);
        $manager->persist($config);

        $config = new MotivoDevolucaoCheque();
        $config->setDescricao("Divergência ou insuficiência de assinatura");
        $config->setCodigo(22);
        $manager->persist($config);

        $config = new MotivoDevolucaoCheque();
        $config->setDescricao("Bloqueio judicial ou determinação do Banco Central do Brasil");
        $config->setCodigo(24);
        $manager->persist($config);

        $config = new MotivoDevolucaoCheque();
        $config->setDescricao("Cancelamento de talonário pelo banco sacado");
        $config->setCodigo(25);
        $manager->persist($config);

        $config = new MotivoDevolucaoCheque();
        $config->setDescricao("Contra-ordem (ou revogação) ou oposição (ou sustação), ocasionada por furto ou roubo");
        $config->setCodigo(28);
        $manager->persist($config);

        $config = new MotivoDevolucaoCheque();
        $config->setDescricao("Cheque bloqueado por falta de confirmação de recebimento do talonário pelo correntista");
        $config->setCodigo(29);
        $manager->persist($config);

        $config = new MotivoDevolucaoCheque();
        $config->setDescricao("Cheque fraudado, emitido sem prévio controle ou responsabilidade do estabelecimento bancário");
        $config->setCodigo(35);
        $manager->persist($config);

        $config = new MotivoDevolucaoCheque();
        $config->setDescricao("Cheque apresentado a banco que não o sacado");
        $config->setCodigo(41);
        $manager->persist($config);

        $config = new MotivoDevolucaoCheque();
        $config->setDescricao("Cheque prescrito");
        $config->setCodigo(44);
        $manager->persist($config);

        $config = new MotivoDevolucaoCheque();
        $config->setDescricao("Informação essencial faltante ou inconsistente não passível de verificação pelo banco remetente");
        $config->setCodigo(59);
        $manager->persist($config);

        $config = new MotivoDevolucaoCheque();
        $config->setDescricao("Papel não compensável");
        $config->setCodigo(61);
        $manager->persist($config);

        $config = new MotivoDevolucaoCheque();
        $config->setDescricao("Contrato de compensação encerrado");
        $config->setCodigo(72);
        $manager->persist($config);

        $manager->flush();

    }


}
