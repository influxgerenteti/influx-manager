<?php
namespace App\Helper;

/**
 *
 * @author Luiz Antonio Costa
 */
class FunctionHelper
{
    private static $COR_PHPUNIT_NORMAL   = "\e[0m";
    private static $COR_PHPUNIT_VERMELHO = "\e[41;97m";
    private static $COR_PHPUNIT_AMARELO  = "\e[43;30m";
    private static $COR_PHPUNIT_VERDE    = "\e[42;30m";

    /**
     * Apresenta o background em vermelho com as letras brancas
     *
     * @param string $texto mensagem a ser exibida
     *
     * @return string
     */
    public static function mostrarTextoUnitVermelho($texto)
    {
        return self::$COR_PHPUNIT_VERMELHO . $texto . self::$COR_PHPUNIT_NORMAL;
    }

    /**
     * Apresenta o background em amarelo com as letras brancas
     *
     * @param string $texto mensagem a ser exibida
     *
     * @return string
     */
    public static function mostrarTextoUnitAmarelo($texto)
    {
        return self::$COR_PHPUNIT_AMARELO . $texto . self::$COR_PHPUNIT_NORMAL;
    }

    /**
     * calcula diferença entre horas, retorna hora:minutos
     *
     * @param string $mensagem
     */
    public function calculaTempo($hora_inicial, $hora_final) {
        $i = 1;
      //  $tempo_total;
        
        $tempos = array($hora_final, $hora_inicial);

        
        foreach($tempos as $tempo) {
            $segundos = 0;
            list($h, $m) = explode(':', $tempo);
            
            $segundos += $h * 3600;
            $segundos += $m * 60;
            //$segundos += $s;
            
            $tempo_total[$i] = $segundos;
            
            $i++;
        }
        $segundos = $tempo_total[1] - $tempo_total[2];
        
        $horas = floor($segundos / 3600);
        $segundos -= $horas * 3600;
        $minutos = str_pad((floor($segundos / 60)), 2, '0', STR_PAD_LEFT);
        $segundos -= $minutos * 60;
        $segundos = str_pad($segundos, 2, '0', STR_PAD_LEFT);
        
        return "$horas:$minutos";
    }

    /**
     * calcula diferença entre horas, retorna valor em segundos
     *
     * @param string $mensagem
     */
    public function calculaTempoSegundos($hora_inicial, $hora_final) {
        $i = 1;
      //  $tempo_total;
        
        $tempos = array($hora_final, $hora_inicial);

        
        foreach($tempos as $tempo) {
            $segundos = 0;
            list($h, $m) = explode(':', $tempo);
            
            $segundos += $h * 3600;
            $segundos += $m * 60;
            //$segundos += $s;
            
            $tempo_total[$i] = $segundos;
            
            $i++;
        }
        $segundos = $tempo_total[1] - $tempo_total[2];
        
        return "$segundos";
        }



    /**
     * Apresenta o background em verde com as letras brancas
     *
     * @param string $texto mensagem a ser exibida
     *
     * @return string
     */
    public static function mostrarTextoUnitVerde($texto)
    {
        return self::$COR_PHPUNIT_VERDE . $texto . self::$COR_PHPUNIT_NORMAL;
    }

    /**
     * Função que ira setar os parametros no objeto ORM, chamara setNomepropriedade(valor), conforme exemplificado no DOC do $params
     *
     * @param array $parametros    array indexado com as propriedades da classe e valores<br>"[data_ativo_login]=>valor" ficara "objeto->setDataAtivoLogin(valor)"
     * @param Object $objetoORM Objeto ORM para ser realizado o set dos objetos
     */
    public static function setParams($parametros, &$objetoORM)
    {
        try {
            foreach ($parametros as $chave => $valor) {
                $campo  = explode("_", $chave);
                $metodo = "set";
                for ($count = 0; $count < count($campo); $count++) {
                    $metodo .= ucfirst($campo[$count]);
                }
     
                if (method_exists($objetoORM, $metodo) === true) {
                         $objetoORM->{$metodo}($valor);
                }
            
            }
        } catch (\Exception $e) {
            throw $e;
        }
        
        
    }

    /**
     * Envia um E-mail com os dados passados
     *
     * @param string $msg          Retorno pro front
     * @param \Swift_Mailer $mailer       Objeto do SwiftMailler
     * @param mixed $remetente    String ou array("teste@teste.com" => "Meu nome eh teste")
     * @param mixed $destinatario String ou array("teste@teste.com" => "Meu nome eh teste")
     * @param string $assunto      Assunto do E-mail
     * @param string $corpo        Texto que irá no body da
     *                             msg
     * @param array $arquivos     Arquivos que iram no anexo<br>Exemplo: [0]=>"/var/www/html/foundationlabs/public/uploads/arquivo.pdf"
     *
     * @return boolean
     */
    public function enviarEmail(&$msg, \Swift_Mailer $mailer, $remetente, $destinatario, $assunto, $corpo, $arquivos=[])
    {
        $retorno = false;
        try {
            $message = new \Swift_Message();
            $message->setFrom($remetente);
            $message->setTo($destinatario);
            $message->setSubject($assunto);
            $message->setBody($corpo, 'text/html');
            if (count($arquivos) > 0) {
                foreach ($arquivos as $indice => $caminho) {
                    $message->attach(\Swift_Attachment::fromPath($caminho));
                }
            }

            $emailsEnviados = $mailer->send($message);
            if ($emailsEnviados > 0) $retorno = true;
        } catch (\Exception $ex) {
            $msg = "Aconteceu um erro ao enviar o e-mail: " . $ex->getMessage();
        }

        return $retorno;
    }

