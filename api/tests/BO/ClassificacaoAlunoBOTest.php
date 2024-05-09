<?php
namespace App\Tests\BO;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\BO\Principal\ClassificacaoAlunoBO;
use App\Helper\FunctionHelper;
use App\Helper\VariaveisCompartilhadas;

/**
 *
 * @author Rodrigo de Souza Fernandes (GATI labs)
 */
class ClassificacaoAlunoBOTest extends KernelTestCase
{

    /**
     *
     * @var \App\Repository\Principal\ClassificacaoAlunoRepository
     */
    private $classificacaoAlunoRepository;

    /**
     *
     * @var \App\BO\Principal\ClassificacaoAlunoBO
     */
    private $classificacaoAlunoBO;

    protected function setUp()
    {
        $kernel = self::bootKernel();
        $this->classificacaoAlunoRepository = $kernel->getContainer()->get('doctrine')->getManager()->getRepository(\App\Entity\Principal\ClassificacaoAluno::class);
        $this->classificacaoAlunoBO         = new ClassificacaoAlunoBO($kernel->getContainer()->get('doctrine')->getManager());
    }

    public function testVerificaNomeExistente()
    {
        $mensagemErro = "";
        $nome         = 'Especial Novo';
        $condition    = $this->classificacaoAlunoBO->verificaNomeExiste($this->classificacaoAlunoRepository, 1, $nome, 0, $mensagemErro);
        $this->assertTrue($condition === true, FunctionHelper::mostrarTextoUnitVermelho("Nome inexistente na base de dados.\nError:" . $mensagemErro));
    }

    public function testVerificaNomeInexistente()
    {
        $mensagemErro = "";
        $nome         = 'Estrela';
        $condition    = $this->classificacaoAlunoBO->verificaNomeExiste($this->classificacaoAlunoRepository, 999, $nome, 0, $mensagemErro);
        $this->assertFalse($condition === true, FunctionHelper::mostrarTextoUnitVermelho("Nome já consta na base de dados.\nError:" . $mensagemErro));
    }

    public function testClassificacaoAlunoEncontrado()
    {
        $objetoORM    = null;
        $mensagemErro = "";
        VariaveisCompartilhadas::$franqueadaID = 1;
        $this->classificacaoAlunoBO->verificaClassificacaoAlunoExiste($this->classificacaoAlunoRepository, 1, $mensagemErro, $objetoORM);
        $this->assertTrue($objetoORM !== null, FunctionHelper::mostrarTextoUnitVermelho("Nao foi encontrado o objeto."));
    }

    public function testClassificacaoAlunoNaoEncontrado()
    {
        $objetoORM    = null;
        $mensagemErro = "";
        $this->classificacaoAlunoBO->verificaClassificacaoAlunoExiste($this->classificacaoAlunoRepository, 999, $mensagemErro, $objetoORM);
        $this->assertTrue($objetoORM === null, FunctionHelper::mostrarTextoUnitVermelho("Encontrou o objeto, onde nao era para ter encontrado."));
    }

    protected function tearDown()
    {
        $this->classificacaoAlunoRepository = null;
        $this->classificacaoAlunoBO         = null;
    }


}
