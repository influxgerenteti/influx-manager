<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\MensagensAjuda;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Principal\Modulo;

class MensagensAjudaFixtures extends Fixture implements DependentFixtureInterface
{


    public function load(ObjectManager $manager)
    {
        $mensagensAjuda = new MensagensAjuda();
        $mensagensAjuda->setModulo($this->getReference(ModuloFixtures::MODULO_CURSOS_REFERENCE));
        $mensagensAjuda->setIdentificadorElemento("form-descricao");
        $mensagensAjuda->setMensagem("Mensagem do banco com um teste de texto para testar um teste de texto dinâmico.");
        $mensagensAjuda->setTitulo("Super dica:");
        $mensagensAjuda->setLink("http://localhost:8081/dashboard");
        $manager->persist($mensagensAjuda);

        $mensagensAjuda2 = new MensagensAjuda();
        $mensagensAjuda2->setModulo($this->getReference(ModuloFixtures::MODULO_CURSOS_REFERENCE));
        $mensagensAjuda2->setIdentificadorElemento("form-sigla");
        $mensagensAjuda2->setMensagem("Esta é uma dica de sigla. Aqui deve conter um sigla de identificação do curso. Ex.: ING (Inglês).");
        $mensagensAjuda2->setTitulo("Preenchimento:");
        $manager->persist($mensagensAjuda2);

        $mensagensAjuda3 = new MensagensAjuda();
        $mensagensAjuda3->setModulo($this->getReference(ModuloFixtures::MODULO_FORMULARIO_FOLLOWUP));
        $mensagensAjuda3->setIdentificadorElemento("form-formulario_follow_up_inicial");
        $mensagensAjuda3->setMensagem("Indicará nas telas em que este formulario for utilizado, se deve ser apresentado como 'formulario inicial' ou como 'formulario geral'.");
        $mensagensAjuda3->setTitulo("Observação:");
        $manager->persist($mensagensAjuda3);

        $mensagensAjuda4 = new MensagensAjuda();
        $mensagensAjuda4->setModulo($this->getReference(ModuloFixtures::MODULO_FORMULARIO_FOLLOWUP));
        $mensagensAjuda4->setIdentificadorElemento("form-formulario_followup_cadastro_convenio");
        $mensagensAjuda4->setMensagem("Marcando este campo, fará com que este formulario seja apenas apresentado na tela de cadastro/alteração de convênio.");
        $mensagensAjuda4->setTitulo("Observação:");
        $manager->persist($mensagensAjuda4);

        $mensagensAjuda5 = new MensagensAjuda();
        $mensagensAjuda5->setModulo($this->getReference(ModuloFixtures::MODULO_CADASTROS_LISTAGEM_CONVENIO_REGIONAL_REFERENCE));
        $mensagensAjuda5->setIdentificadorElemento("form-filtros-lista-convenio-nacional_nacional_cidade");
        $mensagensAjuda5->setMensagem("Marcando este filtro, buscará os registros marcados como 'nacionais' e buscará também, os registros que pertencem a cidade da franqueada selecionada.");
        $mensagensAjuda5->setTitulo("Observação:");
        $manager->persist($mensagensAjuda5);

        $mensagemFormaRecebimento = new MensagensAjuda();
        $mensagemFormaRecebimento->setModulo($this->getReference(ModuloFixtures::MODULO_CONTAS_RECEBER_REFERENCE));
        $mensagemFormaRecebimento->setIdentificadorElemento("form-forma_recebimento");
        $mensagemFormaRecebimento->setTitulo("Formas de recebimento:");
        $mensagemFormaRecebimento->setMensagem("Só pode-se quitar contas a receber com formas de recebimento a vista ou a forma já configurada nela.");
        $manager->persist($mensagemFormaRecebimento);

        $mensagensAjudaFiltroRapidoNomeAluno = new MensagensAjuda();
        $mensagensAjudaFiltroRapidoNomeAluno->setModulo($this->getReference(ModuloFixtures::MODULO_ALUNO_REFERENCE));
        $mensagensAjudaFiltroRapidoNomeAluno->setIdentificadorElemento("filtroRapido-aluno_nome_aluno");
        $mensagensAjudaFiltroRapidoNomeAluno->setTitulo("Filtro:");
        $mensagensAjudaFiltroRapidoNomeAluno->setMensagem("Filtro rápido por nome do aluno");
        $manager->persist($mensagensAjudaFiltroRapidoNomeAluno);

        $filtroRapidoTelefonePreferencialAluno = new MensagensAjuda();
        $filtroRapidoTelefonePreferencialAluno->setModulo($this->getReference(ModuloFixtures::MODULO_ALUNO_REFERENCE));
        $filtroRapidoTelefonePreferencialAluno->setIdentificadorElemento("filtroRapido-aluno_telefone_preferencial");
        $filtroRapidoTelefonePreferencialAluno->setTitulo("Filtro:");
        $filtroRapidoTelefonePreferencialAluno->setMensagem("Filtro rápido pelo telefone do aluno");
        $manager->persist($filtroRapidoTelefonePreferencialAluno);

        $filtroRapidoSituacaoAluno = new MensagensAjuda();
        $filtroRapidoSituacaoAluno ->setModulo($this->getReference(ModuloFixtures::MODULO_ALUNO_REFERENCE));
        $filtroRapidoSituacaoAluno ->setIdentificadorElemento("filtroRapido-aluno_situacao_aluno");
        $filtroRapidoSituacaoAluno ->setTitulo("Filtro:");
        $filtroRapidoSituacaoAluno ->setMensagem("Filtro rápido pela situação do aluno");
        $manager->persist($filtroRapidoSituacaoAluno);

        $filtroAvancadoBuscaCpfAluno = new MensagensAjuda();
        $filtroAvancadoBuscaCpfAluno ->setModulo($this->getReference(ModuloFixtures::MODULO_ALUNO_REFERENCE));
        $filtroAvancadoBuscaCpfAluno ->setIdentificadorElemento("filtroAvancado-aluno_busca_cpf");
        $filtroAvancadoBuscaCpfAluno ->setTitulo("Filtro:");
        $filtroAvancadoBuscaCpfAluno ->setMensagem("Buscar pessoa pelo cpf");
        $manager->persist($filtroAvancadoBuscaCpfAluno);

        $filtroAvancadoSexoAluno = new MensagensAjuda();
        $filtroAvancadoSexoAluno ->setModulo($this->getReference(ModuloFixtures::MODULO_ALUNO_REFERENCE));
        $filtroAvancadoSexoAluno ->setIdentificadorElemento("filtroAvancado-aluno_sexo");
        $filtroAvancadoSexoAluno ->setTitulo("Filtro:");
        $filtroAvancadoSexoAluno ->setMensagem("Sexo que deseja filtrar");
        $manager->persist($filtroAvancadoSexoAluno);

        $filtroAvancadoEstadoCivilAluno = new MensagensAjuda();
        $filtroAvancadoEstadoCivilAluno ->setModulo($this->getReference(ModuloFixtures::MODULO_ALUNO_REFERENCE));
        $filtroAvancadoEstadoCivilAluno ->setIdentificadorElemento("filtroAvancado-aluno_estado_civil");
        $filtroAvancadoEstadoCivilAluno ->setTitulo("Filtro:");
        $filtroAvancadoEstadoCivilAluno ->setMensagem("Estado Civil que deseja filtrar");
        $manager->persist($filtroAvancadoEstadoCivilAluno);

        $filtroAvancadoResponsavelFinanceiroAluno = new MensagensAjuda();
        $filtroAvancadoResponsavelFinanceiroAluno ->setModulo($this->getReference(ModuloFixtures::MODULO_ALUNO_REFERENCE));
        $filtroAvancadoResponsavelFinanceiroAluno ->setIdentificadorElemento("filtroAvancado-aluno_responsavel_financeiro");
        $filtroAvancadoResponsavelFinanceiroAluno ->setTitulo("Filtro:");
        $filtroAvancadoResponsavelFinanceiroAluno ->setMensagem("Qual pessoa, responsável ficanceiro deseja filtrar");
        $manager->persist($filtroAvancadoResponsavelFinanceiroAluno);

        $filtroAvancadoResponsavelDidaticoAluno = new MensagensAjuda();
        $filtroAvancadoResponsavelDidaticoAluno ->setModulo($this->getReference(ModuloFixtures::MODULO_ALUNO_REFERENCE));
        $filtroAvancadoResponsavelDidaticoAluno ->setIdentificadorElemento("filtroAvancado-aluno_responsavel_didatico");
        $filtroAvancadoResponsavelDidaticoAluno ->setTitulo("Filtro:");
        $filtroAvancadoResponsavelDidaticoAluno ->setMensagem("Qual pessoa, responsável didático deseja filtrar");
        $manager->persist($filtroAvancadoResponsavelDidaticoAluno);

        $filtroAvancadoClassificacaoAluno = new MensagensAjuda();
        $filtroAvancadoClassificacaoAluno ->setModulo($this->getReference(ModuloFixtures::MODULO_ALUNO_REFERENCE));
        $filtroAvancadoClassificacaoAluno ->setIdentificadorElemento("filtroAvancado-aluno_classificacao");
        $filtroAvancadoClassificacaoAluno ->setTitulo("Filtro:");
        $filtroAvancadoClassificacaoAluno ->setMensagem("Qual a classificação deseja filtrar");
        $manager->persist($filtroAvancadoClassificacaoAluno);

        $filtroAvancadoCursoAluno = new MensagensAjuda();
        $filtroAvancadoCursoAluno ->setModulo($this->getReference(ModuloFixtures::MODULO_ALUNO_REFERENCE));
        $filtroAvancadoCursoAluno ->setIdentificadorElemento("filtroAvancado-aluno_curso");
        $filtroAvancadoCursoAluno ->setTitulo("Filtro:");
        $filtroAvancadoCursoAluno ->setMensagem("Qual curso de deseja filtrar");
        $manager->persist($filtroAvancadoCursoAluno);

        $filtroAvancadoEmancipadoAluno = new MensagensAjuda();
        $filtroAvancadoEmancipadoAluno ->setModulo($this->getReference(ModuloFixtures::MODULO_ALUNO_REFERENCE));
        $filtroAvancadoEmancipadoAluno ->setIdentificadorElemento("filtroAvancado-aluno_emancipado");
        $filtroAvancadoEmancipadoAluno ->setTitulo("Filtro:");
        $filtroAvancadoEmancipadoAluno ->setMensagem("Filtrar pela emancipação");
        $manager->persist($filtroAvancadoEmancipadoAluno);

        $filtroAvancadoDataCadastroAluno = new MensagensAjuda();
        $filtroAvancadoDataCadastroAluno ->setModulo($this->getReference(ModuloFixtures::MODULO_ALUNO_REFERENCE));
        $filtroAvancadoDataCadastroAluno ->setIdentificadorElemento("filtroAvancado-aluno_data_cadastro");
        $filtroAvancadoDataCadastroAluno ->setTitulo("Filtro:");
        $filtroAvancadoDataCadastroAluno ->setMensagem("Período de data de cadastro");
        $manager->persist($filtroAvancadoDataCadastroAluno);

        $filtroAvancadoDataNascimentoAluno = new MensagensAjuda();
        $filtroAvancadoDataNascimentoAluno ->setModulo($this->getReference(ModuloFixtures::MODULO_ALUNO_REFERENCE));
        $filtroAvancadoDataNascimentoAluno ->setIdentificadorElemento("filtroAvancado-aluno_data_nascimento");
        $filtroAvancadoDataNascimentoAluno ->setTitulo("Filtro:");
        $filtroAvancadoDataNascimentoAluno ->setMensagem("Período de data de nascimento");
        $manager->persist($filtroAvancadoDataNascimentoAluno);

        $mensagemConciliar = new MensagensAjuda();
        $mensagemConciliar->setModulo($this->getReference(ModuloFixtures::MODULO_CONTAS_RECEBER_REFERENCE));
        $mensagemConciliar->setIdentificadorElemento("form-quitar-conta-receber_conciliar");
        $mensagemConciliar->setTitulo("Conciliar:");
        $mensagemConciliar->setMensagem("Marcar este campo para efetuar a movimentação de conta.");
        $manager->persist($mensagemConciliar);

        $mensagemDigitosCartao = new MensagensAjuda();
        $mensagemDigitosCartao->setModulo($this->getReference(ModuloFixtures::MODULO_CONTAS_RECEBER_REFERENCE));
        $mensagemDigitosCartao->setIdentificadorElemento("form-quitar-conta-receber_digitos_cartao");
        $mensagemDigitosCartao->setTitulo("Dígitos do cartão:");
        $mensagemDigitosCartao->setMensagem("Preencher com os quatro últimos dígitos do cartão.");
        $manager->persist($mensagemDigitosCartao);

        $filtroRapidoCartaoSacado = new MensagensAjuda();
        $filtroRapidoCartaoSacado->setModulo($this->getReference(ModuloFixtures::MODULO_CARTOES_REFERENCE));
        $filtroRapidoCartaoSacado->setIdentificadorElemento("filtroRapido-cartao_sacado_filtro_rapido");
        $filtroRapidoCartaoSacado->setTitulo("Filtro Rapido");
        $filtroRapidoCartaoSacado->setMensagem("filtroRapido-cartao_sacado_filtro_rapido");
        $manager->persist($filtroRapidoCartaoSacado);

        $filtroRapidoCartaoNumeroLancamento = new MensagensAjuda();
        $filtroRapidoCartaoNumeroLancamento->setModulo($this->getReference(ModuloFixtures::MODULO_CARTOES_REFERENCE));
        $filtroRapidoCartaoNumeroLancamento->setIdentificadorElemento("filtroRapido-cartao_numero_lancamento");
        $filtroRapidoCartaoNumeroLancamento->setTitulo("Filtro Rapido");
        $filtroRapidoCartaoNumeroLancamento->setMensagem("filtroRapido-cartao_numero_lancamento");
        $manager->persist($filtroRapidoCartaoNumeroLancamento);

        $filtroRapidoCartaoSituacao = new MensagensAjuda();
        $filtroRapidoCartaoSituacao->setModulo($this->getReference(ModuloFixtures::MODULO_CARTOES_REFERENCE));
        $filtroRapidoCartaoSituacao->setIdentificadorElemento("filtroRapido-cartao_situacao");
        $filtroRapidoCartaoSituacao->setTitulo("Filtro Rapido");
        $filtroRapidoCartaoSituacao->setMensagem("filtroRapido-cartao_situacao");
        $manager->persist($filtroRapidoCartaoSituacao);

        $filtroRapidoCartaoBandeira = new MensagensAjuda();
        $filtroRapidoCartaoBandeira->setModulo($this->getReference(ModuloFixtures::MODULO_CARTOES_REFERENCE));
        $filtroRapidoCartaoBandeira->setIdentificadorElemento("filtroRapido-cartao_bandeira");
        $filtroRapidoCartaoBandeira->setTitulo("Filtro Rapido");
        $filtroRapidoCartaoBandeira->setMensagem("filtroRapido-cartao_bandeira");
        $manager->persist($filtroRapidoCartaoBandeira);

        $filtroRapidoCartaoTipoTransacao = new MensagensAjuda();
        $filtroRapidoCartaoTipoTransacao->setModulo($this->getReference(ModuloFixtures::MODULO_CARTOES_REFERENCE));
        $filtroRapidoCartaoTipoTransacao->setIdentificadorElemento("filtroRapido-cartao_tipo_transacao");
        $filtroRapidoCartaoTipoTransacao->setTitulo("Filtro Rapido");
        $filtroRapidoCartaoTipoTransacao->setMensagem("filtroRapido-cartao_tipo_transacao");
        $manager->persist($filtroRapidoCartaoTipoTransacao);

        $filtroRapidoCartaoIdentificador = new MensagensAjuda();
        $filtroRapidoCartaoIdentificador->setModulo($this->getReference(ModuloFixtures::MODULO_CARTOES_REFERENCE));
        $filtroRapidoCartaoIdentificador->setIdentificadorElemento("filtroRapido-cartao_tipo_identificador");
        $filtroRapidoCartaoIdentificador->setTitulo("Filtro Rapido");
        $filtroRapidoCartaoIdentificador->setMensagem("filtroRapido-cartao_tipo_identificador");
        $manager->persist($filtroRapidoCartaoIdentificador);

        $filtroAvancadoCartaoSacado = new MensagensAjuda();
        $filtroAvancadoCartaoSacado->setModulo($this->getReference(ModuloFixtures::MODULO_CARTOES_REFERENCE));
        $filtroAvancadoCartaoSacado->setIdentificadorElemento("filtroAvancado-cartao_sacado");
        $filtroAvancadoCartaoSacado->setTitulo("Filtro Avancado");
        $filtroAvancadoCartaoSacado->setMensagem("filtroAvancado-cartao_sacado");
        $manager->persist($filtroAvancadoCartaoSacado);

        $filtroAvancadoCartaoNumeroLancamento = new MensagensAjuda();
        $filtroAvancadoCartaoNumeroLancamento->setModulo($this->getReference(ModuloFixtures::MODULO_CARTOES_REFERENCE));
        $filtroAvancadoCartaoNumeroLancamento->setIdentificadorElemento("filtroAvancado-cartao_numero_lancamento");
        $filtroAvancadoCartaoNumeroLancamento->setTitulo("Filtro Avancado");
        $filtroAvancadoCartaoNumeroLancamento->setMensagem("filtroAvancado-cartao_numero_lancamento");
        $manager->persist($filtroAvancadoCartaoNumeroLancamento);

        $filtroAvancadoCartaoSituacao = new MensagensAjuda();
        $filtroAvancadoCartaoSituacao->setModulo($this->getReference(ModuloFixtures::MODULO_CARTOES_REFERENCE));
        $filtroAvancadoCartaoSituacao->setIdentificadorElemento("filtroAvancado-cartao_situacao");
        $filtroAvancadoCartaoSituacao->setTitulo("Filtro Avancado");
        $filtroAvancadoCartaoSituacao->setMensagem("filtroAvancado-cartao_situacao");
        $manager->persist($filtroAvancadoCartaoSituacao);

        $filtroAvancadoCartaoBandeira = new MensagensAjuda();
        $filtroAvancadoCartaoBandeira->setModulo($this->getReference(ModuloFixtures::MODULO_CARTOES_REFERENCE));
        $filtroAvancadoCartaoBandeira->setIdentificadorElemento("filtroAvancado-cartao_bandeira");
        $filtroAvancadoCartaoBandeira->setTitulo("Filtro Avancado");
        $filtroAvancadoCartaoBandeira->setMensagem("filtroAvancado-cartao_bandeira");
        $manager->persist($filtroAvancadoCartaoBandeira);

        $filtroAvancadoCartaoTipoTransacao = new MensagensAjuda();
        $filtroAvancadoCartaoTipoTransacao->setModulo($this->getReference(ModuloFixtures::MODULO_CARTOES_REFERENCE));
        $filtroAvancadoCartaoTipoTransacao->setIdentificadorElemento("filtroAvancado-cartao_tipo_transacao");
        $filtroAvancadoCartaoTipoTransacao->setTitulo("Filtro Avancado");
        $filtroAvancadoCartaoTipoTransacao->setMensagem("filtroAvancado-cartao_tipo_transacao");
        $manager->persist($filtroAvancadoCartaoTipoTransacao);

        $filtroAvancadoCartaoIdentificador = new MensagensAjuda();
        $filtroAvancadoCartaoIdentificador->setModulo($this->getReference(ModuloFixtures::MODULO_CARTOES_REFERENCE));
        $filtroAvancadoCartaoIdentificador->setIdentificadorElemento("filtroAvancado-cartao_identificador");
        $filtroAvancadoCartaoIdentificador->setTitulo("Filtro Avancado");
        $filtroAvancadoCartaoIdentificador->setMensagem("filtroAvancado-cartao_identificador");
        $manager->persist($filtroAvancadoCartaoIdentificador);

        $filtroRapidoChequeTipo = new MensagensAjuda();
        $filtroRapidoChequeTipo->setModulo($this->getReference(ModuloFixtures::MODULO_CHEQUE_REFERENCE));
        $filtroRapidoChequeTipo->setIdentificadorElemento("filtroRapido-cheque_tipo");
        $filtroRapidoChequeTipo->setTitulo("Filtro");
        $filtroRapidoChequeTipo->setMensagem("filtroRapido-cheque_tipo");
        $manager->persist($filtroRapidoChequeTipo);

        $filtroRapidoChequeEntrada = new MensagensAjuda();
        $filtroRapidoChequeEntrada->setModulo($this->getReference(ModuloFixtures::MODULO_CHEQUE_REFERENCE));
        $filtroRapidoChequeEntrada->setIdentificadorElemento("filtroRapido-cheque_entrada");
        $filtroRapidoChequeEntrada->setTitulo("Filtro");
        $filtroRapidoChequeEntrada->setMensagem("filtroRapido-cheque_entrada");
        $manager->persist($filtroRapidoChequeEntrada);

        $filtroRapidoChequeBomPara = new MensagensAjuda();
        $filtroRapidoChequeBomPara->setModulo($this->getReference(ModuloFixtures::MODULO_CHEQUE_REFERENCE));
        $filtroRapidoChequeBomPara->setIdentificadorElemento("filtroRapido-cheque_bom_para");
        $filtroRapidoChequeBomPara->setTitulo("Filtro");
        $filtroRapidoChequeBomPara->setMensagem("filtroRapido-cheque_bom_para");
        $manager->persist($filtroRapidoChequeBomPara);

        $filtroRapidoChequeSituacao = new MensagensAjuda();
        $filtroRapidoChequeSituacao->setModulo($this->getReference(ModuloFixtures::MODULO_CHEQUE_REFERENCE));
        $filtroRapidoChequeSituacao->setIdentificadorElemento("filtroRapido-cheque_situacao");
        $filtroRapidoChequeSituacao->setTitulo("Filtro");
        $filtroRapidoChequeSituacao->setMensagem("filtroRapido-cheque_situacao");
        $manager->persist($filtroRapidoChequeSituacao);

        $filtroAvancadoChequeTitular = new MensagensAjuda();
        $filtroAvancadoChequeTitular->setModulo($this->getReference(ModuloFixtures::MODULO_CHEQUE_REFERENCE));
        $filtroAvancadoChequeTitular->setIdentificadorElemento("filtroAvancado-cheque_titular");
        $filtroAvancadoChequeTitular->setTitulo("Filtro");
        $filtroAvancadoChequeTitular->setMensagem("filtroAvancado-cheque_titular");
        $manager->persist($filtroAvancadoChequeTitular);

        $filtroAvancadoChequeNumeroCheque = new MensagensAjuda();
        $filtroAvancadoChequeNumeroCheque->setModulo($this->getReference(ModuloFixtures::MODULO_CHEQUE_REFERENCE));
        $filtroAvancadoChequeNumeroCheque->setIdentificadorElemento("filtroAvancado-cheque_numero_cheque");
        $filtroAvancadoChequeNumeroCheque->setTitulo("Filtro");
        $filtroAvancadoChequeNumeroCheque->setMensagem("filtroAvancado-cheque_numero_cheque");
        $manager->persist($filtroAvancadoChequeNumeroCheque);

        $filtroAvancadoChequeTipoCheque = new MensagensAjuda();
        $filtroAvancadoChequeTipoCheque->setModulo($this->getReference(ModuloFixtures::MODULO_CHEQUE_REFERENCE));
        $filtroAvancadoChequeTipoCheque->setIdentificadorElemento("filtroAvancado-cheque_tipo_cheque");
        $filtroAvancadoChequeTipoCheque->setTitulo("Filtro");
        $filtroAvancadoChequeTipoCheque->setMensagem("filtroAvancado-cheque_tipo_cheque");
        $manager->persist($filtroAvancadoChequeTipoCheque);

        $filtroAvancadoChequeBanco = new MensagensAjuda();
        $filtroAvancadoChequeBanco->setModulo($this->getReference(ModuloFixtures::MODULO_CHEQUE_REFERENCE));
        $filtroAvancadoChequeBanco->setIdentificadorElemento("filtroAvancado-cheque_banco");
        $filtroAvancadoChequeBanco->setTitulo("Filtro");
        $filtroAvancadoChequeBanco->setMensagem("filtroAvancado-cheque_banco");
        $manager->persist($filtroAvancadoChequeBanco);

        $filtroAvancadoChequeConta = new MensagensAjuda();
        $filtroAvancadoChequeConta->setModulo($this->getReference(ModuloFixtures::MODULO_CHEQUE_REFERENCE));
        $filtroAvancadoChequeConta->setIdentificadorElemento("filtroAvancado-cheque_conta");
        $filtroAvancadoChequeConta->setTitulo("Filtro");
        $filtroAvancadoChequeConta->setMensagem("filtroAvancado-cheque_conta");
        $manager->persist($filtroAvancadoChequeConta);

        $filtroAvancadoChequeValor = new MensagensAjuda();
        $filtroAvancadoChequeValor->setModulo($this->getReference(ModuloFixtures::MODULO_CHEQUE_REFERENCE));
        $filtroAvancadoChequeValor->setIdentificadorElemento("filtroAvancado-cheque_valor");
        $filtroAvancadoChequeValor->setTitulo("Filtro");
        $filtroAvancadoChequeValor->setMensagem("filtroAvancado-cheque_valor");
        $manager->persist($filtroAvancadoChequeValor);

        $filtroAvancadoChequeEntrada = new MensagensAjuda();
        $filtroAvancadoChequeEntrada->setModulo($this->getReference(ModuloFixtures::MODULO_CHEQUE_REFERENCE));
        $filtroAvancadoChequeEntrada->setIdentificadorElemento("filtroAvancado-cheque_entrada");
        $filtroAvancadoChequeEntrada->setTitulo("Filtro");
        $filtroAvancadoChequeEntrada->setMensagem("filtroAvancado-cheque_entrada");
        $manager->persist($filtroAvancadoChequeEntrada);

        $filtroAvancadoChequeBomPara = new MensagensAjuda();
        $filtroAvancadoChequeBomPara->setModulo($this->getReference(ModuloFixtures::MODULO_CHEQUE_REFERENCE));
        $filtroAvancadoChequeBomPara->setIdentificadorElemento("filtroAvancado-cheque_bom_para");
        $filtroAvancadoChequeBomPara->setTitulo("Filtro");
        $filtroAvancadoChequeBomPara->setMensagem("filtroAvancado-cheque_bom_para");
        $manager->persist($filtroAvancadoChequeBomPara);

        $filtroAvancadoChequeSituacao = new MensagensAjuda();
        $filtroAvancadoChequeSituacao->setModulo($this->getReference(ModuloFixtures::MODULO_CHEQUE_REFERENCE));
        $filtroAvancadoChequeSituacao->setIdentificadorElemento("filtroAvancado-cheque_situacao");
        $filtroAvancadoChequeSituacao->setTitulo("Filtro");
        $filtroAvancadoChequeSituacao->setMensagem("filtroAvancado-cheque_situacao");
        $manager->persist($filtroAvancadoChequeSituacao);

        $modalDevolverChequeMotivo = new MensagensAjuda();
        $modalDevolverChequeMotivo->setModulo($this->getReference(ModuloFixtures::MODULO_CHEQUE_REFERENCE));
        $modalDevolverChequeMotivo->setIdentificadorElemento("modal-cheque_devolver_motivo");
        $modalDevolverChequeMotivo->setTitulo("Motivo");
        $modalDevolverChequeMotivo->setMensagem("modal-cheque_devolver_motivo");
        $manager->persist($modalDevolverChequeMotivo);

        $filtroRapidoContasPagarPessoa = new MensagensAjuda();
        $filtroRapidoContasPagarPessoa->setModulo($this->getReference(ModuloFixtures::MODULO_CONTAS_PAGAR_REFERENCE));
        $filtroRapidoContasPagarPessoa->setIdentificadorElemento("filtroRapido-contas-pagar_pessoa");
        $filtroRapidoContasPagarPessoa->setTitulo("Filtro");
        $filtroRapidoContasPagarPessoa->setMensagem("filtroRapido-contas-pagar_pessoa");
        $manager->persist($filtroRapidoContasPagarPessoa);

        $filtroRapidoContasPagarMes = new MensagensAjuda();
        $filtroRapidoContasPagarMes->setModulo($this->getReference(ModuloFixtures::MODULO_CONTAS_PAGAR_REFERENCE));
        $filtroRapidoContasPagarMes->setIdentificadorElemento("filtroRapido-contas-pagar_mes");
        $filtroRapidoContasPagarMes->setTitulo("Filtro");
        $filtroRapidoContasPagarMes->setMensagem("filtroRapido-contas-pagar_mes");
        $manager->persist($filtroRapidoContasPagarMes);

        $filtroRapidoContasPagarAno = new MensagensAjuda();
        $filtroRapidoContasPagarAno->setModulo($this->getReference(ModuloFixtures::MODULO_CONTAS_PAGAR_REFERENCE));
        $filtroRapidoContasPagarAno->setIdentificadorElemento("filtroRapido-contas-pagar_ano");
        $filtroRapidoContasPagarAno->setTitulo("Filtro");
        $filtroRapidoContasPagarAno->setMensagem("filtroRapido-contas-pagar_ano");
        $manager->persist($filtroRapidoContasPagarAno);

        $filtroRapidoContasPagarSituacao = new MensagensAjuda();
        $filtroRapidoContasPagarSituacao->setModulo($this->getReference(ModuloFixtures::MODULO_CONTAS_PAGAR_REFERENCE));
        $filtroRapidoContasPagarSituacao->setIdentificadorElemento("filtroRapido-contas-pagar_situacao");
        $filtroRapidoContasPagarSituacao->setTitulo("Filtro");
        $filtroRapidoContasPagarSituacao->setMensagem("filtroRapido-contas-pagar_situacao");
        $manager->persist($filtroRapidoContasPagarSituacao);

        $filtroAvancadoContasPagarPessoa = new MensagensAjuda();
        $filtroAvancadoContasPagarPessoa->setModulo($this->getReference(ModuloFixtures::MODULO_CONTAS_PAGAR_REFERENCE));
        $filtroAvancadoContasPagarPessoa->setIdentificadorElemento("filtroAvancado-contas-pagar_pessoa");
        $filtroAvancadoContasPagarPessoa->setTitulo("Filtro");
        $filtroAvancadoContasPagarPessoa->setMensagem("filtroAvancado-contas-pagar_pessoa");
        $manager->persist($filtroAvancadoContasPagarPessoa);

        $filtroAvancadoContasPagarFormaCobranca = new MensagensAjuda();
        $filtroAvancadoContasPagarFormaCobranca->setModulo($this->getReference(ModuloFixtures::MODULO_CONTAS_PAGAR_REFERENCE));
        $filtroAvancadoContasPagarFormaCobranca->setIdentificadorElemento("filtroAvancado-contas-pagar_forma_cobranca");
        $filtroAvancadoContasPagarFormaCobranca->setTitulo("Filtro");
        $filtroAvancadoContasPagarFormaCobranca->setMensagem("filtroAvancado-contas-pagar_forma_cobranca");
        $manager->persist($filtroAvancadoContasPagarFormaCobranca);

        $filtroAvancadoContasPagarFormaCobranca = new MensagensAjuda();
        $filtroAvancadoContasPagarFormaCobranca->setModulo($this->getReference(ModuloFixtures::MODULO_CONTAS_PAGAR_REFERENCE));
        $filtroAvancadoContasPagarFormaCobranca->setIdentificadorElemento("filtroAvancado-contas-pagar_forma_situacao");
        $filtroAvancadoContasPagarFormaCobranca->setTitulo("Filtro");
        $filtroAvancadoContasPagarFormaCobranca->setMensagem("filtroAvancado-contas-pagar_forma_situacao");
        $manager->persist($filtroAvancadoContasPagarFormaCobranca);

        $filtroAvancadoContasPagarPlanoConta = new MensagensAjuda();
        $filtroAvancadoContasPagarPlanoConta->setModulo($this->getReference(ModuloFixtures::MODULO_CONTAS_PAGAR_REFERENCE));
        $filtroAvancadoContasPagarPlanoConta->setIdentificadorElemento("filtroAvancado-contas-pagar_plano_contas");
        $filtroAvancadoContasPagarPlanoConta->setTitulo("Filtro");
        $filtroAvancadoContasPagarPlanoConta->setMensagem("filtroAvancado-contas-pagar_plano_contas");
        $manager->persist($filtroAvancadoContasPagarPlanoConta);

        $filtroAvancadoContasPagarVencimento = new MensagensAjuda();
        $filtroAvancadoContasPagarVencimento->setModulo($this->getReference(ModuloFixtures::MODULO_CONTAS_PAGAR_REFERENCE));
        $filtroAvancadoContasPagarVencimento->setIdentificadorElemento("filtroAvancado-contas-pagar_data_inicial_vencimento");
        $filtroAvancadoContasPagarVencimento->setTitulo("Filtro");
        $filtroAvancadoContasPagarVencimento->setMensagem("filtroAvancado-contas-pagar_data_inicial_vencimento");
        $manager->persist($filtroAvancadoContasPagarVencimento);

        $filtroAvancadoContasPagarPagamento = new MensagensAjuda();
        $filtroAvancadoContasPagarPagamento->setModulo($this->getReference(ModuloFixtures::MODULO_CONTAS_PAGAR_REFERENCE));
        $filtroAvancadoContasPagarPagamento->setIdentificadorElemento("filtroAvancado-contas-pagar_pagamento");
        $filtroAvancadoContasPagarPagamento->setTitulo("Filtro");
        $filtroAvancadoContasPagarPagamento->setMensagem("filtroAvancado-contas-pagar_pagamento");
        $manager->persist($filtroAvancadoContasPagarPagamento);

        $filtroAvancadoContasPagarValor = new MensagensAjuda();
        $filtroAvancadoContasPagarValor->setModulo($this->getReference(ModuloFixtures::MODULO_CONTAS_PAGAR_REFERENCE));
        $filtroAvancadoContasPagarValor->setIdentificadorElemento("filtroAvancado-contas-pagar_valor");
        $filtroAvancadoContasPagarValor->setTitulo("Filtro");
        $filtroAvancadoContasPagarValor->setMensagem("filtroAvancado-contas-pagar_valor");
        $manager->persist($filtroAvancadoContasPagarValor);

        $formContaPagarDestino = new MensagensAjuda();
        $formContaPagarDestino->setModulo($this->getReference(ModuloFixtures::MODULO_CONTAS_PAGAR_REFERENCE));
        $formContaPagarDestino->setIdentificadorElemento("form-contas-pagar_destino");
        $formContaPagarDestino->setTitulo("Titulo");
        $formContaPagarDestino->setMensagem("form-contas-pagar_destino");
        $manager->persist($formContaPagarDestino);

        $formContaPagarFormaCobranca = new MensagensAjuda();
        $formContaPagarFormaCobranca->setModulo($this->getReference(ModuloFixtures::MODULO_CONTAS_PAGAR_REFERENCE));
        $formContaPagarFormaCobranca->setIdentificadorElemento("form-conta-pagar_forma_cobranca");
        $formContaPagarFormaCobranca->setTitulo("Titulo");
        $formContaPagarFormaCobranca->setMensagem("form-conta-pagar_forma_cobranca");
        $manager->persist($formContaPagarFormaCobranca);

        $formContaPagarConta = new MensagensAjuda();
        $formContaPagarConta->setModulo($this->getReference(ModuloFixtures::MODULO_CONTAS_PAGAR_REFERENCE));
        $formContaPagarConta->setIdentificadorElemento("form-conta-pagar_conta");
        $formContaPagarConta->setTitulo("Titulo");
        $formContaPagarConta->setMensagem("form-conta-pagar_conta");
        $manager->persist($formContaPagarConta);

        $formContaPagarDataVencimento = new MensagensAjuda();
        $formContaPagarDataVencimento->setModulo($this->getReference(ModuloFixtures::MODULO_CONTAS_PAGAR_REFERENCE));
        $formContaPagarDataVencimento->setIdentificadorElemento("form-conta-pagar_data_vencimento");
        $formContaPagarDataVencimento->setTitulo("Titulo");
        $formContaPagarDataVencimento->setMensagem("form-conta-pagar_data_vencimento");
        $manager->persist($formContaPagarDataVencimento);

        $formContaPagarValorParcela = new MensagensAjuda();
        $formContaPagarValorParcela->setModulo($this->getReference(ModuloFixtures::MODULO_CONTAS_PAGAR_REFERENCE));
        $formContaPagarValorParcela->setIdentificadorElemento("form-conta-pagar_valor_parcela");
        $formContaPagarValorParcela->setTitulo("Titulo");
        $formContaPagarValorParcela->setMensagem("form-conta-pagar_valor_parcela");
        $manager->persist($formContaPagarValorParcela);

        $formContaPagarNumeroParcelas = new MensagensAjuda();
        $formContaPagarNumeroParcelas->setModulo($this->getReference(ModuloFixtures::MODULO_CONTAS_PAGAR_REFERENCE));
        $formContaPagarNumeroParcelas->setIdentificadorElemento("form-conta-pagar_numero_parcelas");
        $formContaPagarNumeroParcelas->setTitulo("Titulo");
        $formContaPagarNumeroParcelas->setMensagem("form-conta-pagar_numero_parcelas");
        $manager->persist($formContaPagarNumeroParcelas);

        $formContaPagarValorTotal = new MensagensAjuda();
        $formContaPagarValorTotal->setModulo($this->getReference(ModuloFixtures::MODULO_CONTAS_PAGAR_REFERENCE));
        $formContaPagarValorTotal->setIdentificadorElemento("form-conta-pagar_valor_total");
        $formContaPagarValorTotal->setTitulo("Titulo");
        $formContaPagarValorTotal->setMensagem("form-conta-pagar_valor_total");
        $manager->persist($formContaPagarValorTotal);

        $formContaPagarCategoria = new MensagensAjuda();
        $formContaPagarCategoria->setModulo($this->getReference(ModuloFixtures::MODULO_CONTAS_PAGAR_REFERENCE));
        $formContaPagarCategoria->setIdentificadorElemento("form-conta-pagar_categoria");
        $formContaPagarCategoria->setTitulo("Titulo");
        $formContaPagarCategoria->setMensagem("form-conta-pagar_categoria");
        $manager->persist($formContaPagarCategoria);

        $formContaPagarValor = new MensagensAjuda();
        $formContaPagarValor->setModulo($this->getReference(ModuloFixtures::MODULO_CONTAS_PAGAR_REFERENCE));
        $formContaPagarValor->setIdentificadorElemento("form-conta-pagar_valor");
        $formContaPagarValor->setTitulo("Titulo");
        $formContaPagarValor->setMensagem("form-conta-pagar_valor");
        $manager->persist($formContaPagarValor);

        $formContaPagarComplemento = new MensagensAjuda();
        $formContaPagarComplemento->setModulo($this->getReference(ModuloFixtures::MODULO_CONTAS_PAGAR_REFERENCE));
        $formContaPagarComplemento->setIdentificadorElemento("form-conta-pagar_complemento");
        $formContaPagarComplemento->setTitulo("Titulo");
        $formContaPagarComplemento->setMensagem("form-conta-pagar_complemento");
        $manager->persist($formContaPagarComplemento);

        $formContaPagarRemoverPlano = new MensagensAjuda();
        $formContaPagarRemoverPlano->setModulo($this->getReference(ModuloFixtures::MODULO_CONTAS_PAGAR_REFERENCE));
        $formContaPagarRemoverPlano->setIdentificadorElemento("form-conta-pagar_remove_plano");
        $formContaPagarRemoverPlano->setTitulo("Titulo");
        $formContaPagarRemoverPlano->setMensagem("form-conta-pagar_remove_plano");
        $manager->persist($formContaPagarRemoverPlano);

        $formContaPagarVencimento = new MensagensAjuda();
        $formContaPagarVencimento->setModulo($this->getReference(ModuloFixtures::MODULO_CONTAS_PAGAR_REFERENCE));
        $formContaPagarVencimento->setIdentificadorElemento("form-conta-pagar_vencimento");
        $formContaPagarVencimento->setTitulo("Titulo");
        $formContaPagarVencimento->setMensagem("form-conta-pagar_vencimento");
        $manager->persist($formContaPagarVencimento);

        $formContaPagarValorOriginal = new MensagensAjuda();
        $formContaPagarValorOriginal->setModulo($this->getReference(ModuloFixtures::MODULO_CONTAS_PAGAR_REFERENCE));
        $formContaPagarValorOriginal->setIdentificadorElemento("form-conta-pagar_valor_original");
        $formContaPagarValorOriginal->setTitulo("Titulo");
        $formContaPagarValorOriginal->setMensagem("form-conta-pagar_valor_original");
        $manager->persist($formContaPagarValorOriginal);

        $formContaPagarNarrativa = new MensagensAjuda();
        $formContaPagarNarrativa->setModulo($this->getReference(ModuloFixtures::MODULO_CONTAS_PAGAR_REFERENCE));
        $formContaPagarNarrativa->setIdentificadorElemento("form-conta-pagar_narrativa");
        $formContaPagarNarrativa->setTitulo("Titulo");
        $formContaPagarNarrativa->setMensagem("form-conta-pagar_narrativa");
        $manager->persist($formContaPagarNarrativa);

        $formContaPagarObservacao = new MensagensAjuda();
        $formContaPagarObservacao->setModulo($this->getReference(ModuloFixtures::MODULO_CONTAS_PAGAR_REFERENCE));
        $formContaPagarObservacao->setIdentificadorElemento("form-conta-pagar_observacao");
        $formContaPagarObservacao->setTitulo("Titulo");
        $formContaPagarObservacao->setMensagem("form-conta-pagar_observacao");
        $manager->persist($formContaPagarObservacao);

        $filtroRapidoContasContasReceberSacado = new MensagensAjuda();
        $filtroRapidoContasContasReceberSacado->setModulo($this->getReference(ModuloFixtures::MODULO_CONTAS_RECEBER_REFERENCE));
        $filtroRapidoContasContasReceberSacado->setIdentificadorElemento("filtroRapido-contas-receber_sacado");
        $filtroRapidoContasContasReceberSacado->setTitulo("Filtro");
        $filtroRapidoContasContasReceberSacado->setMensagem("filtroRapido-contas-receber_sacado");
        $manager->persist($filtroRapidoContasContasReceberSacado);

        $filtroRapidoContasContasReceberMes = new MensagensAjuda();
        $filtroRapidoContasContasReceberMes->setModulo($this->getReference(ModuloFixtures::MODULO_CONTAS_RECEBER_REFERENCE));
        $filtroRapidoContasContasReceberMes->setIdentificadorElemento("filtroRapido-contas-receber_mes");
        $filtroRapidoContasContasReceberMes->setTitulo("Filtro");
        $filtroRapidoContasContasReceberMes->setMensagem("filtroRapido-contas-receber_mes");
        $manager->persist($filtroRapidoContasContasReceberMes);

        $filtroRapidoContasContasReceberAno = new MensagensAjuda();
        $filtroRapidoContasContasReceberAno->setModulo($this->getReference(ModuloFixtures::MODULO_CONTAS_RECEBER_REFERENCE));
        $filtroRapidoContasContasReceberAno->setIdentificadorElemento("filtroRapido-contas-receber_ano");
        $filtroRapidoContasContasReceberAno->setTitulo("Filtro");
        $filtroRapidoContasContasReceberAno->setMensagem("filtroRapido-contas-receber_ano");
        $manager->persist($filtroRapidoContasContasReceberAno);

        $filtroRapidoContasContasReceberSituacao = new MensagensAjuda();
        $filtroRapidoContasContasReceberSituacao->setModulo($this->getReference(ModuloFixtures::MODULO_CONTAS_RECEBER_REFERENCE));
        $filtroRapidoContasContasReceberSituacao->setIdentificadorElemento("filtroRapido-contas-receber_situacao");
        $filtroRapidoContasContasReceberSituacao->setTitulo("Filtro");
        $filtroRapidoContasContasReceberSituacao->setMensagem("filtroRapido-contas-receber_situacao");
        $manager->persist($filtroRapidoContasContasReceberSituacao);

        $filtroAvancadoContasContasReceberVencimento = new MensagensAjuda();
        $filtroAvancadoContasContasReceberVencimento->setModulo($this->getReference(ModuloFixtures::MODULO_CONTAS_RECEBER_REFERENCE));
        $filtroAvancadoContasContasReceberVencimento->setIdentificadorElemento("filtroAvancado-contas-receber_vencimento");
        $filtroAvancadoContasContasReceberVencimento->setTitulo("Filtro");
        $filtroAvancadoContasContasReceberVencimento->setMensagem("filtroAvancado-contas-receber_vencimento");
        $manager->persist($filtroAvancadoContasContasReceberVencimento);

        $filtroAvancadoContasContasReceberSaldo = new MensagensAjuda();
        $filtroAvancadoContasContasReceberSaldo->setModulo($this->getReference(ModuloFixtures::MODULO_CONTAS_RECEBER_REFERENCE));
        $filtroAvancadoContasContasReceberSaldo->setIdentificadorElemento("filtroAvancado-contas-receber_saldo");
        $filtroAvancadoContasContasReceberSaldo->setTitulo("Filtro");
        $filtroAvancadoContasContasReceberSaldo->setMensagem("filtroAvancado-contas-receber_saldo");
        $manager->persist($filtroAvancadoContasContasReceberSaldo);

        $filtroAvancadoContasContasReceberSituacao = new MensagensAjuda();
        $filtroAvancadoContasContasReceberSituacao->setModulo($this->getReference(ModuloFixtures::MODULO_CONTAS_RECEBER_REFERENCE));
        $filtroAvancadoContasContasReceberSituacao->setIdentificadorElemento("filtroAvancado-contas-receber_situacao");
        $filtroAvancadoContasContasReceberSituacao->setTitulo("Filtro");
        $filtroAvancadoContasContasReceberSituacao->setMensagem("filtroAvancado-contas-receber_situacao");
        $manager->persist($filtroAvancadoContasContasReceberSituacao);

        $parametroReativacaoMatriculaParametrosFranqueadora = new MensagensAjuda();
        $parametroReativacaoMatriculaParametrosFranqueadora->setModulo($this->getReference(ModuloFixtures::MODULO_PARAMETROS_FRANQUEADORA));
        $parametroReativacaoMatriculaParametrosFranqueadora->setIdentificadorElemento("form-parametros-franqueadora_dias_reativacao_interessado");
        $parametroReativacaoMatriculaParametrosFranqueadora->setTitulo("OBS:");
        $parametroReativacaoMatriculaParametrosFranqueadora->setMensagem("Este parâmetro irá comparar se a situação do interessado está marcado como 'Matricula Perdida' após N dias.");
        $manager->persist($parametroReativacaoMatriculaParametrosFranqueadora);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ModuloFixtures::class,
        ];
    }


}
