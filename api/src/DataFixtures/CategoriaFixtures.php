<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\Categoria;

class CategoriaFixtures extends Fixture
{

    public const CATEGORIA_REFERENCE = "categoria_aluno";
    public const CATEGORIA_FUNCIONARIO_REFERENCE = "categoria_funcionario";
    public const CATEGORIA_FORNECEDOR_REFERENCE  = "categoria_fornecedor";
    public const CATEGORIA_PROFESSOR_REFERENCE   = "categoria_professor";

    public function load(ObjectManager $manager)
    {
        $aluno = new Categoria();
        $aluno->setNome("Aluno");
        $manager->persist($aluno);

        $funcionario = new Categoria();
        $funcionario->setNome("Funcionário");
        $manager->persist($funcionario);

        $fornecedor = new Categoria();
        $fornecedor->setNome("Fornecedor");
        $manager->persist($fornecedor);

        $professor = new Categoria();
        $professor->setNome("Professor");
        $manager->persist($professor);

        $manager->flush();
        $this->addReference(self::CATEGORIA_REFERENCE, $aluno);
        $this->addReference(self::CATEGORIA_FUNCIONARIO_REFERENCE, $funcionario);
        $this->addReference(self::CATEGORIA_FORNECEDOR_REFERENCE, $fornecedor);
        $this->addReference(self::CATEGORIA_PROFESSOR_REFERENCE, $professor);
    }


}
