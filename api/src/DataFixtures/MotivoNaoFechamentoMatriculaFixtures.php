<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\MotivoNaoFechamentoMatricula;

class MotivoNaoFechamentoMatriculaFixtures extends Fixture
{


    public function load(ObjectManager $manager)
    {
        $config = new MotivoNaoFechamentoMatricula();
        $config->setDescricao("Fechou em outra escola");
        $config->setEfetivo(true);
        $manager->persist($config);

        $config = new MotivoNaoFechamentoMatricula();
        $config->setDescricao("Não tem interesse");
        $config->setEfetivo(true);
        $manager->persist($config);

        $config = new MotivoNaoFechamentoMatricula();
        $config->setDescricao("Problema de Horário");
        $config->setEfetivo(true);
        $manager->persist($config);

        $config = new MotivoNaoFechamentoMatricula();
        $config->setDescricao("Excesso de tentativas de contato");
        $config->setEfetivo(false);
        $manager->persist($config);

        $config = new MotivoNaoFechamentoMatricula();
        $config->setDescricao("Sem resposta do interessado");
        $config->setEfetivo(false);
        $manager->persist($config);

        $config = new MotivoNaoFechamentoMatricula();
        $config->setDescricao("Número de telefone errado");
        $config->setEfetivo(false);
        $manager->persist($config);

        $config = new MotivoNaoFechamentoMatricula();
        $config->setDescricao("Email inválido");
        $config->setEfetivo(false);
        $manager->persist($config);

        $config = new MotivoNaoFechamentoMatricula();
        $config->setDescricao("Deixará para outro momento");
        $config->setEfetivo(true);
        $manager->persist($config);

        $manager->flush();

    }


}
