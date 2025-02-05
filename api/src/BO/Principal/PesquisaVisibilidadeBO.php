<?php
namespace App\BO\Principal;

use App\Helper\ConstanteParametros;
use Doctrine\ORM\EntityManagerInterface;

/**
 *
 * @author Dayan Freitas
 */
class PesquisaVisibilidadeBO extends GenericBO
{


    function __construct(EntityManagerInterface $entityManager)
    {
        parent::configuraGenericBO(
            [
                "franqueadaRepository" => $entityManager->getRepository(\App\Entity\Principal\Franqueada::class),
            ]
        );
    }


    /**
     * Verifica campos relacionados a entidade
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaCamposRelacionaisObrigatorios(&$parametros, &$mensagemErro)
    {
        if ($this->verificaFranqueadaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === true) {
            return true;
        }

        return false;
    }


    /**
     * Verifica se os campos relacionais estao validos e indica se pode salvar ou não
     *
     * @param array $parametros Ponteiro de array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     *
     * @return boolean
     */
    public function podeSalvar(&$parametros, &$mensagemErro)
    {
        if ($this->verificaCamposRelacionaisObrigatorios($parametros, $mensagemErro) === true) {
            return true;
        }

        return false;
    }

    /**
     * Realiza as verificacoes nos campos relacionaveis e configura os indices com os objetos
     * Se algum valor nao existir ele retornara false
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function podeAlterar(&$parametros, &$mensagemErro)
    {
        return $this->configuraCamposRelacionaisOpcionais($parametros, $mensagemErro);
    }


    private function configuraCamposRelacionaisOpcionais($parametros, &$mensagemErro)
    {
        return true;
    }

    /**
     * Verifica se a pesquisa de visibilidade existe na base de dados
     *
     * @param \App\Repository\Principal\PesquisaVisibilidadeRepository $pesquisaVisibilidadeRepository Repositorio de pesquisa de visibilidade
     * @param integer $id Chave primaria da Pesquisa de visibilidad
     * @param string $mensagemErro Retorno de erro para front end
     * @param null|\App\Entity\Principal\PesquisaVisibilidade $objetoRetorno Retorna o objeto encontrado ou nulo
     * @param boolean $retornarObjeto Se deve retornar como array
     *
     * @return boolean
     */
    public static function verificaPesquisaExiste(\App\Repository\Principal\PesquisaVisibilidadeRepository $pesquisaVisibilidadeRepository, $id, &$mensagemErro, &$objetoRetorno=null, $retornarObjeto=false)
    {

        $objetoRetorno = $pesquisaVisibilidadeRepository->buscarPorId($id, $retornarObjeto);
        if ($objetoRetorno === null) {
            $mensagemErro = "Pesquisa não encontrada na base de dados.";
            return false;
        }

        return true;
    }

    /**
     * Configura os parametros para a parte de atualização
     *
     * @param array $parametros
     * @param \App\Entity\Principal\PesquisaVisibilidade $objetoORM
     */
    public function configuraParametros($parametros, &$objetoORM)
    {
        \App\Helper\FunctionHelper::setParams($parametros, $objetoORM);
    }


}
