<?php

namespace App\Service;

use App\Entity\Principal\Aluno;
use App\Entity\Principal\ContaReceber;
use App\Entity\Principal\Contrato;
use App\Entity\Principal\Franqueada;
use App\Entity\Principal\Funcionario;
use App\Entity\Principal\Horario;
use App\Entity\Principal\HorarioAula;
use App\Entity\Principal\ItemContaReceber;
use App\Entity\Principal\Livro;
use App\Entity\Principal\Pessoa;
use App\Entity\Principal\Semestre;
use App\Entity\Principal\Turma;
use App\Entity\Principal\Usuario;
use App\Repository\Principal\AlunoRepository;
use App\Repository\Principal\BancoRepository;
use App\Repository\Principal\CargoRepository;
use App\Repository\Principal\ClassificacaoAlunoRepository;
use App\Repository\Principal\ContaReceberRepository;
use App\Repository\Principal\ContratoRepository;
use App\Repository\Principal\CursoRepository;
use App\Repository\Principal\FranqueadaRepository;
use App\Repository\Principal\FuncionarioRepository;
use App\Repository\Principal\HorarioRepository;
use App\Repository\Principal\ItemContaReceberRepository;
use App\Repository\Principal\ItemRepository;
use App\Repository\Principal\LivroRepository;
use App\Repository\Principal\ModalidadeTurmaRepository;
use App\Repository\Principal\PessoaRepository;
use App\Repository\Principal\PlanoContaRepository;
use App\Repository\Principal\SemestreRepository;
use App\Repository\Principal\TurmaRepository;
use App\Repository\Principal\UsuarioRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;


class SponteRequestDataService
{

    private Client $request;
    private static $token;
    private $uri   = 'https://integracao.sponteweb.net.br/api/v1/';
    private $uriv2 = 'https://integracao.sponteweb.net.br/api/v2/';
    private $uriv3 = 'https://integracaopp.sponteweb.com.br/api/v3/';
    private AlunoRepository $alunoRepository;
    private ClassificacaoAlunoRepository $classificacaoAlunoRepository;
    private FranqueadaRepository $franqueadaRepository;
    private EntityManagerInterface $entityManager;
    private PessoaRepository $pessoaRepository;
    private CargoRepository $cargoRepository;
    private BancoRepository $bancoRepository;
    private FuncionarioRepository $funcionarioRepository;
    private TurmaRepository $turmaRepository;
    private LivroRepository $livroRepository;
    private HorarioRepository $horarioRepository;
    private SemestreRepository $semestreRepository;
    private CursoRepository $cursoRepository;
    private ModalidadeTurmaRepository $modalidadeTurmaRepository;
    private LoggerInterface $logger;
    private ContratoRepository $contratoRepository;
    private ContaReceberRepository $contaReceberRepository;
    private UsuarioRepository $usuarioRepository;
    private ItemContaReceberRepository $itemContaReceberRepository;
    private ItemRepository $itemRepository;
    private PlanoContaRepository $contaRepository;
    private $path = __DIR__ . '/../../public';

    public function __construct(
        LoggerInterface $logger,
        AlunoRepository $alunoRepository,
        ClassificacaoAlunoRepository $classificacaoAlunoRepository,
        FranqueadaRepository $franqueadaRepository,
        PessoaRepository $pessoaRepository,
        EntityManagerInterface $entityManager,
        CargoRepository $cargoRepository,
        BancoRepository $bancoRepository,
        FuncionarioRepository $funcionarioRepository,
        TurmaRepository $turmaRepository,
        LivroRepository $livroRepository,
        HorarioRepository $horarioRepository,
        SemestreRepository $semestreRepository,
        CursoRepository $cursoRepository,
        ModalidadeTurmaRepository $modalidadeTurmaRepository,
        ContratoRepository $contratoRepository,
        ContaReceberRepository $contaReceberRepository,
        UsuarioRepository $usuarioRepository,
        ItemContaReceberRepository $itemContaReceberRepository,
        ItemRepository $itemRepository,
        PlanoContaRepository $contaRepository
    ) {
        $this->client          = new Client();
        $this->alunoRepository = $alunoRepository;
        $this->classificacaoAlunoRepository = $classificacaoAlunoRepository;
        $this->franqueadaRepository         = $franqueadaRepository;
        $this->entityManager         = $entityManager;
        $this->pessoaRepository      = $pessoaRepository;
        $this->cargoRepository       = $cargoRepository;
        $this->bancoRepository       = $bancoRepository;
        $this->funcionarioRepository = $funcionarioRepository;
        $this->turmaRepository       = $turmaRepository;
        $this->livroRepository       = $livroRepository;
        $this->horarioRepository     = $horarioRepository;
        $this->semestreRepository    = $semestreRepository;
        $this->cursoRepository       = $cursoRepository;
        $this->modalidadeTurmaRepository = $modalidadeTurmaRepository;
        $this->logger = $logger;
        $this->contratoRepository         = $contratoRepository;
        $this->contaReceberRepository     = $contaReceberRepository;
        $this->usuarioRepository          = $usuarioRepository;
        $this->itemContaReceberRepository = $itemContaReceberRepository;
        $this->itemRepository  = $itemRepository;
        $this->contaRepository = $contaRepository;
    }

    /**
     * @throws GuzzleException
     */
    public function import($escola)
    {
        // $this->processaResponsaveis($escola);
        // $this->processaFuncionarios($escola);
         // $this->processaTurmas($escola);
         $this->processAlunos($escola);
        // $this->processaMatriculas($escola);
        // $this->processaContasReceber($escola);
        // $this->processaContasAPagar($escola);
    }

    public function getToken($escola)
    {
        if (null !== self::$token) {
            return self::$token;
        }

        $res = $this->client->request(
            'POST',
            $this->uriv3 . 'Auth/login',
            [
                'json' => [
                    "email"        => "gerenteti@influx.com.br",
                    "password"     => "%Wp9Ub63#",
                    "codCliSponte" => $escola,
                ],
            ]
        );

        self::$token = json_decode((string) $res->getBody())->accessToken;

        return self::$token;
    }

    private function getAlunos($escola=38, $pagina=1)
    {
        $alunosArray = [];
        do {
            sleep(1);
            $alunos = $this->client->request(
                'GET',
                $this->uriv3 . 'alunos',
                [
                    'query'   => [
                        'CodCliSponte' => $escola,
                        'Pagina'       => $pagina,
                    ],
                    'headers' => [
                        'accept'        => 'application/json',
                        'Authorization' => 'Bearer ' . $this->getToken($escola),
                    ],
                ]
            );

            $response    = json_decode((string) $alunos->getBody(), true);
            $alunosArray = array_merge($response['data'], $alunosArray);

            $condicao = $pagina !== $response['totalPaginas'];
            if ($condicao) {
                $pagina++;
            }
        } while ($condicao);

        return $alunosArray;
    }

    /**
     * @param  $escola
     * @param  $param
     * @param  $uriPath
     * @return array
     * @throws GuzzleException
     */
    private function getData($school, $uriPath, array $params=[]): array
    {
        $paging    = 1;
        $arrayData = [];

        $defaultQuery = [
            'CodCliSponte' => $school,
            'Pagina'       => $paging,
        ];

        $queryParams = array_merge($defaultQuery, $params);
        do {
            sleep(1);
            $values = $this->client->request(
                'GET',
                $this->uriv3 . $uriPath,
                [
                    'query'   => $queryParams,
                    'headers' => [
                        'accept'        => 'application/json',
                        'Authorization' => 'Bearer ' . $this->getToken($school),
                    ],
                ]
            );

            $response  = json_decode((string) $values->getBody(), true);
            $arrayData = array_merge($response['data'], $arrayData);

            $isEmpty = false;
            if ($response['pagination']['totalPages'] == 0
                && $response['pagination']['totalCount'] == 0
            ) {
                $isEmpty = true;
            }

            $condition = $paging !== $response['pagination']['totalPages'];

            if ($condition === true) {
                $paging++;
            }

        } while ($isEmpty === false && $condition === true);

        return $arrayData;
    }