    /**
     * Retorna o array de resultados de uma determinada query ou null
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query montada do repositorio
     * @param boolean $retornaPrimeiroResultado Se for necessário retornar apenas o primeiro resultado
     *
     * @return array|NULL
     */
    public static function retornaArrayNull(\Doctrine\ORM\QueryBuilder $queryBuilder, $retornaPrimeiroResultado=false)
    {
        $retorno = $queryBuilder->getQuery()->getArrayResult();

        if (count($retorno) > 0) {
            if ($retornaPrimeiroResultado === true) {
                $retorno = $retorno[0];
            }

            return $retorno;
        }

        return null;
    }

    /**
     * Formata o valor string para double(2 precisao, exemplo: 100.500.500,55, resultado: 100500500.55
     *
     * @param string $valor Ponteiro para realizar formatacao para double
     */
    public static function formataValorDouble(&$valor)
    {
        $valor = str_replace(".", "", $valor);
        $valor = str_replace(",", ".", $valor);
    }


    /**
     * Converte a data string enviada pelo front-end e o transforma e atribui o DateTime ao $retornoObjeto
     *
     * @param string $dataString
     * @param false|\DateTime $retornoObjeto
     */
    public static function formataCampoDateTimeJS($dataString, &$retornoObjeto=null)
    {
        $retornoObjeto = \DateTime::createFromFormat("Y-m-d\TH:i:s.uP", $dataString);
        return $retornoObjeto;
    }

     /**
     * Converte a data string enviada pelo front-end e o transforma e atribui o DateTime ao $retornoObjeto
     *
     * @param string $dataString
     * @param false|\DateTime $retornoObjeto
     */
    public static function formataCampoDateTime($dataString, &$retornoObjeto=null)
    {
        $retornoObjeto = \DateTime::createFromFormat("Y-m-d H:i:s", $dataString);
        return $retornoObjeto;
    }


    /**
     * Converte a data de DateTime para o formato do front-end
     *
     * @param \DateTime $dataDateTime
     * @param false|\DateTime $retornoDataString
     */
    public static function formataCampoJSDateTime($dataDateTime, &$retornoDataString=null)
    {
        $retornoDataString = $dataDateTime->format('Y-m-d\TH:i:s.uP');
        return $retornoDataString;
    }

    /**
     * Converte o horario string em objeto DateTime.<br> O formato aceito, eh 24H(ou seja: 14:30)
     *
     * @param string $timeString
     * @param false|\DateTime $retornoObjeto
     */
    public static function formataCampoTimeJS($timeString, &$retornoObjeto=null)
    {
        $retornoObjeto = \DateTime::createFromFormat("H:i", $timeString);
    }

