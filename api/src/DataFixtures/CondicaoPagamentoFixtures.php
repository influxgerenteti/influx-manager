<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\CondicaoPagamento;

class CondicaoPagamentoFixtures extends Fixture
{

    public const CONDICAO_PAGAMENTO_REFERENCE   = "condicao_pagamento";
    public const CONDICAO_PAGAMENTO_REFERENCE_2 = "condicao_pagamento_a_vista";
    public const CONDICAO_PAGAMENTO_REFERENCE_3 = "condicao_pagamento_30_dias";
    public const CONDICAO_PAGAMENTO_REFERENCE_4 = "condicao_pagamento_1_1";

    public function load(ObjectManager $manager)
    {
        $condicaoPagamento = new CondicaoPagamento();
        $condicaoPagamento->setDescricao("30/60/90/120/150");
        $condicaoPagamento->setQuantidadeParcelas(5);
        $manager->persist($condicaoPagamento);

        $condicaoPagamento2 = new CondicaoPagamento();
        $condicaoPagamento2->setDescricao("À vista");
        $condicaoPagamento2->setQuantidadeParcelas(1);
        $manager->persist($condicaoPagamento2);

        $condicaoPagamento3 = new CondicaoPagamento();
        $condicaoPagamento3->setDescricao("30 dias");
        $condicaoPagamento3->setQuantidadeParcelas(1);
        $manager->persist($condicaoPagamento3);

        $condicaoPagamento4 = new CondicaoPagamento();
        $condicaoPagamento4->setDescricao("1 + 1");
        $condicaoPagamento4->setQuantidadeParcelas(2);
        $manager->persist($condicaoPagamento4);

        $manager->flush();
        $this->addReference(self::CONDICAO_PAGAMENTO_REFERENCE, $condicaoPagamento);
        $this->addReference(self::CONDICAO_PAGAMENTO_REFERENCE_2, $condicaoPagamento2);
        $this->addReference(self::CONDICAO_PAGAMENTO_REFERENCE_3, $condicaoPagamento3);
        $this->addReference(self::CONDICAO_PAGAMENTO_REFERENCE_4, $condicaoPagamento4);
    }


}