    private function getAulas()
    {
    }

    private function getAulasLivres()
    {
    }

    private function getBolsas()
    {
    }

    private function getContasPagar()
    {
    }

    private function getFollowUps()
    {
    }

    private function getResponsaveis()
    {
    }

    private function processAlunos(int $escola)
    {
        $alunos = $this->getData($escola, 'alunos');

        /*
         * @var array{
         *     alunoID: int,
         *     nomeAluno: string,
         *     emailPadrao: string,
         *     cpf: string,
         *     rg: string,
         *     dataNascimento: string,
         *     cidade: string,
         *     bairro: string,
         *     rua: string,
         *     cep: string,
         *     numeroCasa: string,
         *     sexo: string,
         *     telefone: string,
         *     telefoneComercial: string,
         *     celular: string,
         *     responsavelFinanceiroID: int,
         *     responsavelDidaticoID: int,
         *     situacaoID: int,
         *     situacao: string,
         * } $aluno
         */
        foreach ($alunos as $aluno) {
            $this->createOrUpdateAluno($escola, $aluno);
        }
    }

    private function createOrUpdateAluno($escola, $alunoArray)
    {
        $stundentId = $this->createIdSponteWithFranchise($escola, $alunoArray['alunoID']);

        $aluno = $this->alunoRepository->findOneBy(['sponte_id' => $stundentId]);

        if ($aluno === null && empty($alunoArray['cpf']) === false) {
            $pessoa = $this->pessoaRepository->findOneBy(['cnpj_cpf' => $alunoArray['cpf']]);
            if ($pessoa instanceof Pessoa) {
                $aluno = $pessoa->getAlunos();
            }
        }

        $franqueada = $this->franqueadaRepository->findOneBy(['sponte_id' => $escola]);

        if ($aluno instanceof Aluno) {
            $aluno = $this->updateAluno($aluno, $alunoArray, $franqueada);
        } else {
            $aluno = $this->createAluno($escola, $alunoArray);
        }

        $responsavelFinanceiro = $this->pessoaRepository->buscarPessoaFranqueadaSponteId($franqueada, $alunoArray['responsavelFinanceiroID']);

        if ($responsavelFinanceiro instanceof Pessoa) {
            $aluno->setResponsavelFinanceiroPessoa($responsavelFinanceiro);
        } else {
            // Quando não tiver responsável financeiro, o próprio aluno é o responsável financeiro
            $aluno->setResponsavelFinanceiroPessoa($aluno->getPessoa());
        }

        $responsavelDidatico = $this->pessoaRepository->buscarPessoaFranqueadaSponteId($franqueada, $alunoArray['responsavelDidaticoID']);

        if ($responsavelDidatico instanceof Pessoa) {
            $aluno->setResponsavelDidaticoPessoa($responsavelDidatico);
        }

        $this->entityManager->persist($aluno);
        $this->entityManager->flush();

        return $aluno;
    }

    private function createIdSponteWithFranchise($scholl, $stundentId): string
    {
        return hash('sha256', ((string) $stundentId) . ((string) $scholl));
    }

    private function updateAluno(Aluno $aluno, $alunoArray, Franqueada $escola)
    {
        $pessoa = $aluno->getPessoa();
        $aluno->setSituacao('ATI');
        $pessoa->setNomeContato($alunoArray['nomeAluno']);
        if ($alunoArray['emailPadrao'] !== null) {
            $pessoa->setEmailContato($alunoArray['emailPadrao']);
        }

        $pessoa->setCnpjCpf(!empty($alunoArray['cpf']) ? $alunoArray['cpf'] : null);
        $pessoa->setNumeroIdentidade($alunoArray['rg']);
        $pessoa->setSexo($alunoArray['sexo']);
        $pessoa->setTelefoneContato($alunoArray['telefone']);

        $pessoa->setCidade($escola->getCidade());
        $pessoa->setEstado($escola->getEstado());

        if ($alunoArray['bairro'] !== null) {
            $pessoa->setBairroEndereco($alunoArray['bairro']);
        }

        $this->processaEndereco($pessoa, $alunoArray);

        $this->entityManager->persist($pessoa);
        $this->entityManager->flush();

        return $aluno;
    }

    private function createAluno($escola, $alunoArray)
    {
        $newAluno  = new Aluno();
        $newPessoa = new Pessoa();

        $pessoaFromDb = $this->pessoaRepository->findOneBy(['cnpj_cpf' => $alunoArray['cpf']]);
        if ($pessoaFromDb instanceof Pessoa) {
            $newPessoa = $pessoaFromDb;
        }

        $franqueada = $this->franqueadaRepository->findOneBy(['sponte_id' => $escola]);

        $classificacaoAluno = $this->classificacaoAlunoRepository->findOneBy(
            [
                'franqueada' => $franqueada,
                'nome'       => 'Novo',
                'excluido'   => 0,
            ]
        );

        $newAluno->setClassificacaoAluno($classificacaoAluno);
        $newAluno->setEmancipado(false);
        $newAluno->setSituacao('ATI');
        $newAluno->setSponteId($this->createIdSponteWithFranchise($escola, $alunoArray['alunoID']));

        $newPessoa->addFranqueada($franqueada);
        $newPessoa->setNomeContato($alunoArray['nomeAluno']);
        if ($alunoArray['emailPadrao'] !== null) {
            $newPessoa->setEmailContato($alunoArray['emailPadrao']);
        }

        if ($alunoArray['dataNascimento'] !== null) {
            $newPessoa->setDataNascimento(new \DateTime($alunoArray['dataNascimento']));
        }

        $newPessoa->setCnpjCpf(!empty($alunoArray['cpf']) ? $alunoArray['cpf'] : null);
        $newPessoa->setNumeroIdentidade($alunoArray['rg']);
        $newPessoa->setSexo($alunoArray['sexo']);
        $newPessoa->setTelefoneContato($alunoArray['telefone']);

        if ($alunoArray['bairro'] !== null) {
            $newPessoa->setBairroEndereco($alunoArray['bairro']);
        }

        $newPessoa->setCidade($franqueada->getCidade());
        $newPessoa->setEstado($franqueada->getEstado());

        $this->processaEndereco($newPessoa, $alunoArray);

        $newAluno->setPessoa($newPessoa);

        $this->entityManager->persist($newAluno);
        $this->entityManager->flush();

        return $newAluno;
    }

