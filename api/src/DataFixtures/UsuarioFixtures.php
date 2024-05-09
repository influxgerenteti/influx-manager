<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\Usuario;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Principal\ModuloPapelAcao;
use App\Entity\Principal\ModuloUsuarioAcao;

class UsuarioFixtures extends Fixture implements DependentFixtureInterface
{

    const TOKEN_PADRAO = "18c091e9a8b239ce5177caca941dc17c7628eca6589e200d929c12bcbd0c4347dcc44b02f9ac8b69ca0e3dbb356d078ae5132339798cf9c594b82bf9";
    public const USUARIO_REFERENCE          = "usuario";
    public const USUARIO_ERICK_REFERENCE    = "usuarioErick";
    public const USUARIO_MARCOS_REFERENCE   = "usuarioMarcos";
    public const USUARIO_LUZIA_REFERENCE    = "usuarioLuzia";
    public const USUARIO_RAIMUNDA_REFERENCE = "usuarioRaimunda";

    public function load(ObjectManager $manager)
    {
        $moduloRepository = $manager->getRepository(\App\Entity\Principal\Modulo::class);
        $todosModulos     = $moduloRepository->findAll();

        $usuario = new Usuario();
        $usuario->setNome("Administrador");
        $usuario->setEmail("suporte@gatilabs.com.br");
        $usuario->setSenha(password_hash("admingati.2018", PASSWORD_DEFAULT));
        $usuario->setSituacao("A");
        $usuario->setForcaTrocaSenha(false);
        $usuario->setCpf("11122233344");
        $usuario->setFranqueadaPadrao($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $usuario->addFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $usuario->addFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADORA_REFERENCE));
        $usuario->addPapel($this->getReference(PapelFixtures::PAPEL_ADMINISTRADOR_FRANQUEADORA));
        $usuario->addPapel($this->getReference(PapelFixtures::PAPEL_ADMINISTRADOR));
        $manager->persist($usuario);
        $this->addReference(self::USUARIO_REFERENCE, $usuario);

