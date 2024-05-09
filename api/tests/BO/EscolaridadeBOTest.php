<?php
namespace App\Tests\BO;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Entity\Principal\Escolaridade;
use App\BO\Principal\EscolaridadeBO;
use App\Helper\FunctionHelper;

/**
 *
 * @author Rodrigo de Souza Fernandes (GATI labs)
 */
class EscolaridadeBOTest extends KernelTestCase
{

    /**
     *
     * @var \App\Repository\Principal\EscolaridadeRepository
     */
    private $escolaridadeRepository;

    /**
     *
     * @var \App\BO\Principal\EscolaridadeBO
     */
    private $escolaridadeBO;

    protected function setUp()
    {
        $kernel = self::bootKernel();
        $this->escolaridadeRepository = $kernel->getContainer()->get('doctrine')->getManager()->getRepository(Escolaridade::class);
        $this->escolaridadeBO         = new EscolaridadeBO();
    }

    public function testEscolaridadeEncontrado()
    {
        $objetoORM    = null;
        $mensagemErro = "";
        $this->escolaridadeBO->verificaEscolaridadeExiste($this->escolaridadeRepository, 1, $mensagemErro, $objetoORM);
        $this->assertTrue($objetoORM !== null, FunctionHelper::mostrarTextoUnitVermelho("Nao foi encontrado o objeto."));
    }

    public function testEscolaridadeNaoEncontrado()
    {
        $objetoORM    = null;
        $mensagemErro = "";
        $this->escolaridadeBO->verificaEscolaridadeExiste($this->escolaridadeRepository, 999, $mensagemErro, $objetoORM);
        $this->assertTrue($objetoORM === null, FunctionHelper::mostrarTextoUnitVermelho("Encontrou o objeto, onde nao era para ter encontrado."));
    }

    protected function tearDown()
    {
        $this->escolaridadeRepository = null;
        $this->escolaridadeBO         = null;
    }


}