    public function alunosSemContrato()
    {
        return [
            'Neudes Gomes de Souza',
            'Declesio Nienkotter',
            'K Line Victoria Noleto Medeiros',
            'Orle Montibeller Junior',
            'Fernanda Hickmann',
            'Evandro Freese',
            'Rayssa Medeiros Taverny',
            'Thays Lobato Santos',
            'Felipe Alecsander de Almeida',
            'William Gustavo Da Silva Almeida',
            'Ester Alice da Silva',
            'Sergio Dalabona',
            'Taise Aparecida Schulz',
            'Isabella Barros Garcia',
            'Luan Mateus Gonçalves da Rosa',
            'Ana Luiza Ferreira da Silva',
            'Eliezio Dos Santos Portela',
            'Nebison Fabiano Silveira',
            'Fabio Braun',
            'Isabelly Strapassoli',
            'Elenilde Alves Sousa',
            'Diulia Moreira',
            'Lucas Vinicius de Aragão',
            'Alisson Aparecido Nunes',
            'Lucas Sousa dos Santos',
            'Thais Hahn',
            'Nayane Gaida da Silva',
            'Maria Clara Kanis',
            'Larany Cristina Penz',
            'Adinan Bonin',
            'Priscila Withs da Costa',
            'Amábile Catafesta Neres',
            'Danielle Anacleto De Meira',
            'Melissa Monteiro Dias Wamser',
            'Max Ricardo Weitgenandt',
            'Paola Kannenberg Jungton',
            'Murilo Braatz Bueno de Oliveira',
            'Telmo  Alexandre Alves de Andrade',
            'Luan Lavandoski Guarnieri',
            'Fernando Mette',
            'Bruno Alexandre da Silva',
            'Maria Eduarda Brassiani Odelli',
            'Jonathan Mathes',
            'Ronaime Fernandes Brito da Silva',
            'Ícaro Amaral Nobre Vieira',
            'Eloah Cristine Artz',
            'João Victor Schroeder Cunha',
            'Larissa  Aparecida Fontana',
            'Jordan Cauê Metzner',
            'Lidiane dos Passos Sipriano Branger',
            'Byanka Tavares Alves Barros',
            'Isadora Brolini',
            'Marina Antonia Nardelli Milbratz',
            'Eduarda Isabelle Blunk',
            'Guilherme Wetzel Filho',
            'Marcos Eduardo de Andrade',
            'Mateus Schinatto Pereira',
            'Brenda Tayline Poffo',
            'Felipe Bernardo Hoffmann',
            'Rosymeire Oliveira da Silva',
        ];
    }

    /**
     * @param  int $escola
     * @return void
     * @throws GuzzleException
     */
    private function processaMatriculas(int $escola)
    {
        $matriculas = $this->getData($escola, 'matriculas');

        /*
         * @var array{
         *         contratoID: int,
         *         alunoID: int,
         *         nomeAluno: string,
         *         modalidade: string,
         *         tipoContrato: string,
         *         dataInicio: string,
         *         dataTermino: string,
         *         dataContrato: string,
         *         dataRescisao: string,
         *         dataMovimentacao: string,
         *         motivoDesistenciaID: string,
         *         descricaoMotivoDesistencia: string,
         *         funcionarioID: string,
         *         numeroContrato: string,
         *         estagios: array<int, array{
         *             contratoID: int,
         *             estagioID: int,
         *             nomeEstagio: string,
         *             situacaoEstagio: string,
         *             cursoID: int,
         *             nomeCurso: string,
         *             idiomaID: int,
         *             nomeIdioma: string,
         *             turmas: array<int, array{
         *                 contratoID: int,
         *                 estagioID: int,
         *                 turmaID: int,
         *                 nomeTurma: string,
         *             }>,
         *             planos: array,
         *         }>,
         *     } $matricula
         */
        foreach ($matriculas as $matricula) {
            $this->createOrUpdateContrato($matricula, $escola);
        }
    }

    /**
     * @param  $matricula
     * @param  int $escola
     * @return void
     * @throws \Exception
     */
    private function createOrUpdateContrato($matricula, int $escola)
    {
        $franqueada = $this->franqueadaRepository->findOneBy(['sponte_id' => $escola]);

        $contrato = $this->contratoRepository
            ->findOneBy(
                [
                    'franqueada' => $franqueada,
                    'sponte_id'  => $matricula['contratoID'],
                ]
            );

        // Se não existir contrato, cria um novo
        if ($contrato === null) {
            $contrato = new Contrato();
        }

        $contrato->setSponteId($matricula['contratoID']);

        $stundentId = $this->createIdSponteWithFranchise($escola, $matricula['alunoID']);

        $aluno = $this->alunoRepository->findOneBy(['sponte_id' => $stundentId]);

        // Se não existir aluno retorna para não dar erro na criação do contrato
        if ($aluno === null) {
            return;
        }

        $contrato->setAluno($aluno);
        $contrato->setFranqueada($franqueada);

        $funcionario = $this->funcionarioRepository->findOneBy(
            [
                'franqueada' => $franqueada,
                'sponte_id'  => $matricula['funcionarioID'],
            ]
        );

        // Se não tiver funcionário, pega o gerente
        if ($funcionario === null) {
            $funcionario = current(
                $this->funcionarioRepository->findBy(
                    [
                        'franqueada' => $franqueada,
                        'cargo'      => 4,
                        // Gerente
                    ]
                )
            );

            // se não tem o gerente, pega o primeiro funcionário
            if ($funcionario === null) {
                $funcionario = current(
                    $this->funcionarioRepository->findBy(
                        ['franqueada' => $franqueada]
                    )
                );
            }
        }

        // precisa
        $contrato->setResponsavelVendaFuncionario($funcionario);
        $contrato->setResponsavelCarteiraFuncionario($funcionario);

        $responsvelFinanceiroAluno = $aluno->getResponsavelFinanceiroPessoa();

        if ($responsvelFinanceiroAluno === null) {
            $responsvelFinanceiroAluno = $aluno->getPessoa();
        }

        if ($responsvelFinanceiroAluno instanceof Pessoa) {
            $contrato->setResponsavelFinanceiroPessoa($responsvelFinanceiroAluno);
        }

        $turmaDb = null;
        foreach ($matricula['estagios'] as $estagio) {
            foreach ($estagio['turmas'] as $turma) {
                $turmaDb = $this->turmaRepository->findOneBy(['franqueada' => $franqueada, 'sponte_id' => $turma['turmaID']]);
                if ($turmaDb === null) {
                    $turmaDb = $this->criaTurmaApartirDaMatricula($estagio, $franqueada, $turma['turmaID'], $turma['nomeTurma'], $matricula);
                }
            }
        }

        // se não tiver turma, cria uma padrão
        if ($turmaDb === null) {
            $turmaDb = $this->createTurmaPadrao($franqueada, $matricula);
        }

        $contrato->setLivro($turmaDb->getLivro());
        $contrato->setSemestre($turmaDb->getSemestre());
        $contrato->setModalidadeTurma($turmaDb->getModalidadeTurma());

        $contrato->setSequenciaContrato(1);
        $contrato->setSituacao('V');
        $contrato->setTipoContrato('M');
        $contrato->setDataContrato(new \DateTime($matricula['dataContrato']));
        $contrato->setDataMatricula(new \DateTime($matricula['dataContrato']));
        $contrato->setDataInicioContrato(new \DateTime($matricula['dataInicio']));
        $contrato->setDataTerminoContrato(new \DateTime($matricula['dataTermino']));

        $contrato->setTurma($turmaDb);
        $contrato->setCurso($turmaDb->getCurso());

        $this->entityManager->persist($contrato);
        $this->entityManager->flush();
    }

