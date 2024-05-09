<?php
namespace App\BO\Principal;

use Doctrine\ORM\EntityManagerInterface;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Dayan Freitas
 */
class ServicoBO extends GenericBO
{

    /**
     *
     * @var \App\Repository\Principal\ServicoRepository
     */
    private static $servicoRepository;

    function __construct(EntityManagerInterface $entityManager)
    {

        parent::configuraGenericBO(
            [
                "franqueadaRepository"     => $entityManager->getRepository(\App\Entity\Principal\Franqueada::class),
                "alunoRepository"          => $entityManager->getRepository(\App\Entity\Principal\Aluno::class),
                "funcionarioRepository"    => $entityManager->getRepository(\App\Entity\Principal\Funcionario::class),
                "itemRepository"           => $entityManager->getRepository(\App\Entity\Principal\Item::class),
                "formaPagamentoRepository" => $entityManager->getRepository(\App\Entity\Principal\FormaPagamento::class),
            ]
        );
    }

    /**
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaQuantidadeExiste ($parametros, &$mensagemErro)
    {

        if ((isset($parametros[ConstanteParametros::CHAVE_QUANTIDADE]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_QUANTIDADE]) === false)) {
            return true;
        }

        $mensagemErro = "Quantidade de item nao selecionada";
        return false;
    }

    /**
     * @param string $data Ponteiro da data
     * @param string $mensagemErro Mensagem de erro a retornar pro front-end
     *
     * @return boolean
     */
    public function converterData (&$data, &$mensagemErro)
    {
        return self::converteData($data, $mensagemErro);
    }

    /**
     * Configura os parametros obrigatorios para criacao do registro
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaCamposRelacionaisObrigatorios(&$parametros, &$mensagemErro)
    {
        if (self::verificaFranqueadaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === true) {
            if (self::verificaAlunoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_ALUNO]) === true) {
                if (self::verificaFuncionarioExisteBO($parametros, $mensagemErro, ConstanteParametros::CHAVE_FUNCIONARIO, $parametros[ConstanteParametros::CHAVE_FUNCIONARIO]) === true) {
                    if (self::verificaItemExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_ITEM]) === true) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Verifica os parametros não obrigatórios
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaRelacionamentosOpcionais(&$parametros, &$mensagemErro)
    {
        $bRetornoFormaCobranca = true;
        if ((isset($parametros[ConstanteParametros::CHAVE_FORMA_COBRANCA]) === true) && (empty($parametros[ConstanteParametros::CHAVE_FORMA_COBRANCA]) === false)) {
            $bRetornoFormaCobranca = self::verificaFormaPagamentoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FORMA_COBRANCA], ConstanteParametros::CHAVE_FORMA_COBRANCA);
        }

        return $bRetornoFormaCobranca;
    }

    /**
     * Realiza a verificacao das regras para permitir a criacao do registro
     *
     * @param array $parametros  Ponteiro de array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     *
     * @return boolean
     */
    public function podeSalvar(&$parametros, &$mensagemErro)
    {

        if ($this->verificaCamposRelacionaisObrigatorios($parametros, $mensagemErro) === true) {
            if ($this->verificaRelacionamentosOpcionais($parametros, $mensagemErro) === true) {
                if ($this->verificaQuantidadeExiste($parametros, $mensagemErro) === true) {
                    if ($this->converterData($parametros[ConstanteParametros::CHAVE_DATA_SOLICITACAO], $mensagemErro) === true) {
                        if ($this->converterData($parametros[ConstanteParametros::CHAVE_DATA_CONCLUSAO], $mensagemErro) === true) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    /**
     * Verifica se existe o servico no banco de dados, se existir, ele retorna no ultimo parametro na funcao, caso contrario, ele preenchera a msg de erro e retornara falso
     *
     * @param \App\Repository\Principal\ServicoRepository $servicoRepository
     * @param integer $id Chave primaria do servico
     * @param string $mensagemErro Msg de erro para ser retornada ao front-end
     * @param \App\Entity\Principal\Servico|null $servicoORM Retorno no caso de sucesso
     *
     * @return boolean true|false
     */
    public static function verificaServicoExite(\App\Repository\Principal\ServicoRepository $servicoRepository, $id, &$mensagemErro, &$servicoORM)
    {

        $servicoORM = $servicoRepository->find($id);
        if (is_null($servicoORM) === true) {
            $mensagemErro = "Servico não encontrada na base de dados.";
            return false;
        }

        return true;
    }

    /**
     * Verifica se existe o servico no banco de dados, se existir, ele retorna no ultimo parametro na funcao, caso contrario, ele preenchera a msg de erro e retornara falso
     *
     * @param \App\Repository\Principal\ServicoRepository $servicoRepository
     * @param integer $id Chave primaria do servico
     * @param string $mensagemErro Msg de erro para ser retornada ao front-end
     * @param \App\Entity\Principal\Servico|null $servicoORM Retorno no caso de sucesso
     *
     * @return boolean true|false
     */
    public static function verificaServicoExiteId(\App\Repository\Principal\ServicoRepository $servicoRepository, $id, &$mensagemErro, &$servicoORM)
    {
        $servicoORM = $servicoRepository->buscarServicoId($id);
        if (is_null($servicoORM) === true) {
            $mensagemErro = "Servico não encontrada na base de dados.";
            return false;
        }

        return true;
    }


}
