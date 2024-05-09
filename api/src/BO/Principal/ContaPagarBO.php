<?php
namespace App\BO\Principal;

use App\Helper\ConstanteParametros;
use Doctrine\ORM\EntityManagerInterface;

/**
 *
 * @author Luiz Antonio Costa
 */
class ContaPagarBO extends GenericBO
{


    function __construct(EntityManagerInterface $entityManager)
    {
        parent::configuraGenericBO(
            [
                "usuarioRepository"           => $entityManager->getRepository(\App\Entity\Principal\Usuario::class),
                "franqueadaRepository"        => $entityManager->getRepository(\App\Entity\Principal\Franqueada::class),
                "condicaoPagamentoRepository" => $entityManager->getRepository(\App\Entity\Principal\CondicaoPagamento::class),
                "pessoaRepository"            => $entityManager->getRepository(\App\Entity\Principal\Pessoa::class),
                "planoContaRepository"        => $entityManager->getRepository(\App\Entity\Principal\PlanoConta::class),
                "formaPagamentoRepository"    => $entityManager->getRepository(\App\Entity\Principal\FormaPagamento::class),
            ]
        );
    }

    /**
     * Valida a Pessoa pelo indice de Fornecedor e configura o indice de fornecedor pessoa com o objeto a ser gravado
     *
     * @param array $parametros Ponteiro de Array de parametros da requisicao
     * @param string $mensagemErro Mensagem de erro a retornar pro front-end
     *
     * @return boolean
     */
    protected function verificaFornecedorPessoaExiste(&$parametros, &$mensagemErro)
    {
        $parametros[ConstanteParametros::CHAVE_PESSOA] = $parametros[ConstanteParametros::CHAVE_FORNECEDOR_PESSOA];
        return self::verificaPessoaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FORNECEDOR_PESSOA], ConstanteParametros::CHAVE_FORNECEDOR_PESSOA, true);
    }

    /**
     * Valida se os parametros que sao de relacionamento, sao validos, se algum deles nao for valido, ele ira retornar falso
     *
     * @param array $parametros Ponteiro de Array de parametros da requisicao
     * @param string $mensagemErro Mensagem de erro a retornar pro front-end
     *
     * @return boolean
     */
    protected function verificaParametrosRelacionadosValidos(&$parametros, &$mensagemErro)
    {
        if (self::verificaFranqueadaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === true) {
            if ($this->verificaFornecedorPessoaExiste($parametros, $mensagemErro) === true) {
                if (self::verificaUsuarioExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_USUARIO]) === true) {
                    if (self::verificaFormaPagamentoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FORMA_COBRANCA], ConstanteParametros::CHAVE_FORMA_COBRANCA) === true) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Verifica o timestamp da data de emissão se é menor do que o timestamp deste exato momento
     *
     * @param \DateTime $data_emissao Data de Emissão que o usuário informou no front end
     *
     * @return boolean
     */
    protected function verificaDatetimeDataEmissaoMenorQueDatetimeAgora($data_emissao, &$mensagemErro)
    {
        $timestamp_emissao = $data_emissao->getTimestamp();
        $timestamp_agora   = new \Datetime();
        if ($timestamp_emissao > $timestamp_agora->getTimestamp()) {
            $mensagemErro = "Data de emissão maior do que data de hoje";
            return false;
        }

        return true;
    }

    /**
     * Converte a data de string JS do campo de Data_Emissao para o formato JS
     *
     * @param array $parametros Ponteiro de Array de parametros da requisicao
     * @param string $mensagemErro Mensagem de erro a retornar pro front-end
     *
     * @return boolean
     */
    protected function converteDataEmissao(&$parametros, &$mensagemErro)
    {
        \App\Helper\FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_NF_DATA_EMISSAO], $parametros[ConstanteParametros::CHAVE_NF_DATA_EMISSAO]);
        if ($parametros[ConstanteParametros::CHAVE_NF_DATA_EMISSAO] === false) {
            $mensagemErro .= "Ocorreu um erro na formatação de Data no campo Data Emissão. Formato de data não reconhecida.";
            return false;
        }

        return true;
    }

    /**
     * Realiza a verificacao das regras para permitir ou nao a criacao do registro
     *
     * @param array $parametros Ponteiro do array para realizar a formatacao
     * @param string $mensagemErro Mensagem de erro a retornar pro front-end
     *
     * @return boolean
     */
    public function podeCriar(&$parametros, &$mensagemErro)
    {
        return $this->verificaParametrosRelacionadosValidos($parametros, $mensagemErro);
    }

    /**
     * Realiza a verificacao das regras para permitir ou nao a atualizacao do registro
     *
     * @param array $parametros Ponteiro do array para realizar a formatacao
     * @param string $mensagemErro Mensagem de erro a retornar pro front-end
     *
     * @return boolean
     */
    public function podeAtualizar(&$parametros, &$mensagemErro)
    {
        return self::verificaFormaPagamentoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FORMA_COBRANCA], ConstanteParametros::CHAVE_FORMA_COBRANCA) === true;
    }

    /**
     * Busca a conta a pagar atraves da ID
     *
     * @param \App\Repository\Principal\ContaPagarRepository $contaPagarRepository
     * @param int $id Chave primaria do Registro
     * @param string $mensagemErro Mensagem de erro a retornar pro front-end
     * @param null|\App\Entity\Principal\ContaPagar $retornoORM
     * @param null|integer $hydration
     *
     * @return boolean
     */
    public static function verificaContaPagarExiste($contaPagarRepository, $id, &$mensagemErro, &$retornoORM=null, $hydration=null)
    {
        $retornoORM = $contaPagarRepository->buscarContaPagar($id, $hydration);
        if (is_null($retornoORM) === true) {
            $mensagemErro .= "O ID informado não corresponde a nenhuma conta a pagar.";
            return false;
        }

        return true;
    }

    /**
     * Verifica se a data de entrada da nota é maior ou igual a data de emissão.
     *
     * @param \DateTime $data_entrada Data de entrada da nota
     * @param \DateTime $data_emissao Data de emissão da nota
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     *
     * @return boolean
     */
    protected static function verificaDataEntradaMaiorOuIgualQueEmissao($data_entrada, $data_emissao, &$mensagemErro)
    {
        if ((empty($data_entrada) === false) && ($data_entrada < $data_emissao)) {
            $mensagemErro .= "Data de entrada não pode ser inferior a data de emissão.";
            return false;
        }

        return true;
    }


}