    /**
     * @throws GuzzleException
     */
    private function processaFuncionarios(int $escola)
    {
        $funcionarios = $this->getData($escola, 'funcionarios');

        /*
         * @var array{
         *         funcionarioID: int,
         *         nome: string,
         *         nomeCompleto: string,
         *         situacao: bool,
         *         sexo: string,
         *         cpf: string,
         *         rg: string,
         *         gestorComercial: bool,
         *         atendenteComercial: bool,
         *         professorAulasLivres: bool,
         *         professor: bool,
         *         cargo: string,
         *         consultorAlunos: bool,
         *         estadoCivil: string,
         *         cidadeNatal: string,
         *         dataNascimento: string,
         *         telefone: string,
         *         telefoneCelular: string,
         *         email: string,
         *         cep: string,
         *         cidade: string,
         *         bairro: string,
         *         rua: string,
         *         numeroEndereco: string,
         *         dataAdmissao: string,
         *         dataDemissao: string,
         *         carteiraProfissional: string,
         *         pis: string,
         *     }  $funcionario
         */
        foreach ($funcionarios as $funcionario) {
            $this->createOrUpdateFuncionario($funcionario, $escola);
        }
    }

    private function createOrUpdateFuncionario($funcionarioData, $escola)
    {
        $pessoa = $this->findOrNewPessoa($funcionarioData);

        $franqueada  = $this->franqueadaRepository->findOneBy(['sponte_id' => $escola]);
        $funcionario = $this->funcionarioRepository->findOneBy(
            [
                'franqueada' => $franqueada,
                'sponte_id'  => $funcionarioData['funcionarioID'],
            ]
        );

        /*
         * @var Funcionario $funcionario
         */
        $funcionario = $funcionario instanceof Funcionario ? $funcionario : new Funcionario();

        $cargo = $this->procuraCargo($funcionarioData);
        $funcionario->setSponteId($funcionarioData['funcionarioID']);
        $funcionario->setCargo($cargo);

        $funcionario->setFranqueada($franqueada);

        // 999 -> Banco Importação
        $banco = $this->bancoRepository->findOneBy(['codigo' => 999]);
        $funcionario->setBanco($banco);

        $pessoa = $this->preencheDadosPessoa($pessoa, $franqueada, $funcionarioData);
        $funcionario->setPessoa($pessoa);

        $funcionario->setTipoPagamento("M");
        $funcionario->setApelido(explode(' ', $funcionarioData['nome'])[0]);

        $this->entityManager->persist($funcionario);
        $this->entityManager->flush();
    }

    private function preencheDadosPessoa(Pessoa $pessoa, Franqueada $franqueada, $data)
    {
        $pessoa->addFranqueada($franqueada);
        $pessoa->setNomeContato($data['nomeCompleto']);
        if ($data['email'] !== null) {
            $pessoa->setEmailContato($data['email']);
        }

        if ($data['dataNascimento'] !== null) {
            $pessoa->setDataNascimento(new \DateTime($data['dataNascimento']));
        }

        $pessoa->setCnpjCpf(!empty($data['cpf']) ? $data['cpf'] : null);
        $pessoa->setNumeroIdentidade($data['rg']);
        $pessoa->setSexo($data['sexo']);
        $pessoa->setTelefoneContato($data['telefone']);
        $pessoa->setTelefonePreferencial($data['telefoneCelular']);

        $this->entityManager->persist($pessoa);

        return $pessoa;
    }

    private function procuraCargo($funcionarioData)
    {
        // 1    Aux. De Serviços Gerais    A    ASG
        // 2    Consultor de Vendas    A    CON
        // 3    Coordenador Pedagógico    A    COP
        // 4    Gerente    A    GER
        // 5    Instrutor    A    PRO
        // 6    Auxiliar de Coordenação    A    ACO
        // 7    Encarregado Administrativo    A    EAD
        $cargoSponteManager = [
            'Gerente'                => 4,
            'Professor'              => 5,
            'Coordenador Pedagógico' => 3,
            'Consultor'              => 2,
        ];

        if (array_key_exists($funcionarioData['cargo'], $cargoSponteManager)) {
            $cargoId = $cargoSponteManager[$funcionarioData['cargo']];
        } else {
            // Outro
            $cargoId = 8;
        }

        return $this->cargoRepository->find($cargoId);
    }

    private function findOrNewPessoa($funcionarioData)
    {
        $pessoa = $this->pessoaRepository->findOneBy(['cnpj_cpf' => $funcionarioData['cpf']]);

        if ($pessoa === null) {
            $pessoa = $this->pessoaRepository->findOneBy(['nome_contato' => $funcionarioData['nomeCompleto']]);
        }

        return ($pessoa instanceof Pessoa) ? $pessoa : new Pessoa();
    }

    /**
     * @throws GuzzleException
     */
    private function processaTurmas($escola)
    {
        $turmas = $this->getData($escola, 'turmas');

        /*
         * @var array{
         *         turmaID: int,
         *         tipoTurma: string,
         *         nomeTurma: string,
         *         cursoID: int,
         *         nomeCurso: string,
         *         nomeEstagio: string,
         *         modalidade: string,
         *         dataInicio: string,
         *         dataTermino: string,
         *         funcionarioID: int,
         *         nomeCompleto: string,
         *         alunos: array<array{alunoID: int}>
         *     } $turma
         */
        foreach ($turmas as $turma) {
             $this->createOrUpdateTurma($turma, $escola);
        }
    }

    private function createOrUpdateTurma($turmaArray, $escola)
    {
        $franqueada = $this->franqueadaRepository->findOneBy(['sponte_id' => $escola]);

        $turma = $this->turmaRepository->findOneBy(['franqueada' => $franqueada, 'sponte_id' => $turmaArray['turmaID']]);

        if ($turma === null) {
            $turma = new Turma();
        }

        if ($turmaArray['funcionarioID'] !== null) {
            $funcionario = $this->funcionarioRepository
                ->findOneBy(
                    [
                        'franqueada' => $franqueada,
                        'sponte_id'  => $turmaArray['funcionarioID'],
                    ]
                );

            if ($funcionario instanceof Funcionario) {
                $turma->setFuncionario($funcionario);
            }
        }

        $turma->setFranqueada($franqueada);
        $turma->setSponteId($turmaArray['turmaID']);
        $turma->setDescricao($turmaArray['nomeTurma']);

        $livro = $this->buscaLivro($turmaArray['nomeEstagio']);
        if ($livro === null) {
            $this->logger->error('Livro não encontrado para os dados', $turmaArray);
            return;
        }

         $turma->setLivro($livro);

         $horario = $this->buscaHorario($turmaArray['nomeTurma'], $franqueada);
         $turma->setHorario($horario);

        /*
         * Se não encontrar o semestre, busca o ultimo semestre cadastrado
         */
         $semestre = $this->buscaSemestre($turmaArray);

         $turma->setSemestre($semestre);

         $curso = $this->buscaCurso($turmaArray['nomeCurso']);
         if ($curso === null) {
            $this->logger->error('curso', $turmaArray);
         }

         $turma->setCurso($curso);
         $turma->setMaximoAlunos(99);

         $modalidadeDaTurma = $this->buscaModalidadeDaturma($turmaArray['modalidade']);
         $turma->setModalidadeTurma($modalidadeDaTurma);

         $timezone = new \DateTimeZone('America/Sao_Paulo');
         $turma->setDataInicio(new \DateTime($turmaArray['dataInicio'], $timezone));
         $turma->setDataFim(new \DateTime($turmaArray['dataTermino'], $timezone));

         $this->entityManager->persist($turma);
         $this->entityManager->flush();
    }

    private function buscaModalidadeDaturma($modalidade)
    {
        if (str_contains('Vip', $modalidade) || str_contains('VIP', $modalidade)) {
            return $this->modalidadeTurmaRepository->find(2);
        }

        if (str_contains('Personal', $modalidade) || str_contains('PERSONAL', $modalidade)) {
            return $this->modalidadeTurmaRepository->find(3);
        }

        if (str_contains('Hybrid', $modalidade) || str_contains('HYBRID', $modalidade)) {
            return $this->modalidadeTurmaRepository->find(4);
        }

        // Turma || null
        return $this->modalidadeTurmaRepository->find(1);
    }