        $usuarioErick = new Usuario();
        $usuarioErick->setNome("Erick Thales Ramos");
        $usuarioErick->setEmail("erick@influx.com.br");
        $usuarioErick->setSenha(password_hash("admingati.2018", PASSWORD_DEFAULT));
        $usuarioErick->setSituacao("A");
        $usuarioErick->setForcaTrocaSenha(false);
        $usuarioErick->setCpf("52640405837");
        $usuarioErick->addFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $usuarioErick->setFranqueadaPadrao($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $usuarioErick->addPapel($this->getReference(PapelFixtures::PAPEL_GERENTE));
        $manager->persist($usuarioErick);
        $this->addReference(self::USUARIO_ERICK_REFERENCE, $usuarioErick);

        $usuarioMarcos = new Usuario();
        $usuarioMarcos->setNome("Marcos Renan Julio Jesus");
        $usuarioMarcos->setEmail("marcos.jesus@influx.com.br");
        $usuarioMarcos->setSenha(password_hash("admingati.2018", PASSWORD_DEFAULT));
        $usuarioMarcos->setSituacao("A");
        $usuarioMarcos->setForcaTrocaSenha(false);
        $usuarioMarcos->setCpf("47818480790");
        $usuarioMarcos->addFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $usuarioMarcos->setFranqueadaPadrao($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $usuarioMarcos->addPapel($this->getReference(PapelFixtures::PAPEL_COORDENADOR));
        $manager->persist($usuarioMarcos);
        $this->addReference(self::USUARIO_MARCOS_REFERENCE, $usuarioMarcos);

        $usuarioLuzia = new Usuario();
        $usuarioLuzia->setNome("Luzia Sônia Nascimento");
        $usuarioLuzia->setEmail("luzia@influx.com.br");
        $usuarioLuzia->setSenha(password_hash("admingati.2018", PASSWORD_DEFAULT));
        $usuarioLuzia->setSituacao("A");
        $usuarioLuzia->setForcaTrocaSenha(false);
        $usuarioLuzia->setCpf("92409833179");
        $usuarioLuzia->addFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $usuarioLuzia->setFranqueadaPadrao($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $usuarioLuzia->addPapel($this->getReference(PapelFixtures::PAPEL_CONSULTOR));
        $manager->persist($usuarioLuzia);
        $this->addReference(self::USUARIO_LUZIA_REFERENCE, $usuarioLuzia);

        $usuarioRaimunda = new Usuario();
        $usuarioRaimunda->setNome("Raimunda Rafaela Rezende");
        $usuarioRaimunda->setEmail("raimunda@influx.com.br");
        $usuarioRaimunda->setSenha(password_hash("admingati.2018", PASSWORD_DEFAULT));
        $usuarioRaimunda->setSituacao("A");
        $usuarioRaimunda->setForcaTrocaSenha(false);
        $usuarioRaimunda->setCpf("15109134596");
        $usuarioRaimunda->addFranqueada($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $usuarioRaimunda->setFranqueadaPadrao($this->getReference(FranqueadaFixtures::FRANQUEADA_REFERENCE));
        $usuarioRaimunda->addPapel($this->getReference(PapelFixtures::PAPEL_CONSULTOR));
        $manager->persist($usuarioRaimunda);
        $this->addReference(self::USUARIO_RAIMUNDA_REFERENCE, $usuarioRaimunda);

        $manager->flush();

        // $config = new \App\Entity\Principal\UsuarioAcesso();
        // $config->setUsuario($usuario);
        // $config->setTokenAcesso(self::TOKEN_PADRAO);
        // $manager->persist($config);
        $moduloORM = null;

        // Usuario e Acao
        foreach ($todosModulos as $moduloORM) {
            $moduloUsuarioAcaoCriar = new ModuloUsuarioAcao();
            $moduloUsuarioAcaoCriar->setModulo($moduloORM);
            $moduloUsuarioAcaoCriar->setUsuario($usuario);
            $moduloUsuarioAcaoCriar->setAcaoSistema($this->getReference(AcaoSistemaFixtures::ACAO_CRIAR));
            $manager->persist($moduloUsuarioAcaoCriar);

            $moduloUsuarioAcaoEditar = new ModuloUsuarioAcao();
            $moduloUsuarioAcaoEditar->setModulo($moduloORM);
            $moduloUsuarioAcaoEditar->setUsuario($usuario);
            $moduloUsuarioAcaoEditar->setAcaoSistema($this->getReference(AcaoSistemaFixtures::ACAO_EDITAR));
            $manager->persist($moduloUsuarioAcaoEditar);

            $moduloUsuarioAcaoExcluir = new ModuloUsuarioAcao();
            $moduloUsuarioAcaoExcluir->setModulo($moduloORM);
            $moduloUsuarioAcaoExcluir->setUsuario($usuario);
            $moduloUsuarioAcaoExcluir->setAcaoSistema($this->getReference(AcaoSistemaFixtures::ACAO_EXCLUIR));
            $manager->persist($moduloUsuarioAcaoExcluir);

            $moduloUsuarioAcaoListar = new ModuloUsuarioAcao();
            $moduloUsuarioAcaoListar->setModulo($moduloORM);
            $moduloUsuarioAcaoListar->setUsuario($usuario);
            $moduloUsuarioAcaoListar->setAcaoSistema($this->getReference(AcaoSistemaFixtures::ACAO_LISTAR));
            $manager->persist($moduloUsuarioAcaoListar);

            $moduloUsuarioAcaoTransferenciaTurma = new ModuloUsuarioAcao();
            $moduloUsuarioAcaoTransferenciaTurma->setModulo($moduloORM);
            $moduloUsuarioAcaoTransferenciaTurma->setUsuario($usuario);
            $moduloUsuarioAcaoTransferenciaTurma->setAcaoSistema($this->getReference(AcaoSistemaFixtures::ACAO_TRANSFERENCIA_TURMA));
            $manager->persist($moduloUsuarioAcaoTransferenciaTurma);

            $moduloUsuarioAcaoAcessar = new ModuloUsuarioAcao();
            $moduloUsuarioAcaoAcessar->setModulo($moduloORM);
            $moduloUsuarioAcaoAcessar->setUsuario($usuario);
            $moduloUsuarioAcaoAcessar->setAcaoSistema($this->getReference(AcaoSistemaFixtures::ACAO_ACESSAR));
            $manager->persist($moduloUsuarioAcaoAcessar);
        }//end foreach

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            FranqueadaFixtures::class,
            AcaoSistemaFixtures::class,
            PapelFixtures::class,
            ModuloFixtures::class,
        ];
    }


}
