<?php
namespace App\Tests\BO;

use App\BO\Principal\TurmaBO;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Marcelo Andre Naegeler
 */
class TurmaBOTest extends KernelTestCase
{

    /**
     *
     * @var \App\BO\Principal\TurmaBO
     */
    private $turmaBO;

    protected function setUp()
    {
        $kernel        = self::bootKernel();
        $this->turmaBO = new TurmaBO($kernel->getContainer()->get('doctrine')->getManager());
    }

    public function testValidarDatas()
    {
        $parametros = [
            ConstanteParametros::CHAVE_CURSO             => 1,
            ConstanteParametros::CHAVE_MODALIDADE_TURMA  => 1,
            ConstanteParametros::CHAVE_LIVRO             => 1,
            ConstanteParametros::CHAVE_FUNCIONARIO       => 1,
            ConstanteParametros::CHAVE_SALA_FRANQUEADA   => 1,
            ConstanteParametros::CHAVE_HORARIO           => 1,
            ConstanteParametros::CHAVE_VALOR_HORA_LINHAS => 1,
            ConstanteParametros::CHAVE_SEMESTRE          => 1,
        ];

        $mensagem = '';

        $this->turmaBO->podeCriar($parametros, $mensagem);
        $this->assertEquals('Data de início é inválida.', $mensagem);

        $parametros[ConstanteParametros::CHAVE_DATA_INICIO] = '2018-11-13T00:00:00.000Z';
        $this->turmaBO->podeCriar($parametros, $mensagem);
        $this->assertEquals('Data de término é inválida.', $mensagem);

        $parametros[ConstanteParametros::CHAVE_DATA_INICIO] = '2018-11-13T00:00:00.000Z';
        $parametros[ConstanteParametros::CHAVE_DATA_FIM]    = '2018-11-10T00:00:00.000Z';
        $this->turmaBO->podeCriar($parametros, $mensagem);
        $this->assertEquals('Data de término deve ser superior a data de início.', $mensagem);

        $parametros[ConstanteParametros::CHAVE_DATA_INICIO] = '2018-11-13T00:00:00.000Z';
        $parametros[ConstanteParametros::CHAVE_DATA_FIM]    = '2018-12-14T00:00:00.000Z';
        $podeCriar = $this->turmaBO->podeCriar($parametros, $mensagem);
        $this->assertTrue($podeCriar);
    }


}