    private function buscaLivro($nomeEstatio)
    {
        $nomeLivro = $this->processaNomeDoLivro($nomeEstatio);

        return $this->livroRepository->findOneBy(['descricao' => $nomeLivro]);
    }

    private function buscaHorario($nomeTurma, $franqueada): Horario
    {
        $horario = $this->horarioRepository->findOneBy(
            [
                'descricao'  => $nomeTurma,
                'franqueada' => $franqueada,
            ]
        );

        if ($horario === null) {
            $horario = new Horario();
        }

        $horario->setFranqueada($franqueada);
        $horario->setDescricao($nomeTurma);
        $horario->setSituacao('A');

        $this->entityManager->persist($horario);

        $this->processHorarioAula($nomeTurma, $horario);

        $this->entityManager->flush();

        return $horario;
    }

    private function buscaSemestre($turmaArray)
    {
        $nomeDaTurma = $turmaArray['nomeTurma'];
        $nomeArray   = explode(' ', $nomeDaTurma);

        $semestre = $this->semestreRepository->findOneBy(['descricao' => end($nomeArray)]);

        if ($semestre instanceof Semestre) {
            return $semestre;
        }

        $criteria = new Criteria();
        $criteria
            ->where($criteria->expr()->gte('data_inicio', new \DateTime($turmaArray['dataInicio'])))
            ->andWhere($criteria->expr()->lte('data_termino', new \DateTime($turmaArray['dataTermino'])));

        $semestre = $this->semestreRepository->matching($criteria);

        $semestreFirst = $semestre->first();
        if ($semestreFirst instanceof Semestre) {
            return $semestreFirst;
        }

        $semestres = $this->semestreRepository->findAll();

        return end($semestres);
    }

    private function buscaCurso($nomeCurso, ?Livro $livro=null)
    {
        $curosMapeados = [
            'Regular Inglês'       => 7,
        // 'COURSE1'
            'Regular Espanhol'     => 18,
        // 'COURSE2',
            'Regular Junior'       => 3,
        // 'COURSE3',
            'Regular Kids'         => 4,
        // 'COURSE4',
            'Personal'             => 29,
        // 'COURSE6',
            'Comunicação Avançada' => 2,
        // 'COURSE7',
            'Semi Intensivo'       => 11,
        // 'COURSE5',
            'Intensivo de Férias'  => 10,
        // 'COURSE8',
            'VIP'                  => 13,
        // 'COURSE11',
            'VIP ESPANHOL'         => 23,
        // 'COURSE11',
            'Vacation Plus'        => 15,
        // 'COURSE8',
        ];

        if (!array_key_exists($nomeCurso, $curosMapeados)) {
            if ($livro !== null) {
                return $livro->getCurso()->first();
            }

            return  $this->cursoRepository->find(1);
        }

        return $this->cursoRepository->find($curosMapeados[$nomeCurso]);
    }

    private function processHorarioAula($nomeTurma, Horario $horario)
    {

        if ($horario->getHorarioAulas()->count() !== 0) {
            return;
        }

        if (str_contains('Vip', $nomeTurma) === true || str_contains('Personal', $nomeTurma) === true) {
            $newHorarioAula = new HorarioAula();
            $newHorarioAula->setHorario($horario);
            $newHorarioAula->setDiaSemana('DOM');

            $horarioInicio = \DateTime::createFromFormat("H:i", '00:00');

            $newHorarioAula->setHorarioInicio($horarioInicio);
            $this->entityManager->persist($newHorarioAula);
            return;
        }

        $explodeString = explode(' ', $nomeTurma);
        $normal        = str_contains($explodeString[0], '&');

        if ($normal === true) {
            foreach (explode('&', $explodeString[0]) as $sigla) {
                $newHorarioAula = new HorarioAula();
                $newHorarioAula->setHorario($horario);
                $newHorarioAula->setDiaSemana(strtoupper(substr($sigla, 0, 3)));

                $horarioInicio = explode('/', $explodeString[1])[0];
                $horarioInicio = \DateTime::createFromFormat("H:i", $horarioInicio);
                if ($horarioInicio instanceof \DateTime) {
                    $newHorarioAula->setHorarioInicio($horarioInicio);
                }

                $this->entityManager->persist($newHorarioAula);
            }

            return;
        }

        $newHorarioAula = new HorarioAula();
        $newHorarioAula->setHorario($horario);
        if (str_contains($explodeString[0], 'Sábado') === true) {
            $newHorarioAula->setDiaSemana('SAB');

            $horarioInicio = explode('/', $explodeString[1])[0];
            $horarioInicio = \DateTime::createFromFormat("H:i", $horarioInicio);
            if ($horarioInicio instanceof \DateTime) {
                $newHorarioAula->setHorarioInicio($horarioInicio);
            }

            $this->entityManager->persist($newHorarioAula);

            return;
        }

        $newHorarioAula->setDiaSemana('DOM');
        if (str_contains($explodeString[0], 'Domingo') === true) {
            $horarioInicio = explode('/', $explodeString[1])[0];
            $horarioInicio = \DateTime::createFromFormat("H:i", $horarioInicio);
            if ($horarioInicio instanceof \DateTime) {
                $newHorarioAula->setHorarioInicio($horarioInicio);
            }

            $this->entityManager->persist($newHorarioAula);
            return;
        }

        $horarioInicio = \DateTime::createFromFormat("H:i", '00:00');

        $newHorarioAula->setHorarioInicio($horarioInicio);
        $this->entityManager->persist($newHorarioAula);
    }

