<?php
namespace App\Tests\BO;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Entity\Principal\RelacionamentoAluno;
use App\BO\Principal\RelacionamentoAlunoBO;
use App\Helper\FunctionHelper;

/**
 *
 * @author Rodrigo de Souza Fernandes (GATI labs)
 */
class RelacionamentoAlunoBOTest extends KernelTestCase
{

    /**
     *
     * @var \App\Repository\Principal\RelacionamentoAlunoRepository
     */
    private $relacionamentoAlunoRepository;

    /**
     *
     * @var \App\BO\Principal\RelacionamentoAlunoBO
     */
    private $relacionamentoAlunoBO;

    protected function setUp()
    {
        $kernel = self::bootKernel();
        $this->relacionamentoAlunoRepository = $kernel->getContainer()->get('doctrine')->getManager()->getRepository(RelacionamentoAluno::class);
        $this->relacionamentoAlunoBO         = new RelacionamentoAlunoBO();
    }

    public function testRelacionamentoAlunoEncontrado()
    {
        $objetoORM    = null;
        $mensagemErro = "";
        $this->relacionamentoAlunoBO->verificaRelacionamentoAlunoExiste($this->relacionamentoAlunoRepository, 1, $mensagemErro, $objetoORM);
        $this->assertTrue($objetoORM !== null, FunctionHelper::mostrarTextoUnitVermelho("Nao foi encontrado o objeto."));
    }

    public function testRelacionamentoAlunoNaoEncontrado()
    {
        $objetoORM    = null;
        $mensagemErro = "";
        $this->relacionamentoAlunoBO->verificaRelacionamentoAlunoExiste($this->relacionamentoAlunoRepository, 999, $mensagemErro, $objetoORM);
        $this->assertTrue($objetoORM === null, FunctionHelper::mostrarTextoUnitVermelho("Encontrou o objeto, onde nao era para ter encontrado."));
    }

    protected function tearDown()
    {
        $this->relacionamentoAlunoRepository = null;
        $this->relacionamentoAlunoBO         = null;
    }


}
