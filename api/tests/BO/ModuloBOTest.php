<?php
namespace App\Tests\BO;

use App\BO\Principal\ModuloBO;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 *
 * @author Marcelo Andre Naegeler
 */
class ModuloBOTest extends KernelTestCase
{


    public function testOrganizarItensModulo ()
    {
        $modulosPai = [
            [
                'id'   => 1,
                'nome' => 'Modulo Pai 1',
                'url'  => '/modulo-pai-1',
            ],
            [
                'id'   => 2,
                'nome' => 'Modulo Pai 2',
                'url'  => '/modulo-pai-2',
            ],
        ];
        $itens      = [
            [
                'id'          => 3,
                'nome'        => 'Modulo Filho 1',
                'url'         => '/modulo-filho-1',
                'modulo_pai'  => [ 'id' => 1 ],
                'favorito_id' => 1,
            ],
            [
                'id'         => 4,
                'nome'       => 'Modulo Filho 2',
                'url'        => '/modulo-filho-2',
                'modulo_pai' => [ 'id' => 2 ],
            ],
        ];

        $mensagem         = "";
        $itensOrganizados = ModuloBO::organizarItens($itens);

        $modulos   = $itensOrganizados['modulos'];
        $favoritos = $itensOrganizados['favoritos'];

        $primeiroModuloPaiPossuiFilhos = isset($modulos[1]['filhos']);
        $this->assertTrue($primeiroModuloPaiPossuiFilhos, 'O primeiro módulo deveria conter ao menos um item.');

        $this->assertTrue(empty($favoritos) === false, 'Favoritos deveria conter um item.');
    }


}