    private function processaNomeDoLivro($nomeEstagio)
    {

        $nomesMapeados = [
            [
                'A Camino 1' => [
                    'A Camino 1',
                    'Libro 01',
                    'Libro 01 (InFlux)',
                    'Libro 1',
                    'Libro 1 ',
                ],
            ],
            [
                'A Camino 2' => [
                    'A Camino 2',
                    'A Camino 2 ',
                    'A Camino 2 2 horas',
                    'Libro 02',
                    'Libro 2',
                ],
            ],
            [
                'A Camino 3' => [
                    'A Camino 3',
                    'Libro 03',
                    'Libro 3',
                ],
            ],
            [
                'A Camino 4' => [
                    'A Camino 4',
                    'Libro 04',
                    'Libro 04 (InFlux)',
                ],
            ],
            [
                'Backpack 1 A' => [
                    'Backpack 01 A',
                    'Backpack 1',
                    'Backpack 1 A',
                    'Backpack 1 stage 1',
                    'Backpack 1A 2 horas',
                    'Kids 1A',
                    'Backpack 1A',
                ],
            ],
            [
                'Backpack 1 B' => [
                    'Backpack 01 B',
                    'Backpack 1 B',
                    'Backpack 1 stage 2',
                    'BACKPACK 1B',
                    'Kids 1B',
                ],
            ],
            [
                'Backpack 2 A' => [
                    'Backpack 02 A',
                    'Backpack 2 A',
                    'Backpack 2 stage 1',
                    'Backpack 2A',
                    'Backpack 2A 2 horas',
                    'Kids 2A',
                ],
            ],
            [
                'Backpack 2 B' => [
                    'Backpack 2 B',
                    'Backpack 2 B 2 horas',
                    'Backpack 2B',
                ],
            ],
            [
                'Backpack 3 A' => [
                    'Backpack 3 A',
                    'Backpack 3 stage 1',
                    'Backpack 3A',
                    'Backpack 3 A ',
                ],
            ],
            [
                'Backpack 3 B' => [
                    'Backpack 3 B',
                    ' BACKPACK 3B',
                ],
            ],
            [
                'Backpack Starter A' => [
                    'Backpack Starter A',
                    'Backpack StA',
                ],
            ],
            ['Backpack Starter B' => ['Backpack Starter B']],
            [
                'Book 1' => [
                    'Book 01',
                    'Book 01(Influx)',
                    'Book 1',
                    'Book 1 (inFlux)',
                    'Book 1 2 horas',
                    'Book 1.',
                    'inFlux Book 1',
                    'Book 1 influx',
                ],
            ],
            [
                'Book 2' => [
                    'BOOK 02',
                    'Book 02 (Influx)',
                    'Book 2',
                    'Book 2 - Stage 1',
                    'Book 2 (2 horas/aula)',
                    'Book 2 (inFlux)',
                    'Book 2 2 Horas',
                    'Book II',
                    'inFlux Book 2',
                    'Book 2 inFlux',
                    'Book 02',
                ],
            ],
            [
                'Book 3' => [
                    'Book 03',
                    'Book 03 (Influx)',
                    'Book 3',
                    'Book 3 (inFlux)',
                    'Book 3 2 horas',
                    'inFlux Book 3',
                    'Book 3 influx',
                ],
            ],
            [
                'Book 4' => [
                    'Book 04',
                    'Book 04 (Influx)',
                    'Book 4',
                    'Book 4 (inFlux)',
                    'Book 4 2 horas',
                    'inFlux Book 4 ',
                    'Book 4 inFlux',
                ],
            ],
            [
                'Book 5' => [
                    'Book 05',
                    'Book 05 (Influx)',
                    'Book 5',
                    'Book 5 (inFlux)',
                    'Book 5 2 horas',
                    'inFlux Book 5',
                    'Book 5 inFlux',
                ],
            ],
            ['Dominio' => ['Domínio A']],
            [
                'Junior 1' => [
                    'inFlux Junior 1',
                    'Jr 1 2 horas',
                    'Junior 01',
                    'Junior 1',
                    'Junior 1 ',
                    'JUNIOR 01',
                ],
            ],
            [
                'Junior 2' => [
                    'Influx Junior 2',
                    'Influx Junior 2 2 Horas',
                    'Junior 02',
                    'Junior 2',
                    'JUNIOR 02',
                ],
            ],
            [
                'Junior 3' => [
                    'Influx Junior 3',
                    'Influx Junior 3 2 horas',
                    'JUNIOR 03',
                    'Junior 3',
                    'Junior 3 ',
                    'Junior 3 2 Horas ',
                ],
            ],
            [
                'Junior Starter A' => [
                    'inFlux Junior Starter A',
                    'Junior A',
                    'Junior Starter A',
                    'Junior Starter A ',
                    'Junior StA',
                ],
            ],
            [
                'Junior Starter B' => [
                    'inFlux Junior B',
                    'Influx Junior Starter B',
                    'Junior B',
                    'Junior B 2 horas',
                    'Junior Starter B',
                    'Junior StB',
                ],
            ],
            ['Market Leader Advanced A' => ['Market Leader Advanced A']],
            ['Market Leader Advanced B' => ['Market Leader Advanced B']],
            ['Market Leader Upper intermediate A' => ['Market Leader Upper intermediate A']],
            ['Market Leader Upper intermediate B' => ['Market Leader Upper Intermediate B']],
            [
                'Market Leader intermediate A' => [
                    'Business 1',
                    'Market Leader Intermediate A',
                ],
            ],
            [
                'Market Leader intermediate B' => [
                    'Business 2 ',
                    'Market Leader intermediate B',
                ],
            ],
            [
                'Summit 1 A' => [
                    'Adv. Comunication Summit',
                    'Advanced Communication',
                    'Advanced Communication - Summit 1A',
                    'Advanced Comunication 1A',
                    'Summit 1',
                    'Summit 1 A',
                    'Summit 1A',
                    'Summit 1A ',
                    'Summit1 A',
                ],
            ],
            [
                'Summit 1 B' => [
                    'Advanced Comunication 1B',
                    'Summit 1 B',
                    'Summit 1B',
                ],
            ],
            [
                'Summit 2 A' => [
                    'Advanced Comunication 2A',
                    'Summit 2',
                    'Summit 2 A',
                    'Summit 2A',
                    'Summit 2A 2 horas',
                ],
            ],
            [
                'Summit 2 B' => [
                    'SUMMIT 2B',
                    'Summit 2 B',
                    'Summit 2 B',
                ],
            ],
            [
                'Tactics for TOEIC' => [
                    'Tactics for TOEIC',
                    'TOEIC',
                    'TOEIC Prep.',
                    'TOEIC Prep. ',
                    'Preparatório TOEIC',
                    'TOEIC Practics',
                ],
            ],
            ['Tema a Tema' => ['Tema a Tema']],
            [
                'inFlux Traveler 1' => [
                    'Traveler',
                    'Traveler ',
                    'Traveler Stage 1',
                    'Traveler - St 1',
                ],
            ],
            ['inFlux Traveler 2' => ['Traveler Stage 2']],
            ['Vacation Plus 1' => ['Vacation Plus 1']],
            ['Vacation Plus 2' => ['Vacation Plus 2']],
            ['Vacation Plus 3' => ['Vacation Plus 3']],
            ['Vacation Plus 4' => ['Vacation Plus 4']],
            ['Clockwise Upper' => ['Clockwise Upper']],
            ['Clockwise Upper Intermediate' => ['Clockwise Upper-intermediate']],
            ['Pockets Pockets' => ['Pockets Pockets']],
            ['Pockets 1 A' => ['Pockets 1 A']],
            ['Puesta a Punto' => ['Puesta a Punto']],
            ['The Big Picture A' => ['The Big Picture A']],
            ['The Big Picture B' => ['The Big Picture B']],
            ['CA in class' => ['CA in class']],
            ['AC en clase' => ['AC en clase']],
            ['Pockets 2 B' => ['Pockets 2 B']],
            ['Pockets 2 A' => ['Pockets 2 A']],
            ['Pockets 1 B' => ['Pockets 1 B']],
            [
                'Cutting Edge 3º' => [
                    'edition Cutting Edge',
                    'Cutting',
                    'Cutting Edge 3º edition',
                    'Cutting Edge 3º edition',
                ],
            ],
            ['Tema a Tema B2' => ['Tema a Tema B2']],
            ['Tema a Tema C' => ['Tema a Tema C']],
            ['Tema a Tema B2 A' => ['Tema a Tema B2 A']],
            ['Tema a Tema B2 B' => ['Tema a Tema B2 B']],
            ['Tema a Tema C A' => ['Tema a Tema C A']],
            ['Tema a Tema C B' => ['Tema a Tema C B']],
            ['Cutting Edge - new Ed A' => ['Cutting Edge – new Ed A']],
            ['Cutting Edge - new Ed B' => ['Cutting Edge - new Ed B']],
            [
                'F&F STA' => [
                    'Fluxie and Friends Starter A',
                    'Fluxie and Friends St A',
                    'Fluxie and Friends ST A',
                    'Fluxie & Friends Starter A',
                    'F&F STA',
                ],
            ],
            [
                'F&F STB' => [
                    'Fluxie and Friends Starter B',
                    'Fluxie and Friends St B',
                    'Fluxie & Friends Starter B',
                    'F&F STB',
                ],
            ],
            [
                'F&F 1A' => [
                    'Fluxie and Friends 1A',
                    'Fluxie and Friends 1 A',
                    'Fluxie and Friends 1A',
                    'F&F 1A',
                ],
            ],
            [
                'F&F 1B' => [
                    'Fluxie and Friends 1B',
                    'Fluxie and Friends 1 B',
                    'Fluxie and Friends 1B',
                    'F&F 1B',
                ],
            ],
            [
                'F&F 2A' => [
                    'Fluxie and Friends 2A',
                    'Fluxie and Friends 2 A',
                    'Fluxie and Friends 2A',
                    'F&F 2A',
                ],
            ],
            ['On Business 1' => ['On Business']],
            ['On Business 2' => ['On Business 2' ]],
            [
                'F&F 2B' => [
                    'Fluxie and Friends 2B',
                    ' Fluxie and Friends 2 B',
                    'Fluxie and Friends 2B',
                    'F&F 2B',
                ],
            ],
            ['Traveler 1' => ['Traveler 1']],
            ['Traveler 2' => [ 'Traveler 2' ]],
            [
                'Speakout Advanced 2E American Split Coursebook1' => [
                    'Speakout Advanced 2E American',
                    'Speakout Advanced 2 E American',
                    'Speakout Advanced 2E American Split Coursebook1',
                    'SpeakOut A',
                    'American [Speakout Advanced 1',
                    'American Speakout Advanced 1',
                    'American Speakout Advanced 2',
                ],
            ],
            [
                'Speakout Advanced 2E American Split Coursebook2' => [
                    'Speakout Advanced 2E American Split Coursebook2',
                    'SpeakOut B',
                    'American Speakout Advanced 2',
                ],
            ],
            ['¡A Debate! A' => ['¡A Debate! A']],
            ['¡A Debate! B' => ['¡A Debate! B']],
            ['Camino 1' => ['Camino 1']],
            ['Camino 2' => ['Camino 2']],
            ['Camino 3' => ['Camino 3']],
            ['Camino 4' => ['Camino 4']],
        ];

        foreach ($nomesMapeados  as $value) {
            foreach ($value as $key => $data) {
                $data = array_map(fn($item) => trim(strtolower($item)), $data);
                if (in_array(trim(strtolower($nomeEstagio)), $data) === true) {
                    return $key;
                }
            }
        }

        return null;
    }

