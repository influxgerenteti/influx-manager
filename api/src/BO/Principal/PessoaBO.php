<?php
namespace App\BO\Principal;

use App\Helper\ConstanteParametros;
use Doctrine\ORM\EntityManagerInterface;

/**
 *
 * @author Luiz Antonio Costa
 */
class PessoaBO extends GenericBO
{


    function __construct(EntityManagerInterface $entityManager)
    {
        parent::configuraGenericBO(
            [
                "franqueadaRepository" => $entityManager->getRepository(\App\Entity\Principal\Franqueada::class),
                "pessoaRepository"     => $entityManager->getRepository(\App\Entity\Principal\Pessoa::class),
                "bancoRepository"      => $entityManager->getRepository(\App\Entity\Principal\Banco::class),
                "planoContaRepository" => $entityManager->getRepository(\App\Entity\Principal\PlanoConta::class),
                "estadoRepository"     => $entityManager->getRepository(\App\Entity\Principal\Estado::class),
                "cidadeRepository"     => $entityManager->getRepository(\App\Entity\Principal\Cidade::class),
            ]
        );
    }

    /**
     * Verifica se nenhuma das sequencias abaixo foi digitada, ela eh necessaria pois uma vez que se aplicarmos o algoritmo do CPF sobre um numero todo igual como "333.333.333-33" teoricamente os digitos verificadores estao corretos, mas este NAO e um numero valido.
     *
     * @param integer $cpf CPF sem formatacao
     *
     * @return boolean
     */
    private static function verificaSequenciaInvalidaCpf($cpf)
    {
        if ($cpf === '00000000000'
            || $cpf === '11111111111'
            || $cpf === '22222222222'
            || $cpf === '33333333333'
            || $cpf === '44444444444'
            || $cpf === '55555555555'
            || $cpf === '66666666666'
            || $cpf === '77777777777'
            || $cpf === '88888888888'
            || $cpf === '99999999999'
        ) {
                return true;
        }

        return false;
    }

