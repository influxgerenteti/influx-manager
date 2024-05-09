<?php
namespace App\Tests\BO;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Helper\FunctionHelper;

/**
 *
 * @author Luiz Antonio Costa
 */
class HorarioAulaBOTest extends KernelTestCase
{


    public function testHoraExiste()
    {
        $arrayObjetos = [];
        $dataAtual    = new \DateTime();
        $data1Hora    = new \DateTime();
        $data1Hora->modify("+ 1 hour");
        $objetoA = new \App\Entity\Principal\HorarioAula();
        $objetoA->setHorarioInicio($dataAtual);
        $objetoA->setDiaSemana("SEG");
        $arrayObjetos[] = $objetoA;
        $objetoB        = new \App\Entity\Principal\HorarioAula();
        $objetoB->setHorarioInicio($dataAtual);
        $objetoB->setDiaSemana("SEG");
        $objetoB->setHorarioInicio($data1Hora);
        $arrayObjetos[] = $objetoB;

        foreach ($arrayObjetos as $indice => $objetoORM) {
            $retorno = \App\BO\Principal\HorarioAulaBO::naoExisteHorarioIgualParaDiaSelecionado($objetoORM, $arrayObjetos, $indice);
            $this->assertTrue($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("A hora informada é a mesma do objeto"));
        }

    }

    public function testHoraNaoExiste()
    {
        $arrayObjetos = [];
        $dataAtual    = new \DateTime();
        $objetoA      = new \App\Entity\Principal\HorarioAula();
        $objetoA->setHorarioInicio($dataAtual);
        $objetoA->setDiaSemana("SEG");
        $arrayObjetos[] = $objetoA;
        $objetoA->setHorarioInicio($dataAtual);
        $arrayObjetos[] = $objetoA;

        foreach ($arrayObjetos as $indice => $objetoORM) {
            $retorno = \App\BO\Principal\HorarioAulaBO::naoExisteHorarioIgualParaDiaSelecionado($objetoORM, $arrayObjetos, $indice);
            $this->assertFalse($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("O horario informado é maior que 1h"));
        }
    }


}