    /**
     * @throws GuzzleException
     */
    private function processaResponsaveis(int $escola)
    {
        $responsaveisArray = $this->getData($escola, 'responsaveis');

        /*
         * @var array{
         *      responsavelID: int,
         *      nomeResponsavel: string,
         *      dataNascimento: string,
         *      cpf: string,
         *      rg: string,
         *      email: string,
         *      foneResidencial: string,
         *      foneCelular: string,
         *      foneComercial: string,
         *      sexo: string,
         *      rua: string,
         *      cidade: string,
         *      estado: string,
         *      bairro: string,
         *      numeroEndereco: string,
         *      cep: string,
         * } $responsavel
         */
        foreach ($responsaveisArray as $responsavel) {
            $this->createOrUpdateResponsavel($responsavel, $escola);
        }
    }

    /**
     * @param  array $responsavel
     * @param  int $escola
     * @return void
     * @throws \Exception
     */
    private function createOrUpdateResponsavel($responsavel, int $escola)
    {
        $pessoa     = $this->pessoaRepository->findOneBy(['cnpj_cpf' => $responsavel['cpf']]);
        $franqueada = $this->franqueadaRepository->findOneBy(['sponte_id' => $escola]);

        if ($pessoa === null) {
            $pessoa = $this->pessoaRepository->buscarPessoaFranqueadaSponteId($franqueada, $responsavel['responsavelID']);
        }

        if ($pessoa === null) {
            $pessoa = new Pessoa();
        }

        $pessoa->setNomeContato($responsavel['nomeResponsavel']);
        $pessoa->setSponteId($responsavel['responsavelID']);

        if (empty($responsavel['email']) === false
            && $responsavel['email'] !== null
        ) {
            $pessoa->setEmailContato($responsavel['email']);
        }

        if ($responsavel['dataNascimento'] !== null) {
            $pessoa->setDataNascimento(new \DateTime($responsavel['dataNascimento']));
        }

        $pessoa->setCnpjCpf(!empty($responsavel['cpf']) ? $responsavel['cpf'] : null);
        $pessoa->setNumeroIdentidade($responsavel['rg']);
        $pessoa->setSexo($responsavel['sexo']);

        if (empty($responsavel['foneCelular']) === false && $responsavel['foneCelular'] !== null) {
            $pessoa->setTelefoneContato($responsavel['foneCelular']);
        }

        $pessoa->addFranqueada($franqueada);

        $this->entityManager->persist($pessoa);
        $this->entityManager->flush();
    }


    private function criaTurmaApartirDaMatricula(
        $estagio,
        $franqueada,
        $turmaSponteId,
        $nomeTurma,
        $matricula
    ) {
        $turma = new Turma();

        $funcionario = current(
            $this->funcionarioRepository->findBy(
                [
                    'franqueada' => $franqueada,
                    'cargo'      => 4,
                    // Gerente
                ]
            )
        );

        if ($funcionario === null) {
            $funcionario = current(
                $this->funcionarioRepository->findBy(
                    ['franqueada' => $franqueada]
                )
            );
        }

        $turma->setFuncionario($funcionario);

        $turma->setFranqueada($franqueada);
        $turma->setSponteId($turmaSponteId);
        $turma->setDescricao($nomeTurma);

        $livro = $this->buscaLivro($estagio['nomeEstagio']);
        if ($livro === null) {
            $this->logger->error('livro', $estagio);
        }

        $turma->setLivro($livro);

        $horario = $this->buscaHorario($nomeTurma, $franqueada);
        $turma->setHorario($horario);

        $dadosBusca = [
            'nomeTurma'   => $nomeTurma,
            'dataInicio'  => $matricula['dataInicio'],
            'dataTermino' => $matricula['dataTermino'],
        ];
        $semestre   = $this->buscaSemestre($dadosBusca);

        $turma->setSemestre($semestre);

        $nomeCurso = $estagio['nomeCurso'];
        if ($nomeCurso === null) {
            $nomeCurso = '';
        }

        $curso = $this->buscaCurso($nomeCurso, $livro);
        if ($curso === null) {
            $this->logger->error('curso', $estagio);
        }

        return $this->updateTurmaCommonInfo($turma, $curso, $matricula);
    }

    private function createTurmaPadrao(Franqueada $franqueada, $matricula)
    {
        $turma = $this->turmaRepository->findOneBy(['franqueada' => $franqueada, 'sponte_id' => 'turma_padrao']);
        if ($turma instanceof Turma) {
            return $turma;
        }

        $turma = new Turma();

        $funcionario = current(
            $this->funcionarioRepository->findBy(
                [
                    'franqueada' => $franqueada,
                    'cargo'      => 4,
                    // Gerente
                ]
            )
        );

        if ($funcionario === null) {
            $funcionario = current(
                $this->funcionarioRepository->findBy(
                    ['franqueada' => $franqueada]
                )
            );
        }

        $turma->setFuncionario($funcionario);

        $turma->setFranqueada($franqueada);
        $turma->setSponteId('turma_padrao');
        $turma->setDescricao('turma padrao');

        $livro = $this->buscaLivro('Book 1');

        $turma->setLivro($livro);

        $horario = $this->buscaHorario('Sábado 00:00/00:00 B1 2023/1', $franqueada);
        $turma->setHorario($horario);

        $dadosBusca = [
            'nomeTurma'   => '',
            'dataInicio'  => $matricula['dataInicio'],
            'dataTermino' => $matricula['dataTermino'],
        ];
        $semestre   = $this->buscaSemestre($dadosBusca);

        $turma->setSemestre($semestre);

        $nomeCurso = '';
        $curso     = $this->buscaCurso($nomeCurso, $livro);

        return $this->updateTurmaCommonInfo($turma, $curso, $matricula);
    }