    /**
     * Verificam se os dois digitos verificadores sao validos de acordo com o algoritmo do CPF.(Ver link)
     *
     * @param integer $cpf CPF sem formatacao
     *
     * @link   https://www.geradorcpf.com/algoritmo_do_cpf.htm
     * @return boolean
     */
    private static function digitosVerificadoresInvalidoCpf($cpf)
    {
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }

            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] !== ((string) $d)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verifica se o CNPJ informado eh composto por valores iguais. Essa verificacao eh necessaria pois, se aplicarmos o algoritmo do CNPJ sobre um numero todo igual como "33.333.333-3333/33", teoricamente os digitos verificadores estao corretos, mesmo este NAO sendo um numero valido.
     *
     * @param integer $cnpj CNPJ sem formatacao
     *
     * @return boolean
     */
    private static function verificaSequenciaInvalidaCnpj($cnpj)
    {
        if ($cnpj === '00000000000000'
            || $cnpj === '11111111111111'
            || $cnpj === '22222222222222'
            || $cnpj === '33333333333333'
            || $cnpj === '44444444444444'
            || $cnpj === '55555555555555'
            || $cnpj === '66666666666666'
            || $cnpj === '77777777777777'
            || $cnpj === '88888888888888'
            || $cnpj === '99999999999999'
        ) {
                return true;
        }

        return false;
    }

    /**
     * Calculo dos digitos do CNPJ conforme algoritimo.(Ver Link)
     *
     * @param integer $cnpj CNPJ sem formatacao
     *
     * @link   https://www.geradorcnpj.com/algoritmo_do_cnpj.htm
     * @return boolean
     */
    private static function digitosVerificadoresValidosCnpj($cnpj)
    {
        $j = 5;
        $k = 6;

        $soma1 = 0;
        $soma2 = 0;

        for ($i = 0; $i < 13; $i++) {
            if ($j === 1) {
                $j = 9;
            }

            if ($k === 1) {
                $k = 9;
            }

            $soma2 += ($cnpj[$i] * $k);

            if ($i < 12) {
                $soma1 += ($cnpj[$i] * $j);
            }

            $k--;
            $j--;
        }

        if ($soma1 % 11 < 2) {
            $digito1 = 0;
        } else {
            $digito1 = 11 - $soma1 % 11;
        }

        if ($soma2 % 11 < 2) {
            $digito2 = 0;
        } else {
            $digito2 = 11 - $soma2 % 11;
        }

        return (($cnpj[12] == $digito1) && ($cnpj[13] == $digito2));
    }

    /**
     * Realiza a validacao do CPF, atraves do tamanho da string(padrao 11), de sequencias invalidas e dos digitos verificadores, referencia da logica esta no link.
     *
     * @param string $cpf CPF formatado ou nao formatado
     *
     * @link   https://www.geradorcpf.com/script-validar-cpf-php.htm
     * @return boolean
     */
    private static function validaCpf(&$cpf, &$mensagemErro)
    {
        $retorno = true;
        // Limpa a formatacao do CPF(caso venha formatado)
        $cpf = preg_replace("/[^\d]/", "", $cpf);
        $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);

        if (strlen($cpf) !== 0) {
            if (self::verificaSequenciaInvalidaCpf($cpf) === true) {
                $retorno      = false;
                $mensagemErro = "A sequencia informada corresponde a um CPF invalido.";
            } else {
                if (self::digitosVerificadoresInvalidoCpf($cpf) === true) {
                    $retorno      = false;
                    $mensagemErro = "O CPF informado é invalido.";
                }
            }
        }

        return $retorno;
    }

    /**
     * Realiza a validacao do CNPJ, atraves do tamanho da string(padrao 14), de sequencias invalidas e dos digitos verificadores, referencia da logica esta no link.
     *
     * @param string $cnpj CNPJ Formatado ou nao formatado
     *
     * @link   https://www.geradorcnpj.com/script-validar-cnpj-php.htm
     * @return boolean
     */
    private static function validaCnpj(&$cnpj, &$mensagemErro)
    {
        $retorno = true;
        // Limpa a formatacao do CNPJ(caso venha formatado)
        $cnpj = preg_replace("/[^\d]/", "", $cnpj);
        $cnpj = str_pad($cnpj, 14, '0', STR_PAD_LEFT);
        if (strlen($cnpj) !== 14) {
            $retorno      = false;
            $mensagemErro = "O tamanho do CNPJ não é valido";
        } else {
            if (self::verificaSequenciaInvalidaCnpj($cnpj) === true) {
                $retorno      = false;
                $mensagemErro = "A sequencia informada corresponde a um CNPJ inválido.";
            } else {
                if (self::digitosVerificadoresValidosCnpj($cnpj) === false) {
                    $retorno      = false;
                    $mensagemErro = "O CNPJ informado é inválido.";
                }
            }
        }

        return $retorno;
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
     * Verifica e configura os campos que necessitam de relacionamento que sao opcionais na edicao
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function configuraCamposRelacionaisOpcionais(&$parametros, &$mensagemErro)
    {
        $bRetornoEstado           = true;
        $bRetornoCidade           = true;
        $bRetornoNaoExisteCPFCNPJ = true;
        $bRetornoPlanoContaExiste = true;
        $bRetornoBanco            = true;
        $bRetornoDataNascimento   = true;

        if ((isset($parametros[ConstanteParametros::CHAVE_BANCO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_BANCO]) === false)) {
            $bRetornoBanco = self::verificaBancoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_BANCO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_NASCIMENTO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_NASCIMENTO]) === false)) {
            \App\Helper\FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_DATA_NASCIMENTO], $parametros[ConstanteParametros::CHAVE_DATA_NASCIMENTO]);
            if ($parametros[ConstanteParametros::CHAVE_DATA_NASCIMENTO] === false) {
                $mensagemErro          .= "Ocorreu um erro na formatação de Data no campo Data de Nascimento. Formato de data não reconhecida.";
                $bRetornoDataNascimento = false;
            }
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_ESTADO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_ESTADO]) === false)) {
            $bRetornoEstado = $this->verificaEstadoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_ESTADO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_CIDADE]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_CIDADE]) === false)) {
            $bRetornoCidade = $this->verificaCidadeExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_CIDADE]);
            if (($bRetornoEstado === true) && ($bRetornoCidade === true)) {
                if ($parametros[ConstanteParametros::CHAVE_CIDADE]->getEstado()->getId() !== $parametros[ConstanteParametros::CHAVE_ESTADO]->getId()) {
                    $bRetornoCidade = false;
                    $mensagemErro   = "A Cidade informada não pertence ao estado selecionado.";
                }
            }
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_CNPJ_CPF]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_CNPJ_CPF]) === false)) {
            $id = null;
            if (isset($parametros[ConstanteParametros::CHAVE_ID]) === true) {
                $id = $parametros[ConstanteParametros::CHAVE_ID];
            }

            if ($this->isCgcValido($parametros[ConstanteParametros::CHAVE_CNPJ_CPF], $mensagemErro, ($parametros[ConstanteParametros::CHAVE_TIPO_PESSOA] === "F")) === false) {
                $bRetornoNaoExisteCPFCNPJ = false;
            }

            $existeCPFCNPJ = $this->verificaSeExisteCPFCNPJ(self::getPessoaRepository(), $parametros[ConstanteParametros::CHAVE_CNPJ_CPF], $mensagemErro, ($parametros[ConstanteParametros::CHAVE_TIPO_PESSOA] === "F"), $id);
            if ($existeCPFCNPJ === true) {
                $bRetornoNaoExisteCPFCNPJ = false;
            }
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_PLANO_CONTA]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_PLANO_CONTA]) === false)) {
            $bRetornoPlanoContaExiste = self::verificaPlanoContaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_PLANO_CONTA]);
            if ($bRetornoPlanoContaExiste === true) {
                if ($this->verificaExisteFilhosPlanoContaBO($parametros, $mensagemErro) === true) {
                    $bRetornoPlanoContaExiste = false;
                }
            }
        }

        return ($bRetornoEstado && $bRetornoCidade && $bRetornoNaoExisteCPFCNPJ && $bRetornoPlanoContaExiste && $bRetornoBanco && $bRetornoDataNascimento);
    }

    /**
     * Verifica se o CPF/CNPJ eh valido
     *
     * @param string $cnpjCpf CPF/CNPJ formatado ou nao formatado
     * @param string $mensagemErro Mensagem de Erro a ser retornada para o front-end
     * @param boolean $isCpf Flag para realizar a verificacao por CPF
     *
     * @return boolean
     */
    public static function isCgcValido(&$cnpjCpf, &$mensagemErro, $isCpf=false)
    {
        if (is_null($cnpjCpf) === true) {
            return true;
        }

        if ($isCpf === false) {
            return self::validaCnpj($cnpjCpf, $mensagemErro);
        } else {
            return self::validaCpf($cnpjCpf, $mensagemErro);
        }
    }

    /**
     * Verifica se a pessoa existe na base de dados
     *
     * @param \App\Repository\Principal\PessoaRepository $pessoaRepository Repositorio de pessoa
     * @param integer $id Chave primaria da Pessoa
     * @param string $mensagemErro Retorno de erro para front end
     * @param null|\App\Entity\Principal\Pessoa $objetoRetorno Retorna o objeto encontrado ou nulo
     * @param boolean $retornarObjeto Se deve retornar como array
     *
     * @return boolean
     */
    public static function verificaPessoaExiste(\App\Repository\Principal\PessoaRepository $pessoaRepository, $id, &$mensagemErro, &$objetoRetorno=null, $retornarObjeto=false)
    {
        $objetoRetorno = $pessoaRepository->buscarPorId($id, $retornarObjeto);
        if ($objetoRetorno === null) {
            $mensagemErro = "Pessoa não encontrada na base de dados.";
            return false;
        }

        return true;
    }

    /**
     * Busca todos os registros da tabela Pessoa atraves do CPF/CNPJ informado
     *
     * @param \App\Repository\Principal\PessoaRepository $pessoaRepository Repositorio da entidade Pessoa
     * @param string $cpfCnpj CPF/CNPJ a ser pesquisado
     * @param string $mensagemErro Retorno de erro para front end
     * @param \App\Entity\Principal\Pessoa[] $pessoaCollection Retorno para o objeto
     * @param boolean $isCpf Flag para pesquisar por CPF ou CNPJ
     *
     * @return boolean true|false
     */
    public static function buscaPessoasExistentes(\App\Repository\Principal\PessoaRepository $pessoaRepository, $cpfCnpj, &$mensagemErro, &$pessoaCollection, $isCpf=true)
    {
        $pessoaCollection = $pessoaRepository->buscarCpfCnpj($cpfCnpj, null, $isCpf);
        if (is_null($pessoaCollection) === true) {
            $mensagemErro = "Não foi encontrado nenhuma pessoa com o CPF/CNPJ informado";
            return false;
        }

        return true;
    }

    /**
     * Verifica se o valor informado eh um CPF
     *
     * @param string $cpf CPF formatado ou nao formatado
     *
     * @return boolean
     */
    public function isCpf($cpf)
    {
        return strlen($cpf) === 11;
    }

    /**
     * Verifica se existe um CPF/CNPJ na base
     *
     * @param \App\Repository\Principal\PessoaRepository $pessoaRepository Repositorio da entidade Pessoa
     * @param string $cpfCnpj CPF/CNPJ a ser pesquisado
     * @param string $mensagemErro Mensagem de erro
     * @param boolean $isCpf Flag para identificar se eh pessoa fisica ou juridica
     * @param integer|null $id ID da pessoa a alterar
     *
     * @return boolean true|false
     */
    public static function verificaSeExisteCPFCNPJ(\App\Repository\Principal\PessoaRepository $pessoaRepository, $cpfCnpj, &$mensagemErro, $isCpf=true, $id=null)
    {
        $pessoasPorCgc = $pessoaRepository->buscarCpfCnpj($cpfCnpj, $id, $isCpf);
        $existePessoa  = empty($pessoasPorCgc) === false;
        if ($existePessoa === true) {
            $mensagemErro = "O CPF/CNPJ já existe na base de dados.";
        }

        return $existePessoa;
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
            if ($this->configuraCamposRelacionaisOpcionais($parametros, $mensagemErro) === true) {
                return true;
            }
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

    /**
     * Configura os parametros para a parte de atualização
     *
     * @param array $parametros
     * @param \App\Entity\Principal\Pessoa $objetoORM
     */
    public function configuraParametros($parametros, &$objetoORM)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === false)) {
            $franqueadaORM = $parametros[ConstanteParametros::CHAVE_FRANQUEADA];
            unset($parametros[ConstanteParametros::CHAVE_FRANQUEADA]);
            if (get_class($franqueadaORM) === \App\Entity\Principal\Franqueada::class) {
                $objetoORM->addFranqueada($franqueadaORM);
            }
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_ESTADO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_ESTADO]) === false)) {
            $estadoORM = $parametros[ConstanteParametros::CHAVE_ESTADO];
            unset($parametros[ConstanteParametros::CHAVE_ESTADO]);
           // if (get_class($estadoORM) === \App\Entity\Principal\Estado::class) {
                $objetoORM->setEstado($estadoORM);
           // }
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_CIDADE]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_CIDADE]) === false)) {
            $cidadeORM = $parametros[ConstanteParametros::CHAVE_CIDADE];
            unset($parametros[ConstanteParametros::CHAVE_CIDADE]);
          //  if (get_class($cidadeORM) === \App\Entity\Principal\Cidade::class) {
                $objetoORM->setCidade($cidadeORM);
          //  }
        }

        if (isset($parametros[ConstanteParametros::CHAVE_NEGATIVADO]) === true) {
            $objetoORM->setNegativado($parametros[ConstanteParametros::CHAVE_NEGATIVADO]);
            unset($parametros[ConstanteParametros::CHAVE_NEGATIVADO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_CONSULTA_CREDITO]) === true) && (is_null($parametros[ConstanteParametros::CHAVE_DATA_CONSULTA_CREDITO]) === false)) {
            $data = $parametros[ConstanteParametros::CHAVE_DATA_CONSULTA_CREDITO];
            unset($parametros[ConstanteParametros::CHAVE_DATA_CONSULTA_CREDITO]);
            \App\Helper\FunctionHelper::formataCampoDateTimeJS($data, $data);
            $objetoORM->setDataConsultaCredito($data);
        }

        $campos_formatados = [
            ConstanteParametros::CHAVE_NUMERO_IDENTIDADE,
            ConstanteParametros::CHAVE_TELEFONE_CONTATO,
            ConstanteParametros::CHAVE_TELEFONE_PREFERENCIAL,
            ConstanteParametros::CHAVE_TELEFONE_PROFISSIONAL,
        ];
        for ($x = 0, $xMax = count($campos_formatados); $x < $xMax; $x++) {
            if (isset($parametros[$campos_formatados[$x]]) === true) {
                $parametros[$campos_formatados[$x]] = preg_replace('/[^\d]/i', '', $parametros[$campos_formatados[$x]]);
            }
        }

        \App\Helper\FunctionHelper::setParams($parametros, $objetoORM);
    }


}