    /**
     * Monta o componente de paginacao para realizar a paginacao
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param int $pagina
     * @param int $numeroItensPorPagina
     * @param array $opcoes Opcoes para realizar a paginacao verificar as opcoes no 'see'
     *
     * @see \Knp\Component\Pager\Paginator::paginate()
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination Componente de retorno
     */
    public static function montaPaginatorPaginacao(\Doctrine\ORM\QueryBuilder $queryBuilder, $pagina, $numeroItensPorPagina, $opcoes=[])
    {
        $query = $queryBuilder->getQuery();
        $query->setHydrationMode(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $paginator = new \Knp\Component\Pager\Paginator();
        return $paginator->paginate($query, $pagina, $numeroItensPorPagina, $opcoes);
    }

    /**
     * Monta dois objetos \DateTime com o dia inicial e final para o mes informado
     *
     * @param int $mes
     * @param \DateTime $dataMesInicio possui a data com o mes informado para o ano atual com o primeiro dia do mes
     * @param \DateTime $dataMesFinal possui a data com o mes informado para o ano atual com o ultimo dia do mes
     */
    public static function montaPrimeiroUltimoDiaMesAnoAtual($mes, &$dataMesInicio, &$dataMesFinal)
    {
        $anoAtual      = new \DateTime();
        $anoAtual      = $anoAtual->format("Y");
        $dataMesInicio = \DateTime::createFromFormat("Y-m-d H:i:s", $anoAtual . "-" . $mes . "-1 00:00:00");
        $dataMesFinal  = new \DateTime($dataMesInicio->format("Y-m-t"));
        $dataMesFinal->setTime(23, 59, 59);
    }

    /**
     * Função para ordenar array por chave
     *
     * @param array $arrayParaOrdenacao Array para ordenação
     * @param string $chaveOrdenacao Chave para realizar a ordenação do array
     * @param number $ordenacao SORT_ASC ou SORT_DESC <b>OBS: Não é string, são constantes próprias do PHP</b>
     * @param boolean $bMultiDimensionalArray Identificador de array multidimensional
     *
     * @todo Ler o que falta no código implementar na função
     *
     * @example $teste = [
     *              [
     *                  "numero_parcela_documento" => 5,
     *              ],
     *              [
     *                  "numero_parcela_documento" => 2
     *              ],
     *              [
     *                  "numero_parcela_documento" => 3
     *              ],
     *              [
     *                  "numero_parcela_documento" => 4
     *              ],
     *              [
     *                  "numero_parcela_documento" => 1
     *              ],
     *          ];<br>
     *          O retorno da função quando passado o SORT_ASC será<br>
     *          $teste = [
     *              [
     *                  "numero_parcela_documento" => 1,
     *              ],
     *              [
     *                  "numero_parcela_documento" => 2
     *              ],
     *              [
     *                  "numero_parcela_documento" => 3
     *              ],
     *              [
     *                  "numero_parcela_documento" => 4
     *              ],
     *              [
     *                  "numero_parcela_documento" => 5
     *              ],
     *          ];
     *
     * @return array Array ordenado
     */
    public static function ordenarArrayPorChave($arrayParaOrdenacao, $chaveOrdenacao, $ordenacao, $bMultiDimensionalArray=false)
    {
        if ($bMultiDimensionalArray === true) {
            $arrayOrdenado = [];
            foreach ($arrayParaOrdenacao as &$arrayMultiDimensional) {
                // TODO: Falta realizar a busca em outros indices de array
                foreach ($arrayMultiDimensional as $chave => $valor) {
                    if (isset($arrayOrdenado[$chave]) === false) {
                        $arrayOrdenado[$chave] = [];
                    }

                    $arrayOrdenado[$chave][] = $valor;
                }
            }

            array_multisort($arrayOrdenado[$chaveOrdenacao], $ordenacao, $arrayParaOrdenacao);
            return $arrayParaOrdenacao;
        } else {
            // TODO: Função incompleta, falta ordenar diretamente no indice solicitado
            $arrayClonado = [];
            array_push($arrayClonado, $arrayParaOrdenacao);
            return self::ordenarArrayPorChave($arrayClonado, $chaveOrdenacao, $ordenacao, true);
        }//end if
    }

    /**
     * Retorna uma lista de resultados
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     *
     * @return array|NULL
     */
    public static function retornaResultados(\Doctrine\ORM\QueryBuilder $queryBuilder)
    {
        return $queryBuilder->getQuery()->getArrayResult();
    }

    /**
     * Retorna um único resultado da query ou nulo
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param bool $asArray
     *
     * @return array|NULL
     */
    public static function retornaPrimeiroResultado(\Doctrine\ORM\QueryBuilder $queryBuilder, $asArray=true)
    {
        $queryBuilder->setMaxResults(1);
        $hydrate = null;
        if ($asArray === true) {
            $hydrate = \Doctrine\ORM\Query::HYDRATE_ARRAY;
        }

        return $queryBuilder->getQuery()->getOneOrNullResult($hydrate);
    }

    /**
     * Compara se a $dataA <= $dataB
     *
     * @param \DateTime $dataA
     * @param \DateTime $dataB
     * @param int $diasDiferenca
     *
     * @return boolean
     */
    public static function comparaDataAMenorIgualDataB($dataA, $dataB, &$diasDiferenca)
    {
        $dateInterval  = $dataA->diff($dataB);
        $diasDiferenca = (int) $dateInterval->format("%R%a");
        if ($diasDiferenca < 0) {
            return false;
        }

        return true;
    }

    /**
     * Busca a primeira key do array -> usado para quando não se tem certeza se começa no indice 0, null se não tiver
     *
     * @param array $arr
     *
     * @return boolean
     */
    public static function getPrimeiraKeyArray($arr)
    {
        foreach ($arr as $key => $unused) {
            return $key;
        }

        return null;
    }

    /**
     * Busca se o ambiente que está rodando é o ambiente de desenvolvimento
     *
     * @return boolean
     */
    public static function ehAmbienteDev()
    {
        return $_ENV["APP_ENV"] === 'dev';
    }

    /**
     * Retorna as configurações pra conexão com o banco
     *
     * @return array
     */
    public static function getConfigBanco()
    {
        return [
            'driver'   => 'mysql',
            'username' => getenv('DATABASE_PRINCIPAL_USER'),
            'password' => getenv('DATABASE_PRINCIPAL_PASSWORD'),
            'host'     => getenv('DATABASE_PRINCIPAL_HOST'),
            'database' => getenv('DATABASE_PRINCIPAL_NAME'),
            'port'     => getenv('DATABASE_PRINCIPAL_PORT'),
        ];
    }



    /**
     * Transforma um valor decimal (entre 0 e 1) no seu correspondente em porcentagem. Ex.: 0.5321 vira 53,21%
     *
     * @param float $value
     *
     * @return string
     */
    public static function decimalToPercentage($value)
    {
        return str_replace('.', ',', (number_format($value * 100, 2) . '%'));
    }


}
