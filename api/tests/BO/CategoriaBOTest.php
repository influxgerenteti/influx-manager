<?php
namespace App\Tests\BO;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\BO\Principal\CategoriaBO;
use App\Helper\FunctionHelper;

/**
 *
 * @author Luiz Antonio Costa
 */
class CategoriaBOTest extends KernelTestCase
{

    /**
     *
     * @var \App\Repository\Principal\CategoriaRepository
     */
    private $categoriaRepository;

    /**
     *
     * @var \App\BO\Principal\CategoriaBO
     */
    private $categoriaBO;

    protected function setUp()
    {
        $kernel = self::bootKernel();
        $this->categoriaRepository = $kernel->getContainer()->get('doctrine')->getManager()->getRepository(\App\Entity\Principal\Categoria::class);
        $this->categoriaBO         = new CategoriaBO();
    }

    public function testCategoriaEncontrado()
    {
        $objetoORM    = null;
        $mensagemErro = "";
        $this->categoriaBO->verificaCategoriaExiste($this->categoriaRepository, 1, $mensagemErro, $objetoORM);
        $this->assertTrue($objetoORM !== null, FunctionHelper::mostrarTextoUnitVermelho("Nao foi encontrado o objeto."));
    }

    public function testCategoriaNaoEncontrado()
    {
        $objetoORM    = null;
        $mensagemErro = "";
        $this->categoriaBO->verificaCategoriaExiste($this->categoriaRepository, 999, $mensagemErro, $objetoORM);
        $this->assertTrue($objetoORM === null, FunctionHelper::mostrarTextoUnitVermelho("Encontrou o objeto, onde nao era para ter encontrado."));
    }

    public function testVerificouComSucesso()
    {
        $objetoORM    = null;
        $mensagemErro = "";
        $this->categoriaBO->verificaCategoriaExiste($this->categoriaRepository, 1, $mensagemErro, $objetoORM);
        $this->assertEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("Nao passou no teste de verificado com sucesso"));
    }

    public function testVerificouComFalha()
    {
        $objetoORM    = null;
        $mensagemErro = "";
        $objetoORM    = null;
        $mensagemErro = "";
        $this->categoriaBO->verificaCategoriaExiste($this->categoriaRepository, 999, $mensagemErro, $objetoORM);
        $this->assertNotEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("Nao passou no teste de verificacao com falha"));
    }

    protected function tearDown()
    {
        $this->categoriaRepository = null;
        $this->categoriaBO         = null;
    }


}