    /**
     * @param  int $escola
     * @return void
     * @throws GuzzleException
     */
    private function processaContasReceber(int $escola)
    {
        $contasReceber = $this->getData($escola, 'contasReceber', ['Situacao' => 1]);
        $franqueada    = $this->franqueadaRepository->findOneBy(['sponte_id' => $escola]);

        foreach ($contasReceber as $contaReceber) {
            $this->createOrUpdateContasReceber($contaReceber, $franqueada);
        }
    }

    private function createOrUpdateContasReceber($contaReceber, Franqueada $franqueada)
    {
        $escola     = $franqueada->getSponteId();
        $stundentId = $this->createIdSponteWithFranchise($escola, $contaReceber['alunoID']);

        $aluno = $this->alunoRepository->findOneBy(['sponte_id' => $stundentId]);

        $contaReceberFromDb = $this->contaReceberRepository->findOneBy(['franqueada' => $franqueada, 'sponte_id' => $contaReceber['contaReceberID']]);

        if ($contaReceberFromDb === null) {
            $contaReceberNew = new ContaReceber();
        } else {
            $contaReceberNew = $contaReceberFromDb;
        }

        if ($aluno === null) {
            dump($contaReceber);
            return;
        }

        $contaReceberNew->setAluno($aluno);
        $contaReceberNew->setFranqueada($franqueada);
        $contaReceberNew->setBolsista((bool) $contaReceber['bolsaID']);
        $contaReceberNew->setSponteId($contaReceber['contaReceberID']);
        $contaReceberNew->setValorTotal($contaReceber['valorPlano']);
        // $contaReceberNew->setDataEmissao($contaReceber['valorPlano']);
        $contaReceberNew->setSacadoPessoa($aluno->getPessoa());

        $funcionario = current(
            $this->funcionarioRepository->findBy(
                [
                    'franqueada' => $franqueada,
                    'cargo'      => 4,
                    // Gerente
                ]
            )
        );

        if ($funcionario === null) {
            $funcionario = current(
                $this->funcionarioRepository->findBy(
                    ['franqueada' => $franqueada]
                )
            );
        }

        $contaReceberNew->setVendedorFuncionario($funcionario);

        // options={"comment":"(PEN)dentes, (VEN)cidas, (NEG)ativadas, (LIQ)uidadas
        $contaReceberNew->setSituacao('PEN');
        $contaReceberNew->setUsuario($funcionario->getUsuario());
        $observacao = 'Descrição do plano de contas: ' . $contaReceber['planoContasDescricao'] . ' Tipo do Recebimento: ' . $contaReceber['tipoRecebimento'];
        $contaReceberNew->setObservacao($observacao);

        if ($contaReceber['planoContasDescricao'] === 'Parcela Regular' || $contaReceber['planoContasDescricao'] === 'Parcela Personal ') {
            $contrato = $this->contratoRepository->findBy(['aluno' => $aluno]);
            if (!empty($contrato)) {
                $contaReceberNew->setContrato(current($contrato));
            }
        }

        $this->entityManager->persist($contaReceberNew);

        $this->processaContasReceberItem($contaReceber, $contaReceberNew);

        $this->entityManager->flush();
    }

    private function processaContasReceberItem($contaReceber, ContaReceber $contaReceberNew)
    {
        $item        = $this->itemRepository->findOneBy(
            [
                'franqueada' => $contaReceberNew->getFranqueada(),
                'descricao'  => $contaReceber['planoContasDescricao'],
            ]
        );
        $planoContas = $this->contaRepository->find(150);

        foreach ($contaReceber['parcelas'] as $value) {
            $parcela = $this->itemContaReceberRepository->findOneBy(
                [
                    'conta_receber'    => $contaReceberNew,
                    'numero_sequencia' => $value['numeroParcela'],
                ]
            );
            if ($parcela === null) {
                $parcela = new ItemContaReceber();
            }

            $parcela->setContaReceber($contaReceberNew);
            $parcela->setPlanoConta($planoContas);
            $parcela->setNumeroSequencia($value['numeroParcela']);
            $parcela->setItem($item);
            $parcela->setPercentualDesconto(0);
            $parcela->setValorDesconto(0);
            $parcela->setValor($value['valor']);
            if ($value['dataVencimento'] !== null) {
                $parcela->setDataVencimento(new \DateTime($value['dataVencimento']));
            }

            $this->entityManager->persist($parcela);
        }//end foreach
    }

    private function processaContasAPagar(int $escola)
    {
        $contasAPagar = $this->getData($escola, 'contasPagar');
    }

    private function buscaContasApagar(int $escola, $pagina=1)
    {
        $alunosArray = [];
        do {
            sleep(1);
            $alunos = $this->client->request(
                'GET',
                $this->uriv3 . 'contasPagar',
                [
                    'query'   => [
                        'CodCliSponte' => $escola,
                        'Pagina'       => $pagina,
                    ],
                    'headers' => [
                        'accept'        => 'application/json',
                        'Authorization' => 'Bearer ' . $this->getToken($escola),
                    ],
                ]
            );

            $response    = json_decode((string) $alunos->getBody(), true);
            $alunosArray = array_merge($response['data'], $alunosArray);

            $condicao = $pagina !== $response['totalPaginas'];
            if ($condicao) {
                $pagina++;
            }
        } while ($condicao);

        return $alunosArray;
    }

    /**
     * @param  Pessoa $newPessoa
     * @param  array $alunoArray
     * @return void
     */
    private function processaEndereco(Pessoa $newPessoa, $alunoArray): void
    {
        if ($newPessoa->setEndereco($alunoArray['rua']) !== null) {
            $newPessoa->setEndereco($alunoArray['rua']);
        }

        if ($newPessoa->setCepEndereco($alunoArray['cep']) !== null) {
            $newPessoa->setCepEndereco($alunoArray['cep']);
        }

        if ($newPessoa->setCepEndereco($alunoArray['cep']) !== null) {
            $newPessoa->setCepEndereco($alunoArray['cep']);
        }
    }

    /**
     * @param  Turma $turma
     * @param  $curso
     * @param  $matricula
     * @return Turma
     * @throws \Exception
     */
    private function updateTurmaCommonInfo(Turma $turma, $curso, $matricula): Turma
    {
        $turma->setCurso($curso);
        $turma->setMaximoAlunos(99);

        $modalidadeDaTurma = $this->buscaModalidadeDaturma('');
        $turma->setModalidadeTurma($modalidadeDaTurma);

        $turma->setDataInicio(new \DateTime($matricula['dataInicio']));
        $turma->setDataFim(new \DateTime($matricula['dataTermino']));

        $this->entityManager->persist($turma);
        $this->entityManager->flush();

        return $turma;
    }

    private function saveArrayToJson(array $contasReceber, string $string, int $escola)
    {
        $fp = fopen($this->path . $escola . '_' . $string . '.json', 'w');
        fwrite($fp, json_encode($contasReceber));
        fclose($fp);
    }

}
