# Deploy Influx Manager.


## API

### Ambiente BETA

1 - Realizar o commit na branch criada a partir da develop.

2 - Fazer o merge na develop [feature -> develop] e subir.

3 - Realizar os testes.

4 - Fazer o merge na beta [develop -> beta] e subir.

```
sudo su
cd /var/www/beta/
git pull
composer install
```

### Ambiente PROD

5 - Fazer o merge na master [develop -> master] e subir

```
sudo su
cd /var/www/app/
git pull
composer install
```

## FRONT

### Ambiente BETA

1 - Realizar o commit na branch criada a partir da develop.

2 - Fazer o merge na develop [feature -> develop] e subir.

3 - Alterar o arquivo environment para usar o env BETA.

4 - Realizar os testes.

5 - Fazer o merge na beta [develop -> beta] e subir.

6 - Buildar o projeto.

```
bash build.sh -e BETA
```

7 - Acessar o bucket beta.manager.influx.com.br na AWS S3.

8 - Subir os arquivos da pasta dist com permissão publica.

Permissions->Grant public-read access->I understand the risk of granting public-read access to the specified objects.







### Ambiente PROD

3 - Alterar o arquivo environment para usar o env PROD.

4 - Realizar os testes.

5 - Fazer o merge na beta [beta -> master] e subir.

6 - Buildar o projeto.

```
bash build.sh -e PROD
```

7 - Acessar o bucket manager.influx.com.br na AWS S3.

8 - Subir os arquivos da pasta dist com permissão publica.

Permissions->Grant public-read access->I understand the risk of granting public-read access to the specified objects.


### Chave de acesso ssh

```
ssh -i "influx-manager.pem" ubuntu@ec2-54-207-154-157.sa-east-1.compute.amazonaws.com
```

influx-manager.pem   precisa ter permissão 400 no arquivo

```
-----BEGIN RSA PRIVATE KEY-----
MIIEpgIBAAKCAQEA5jveAn9UJsqgT1HUFDUx0RQMK+rX62zPuFwFz6lDaoqK01dO
kl90URteychzBcEPEAxAxYaf4et6iuYXvIcRQezE3rXIVlKo0vXGjHO8TKVrdNMh
GXz10ivO2n2+ap3BdQTtlunRzgTZJiy7rJ9Tf4q6dxbLQD8tSf3DHCAUqhIWOcTk
l88IpwRaoFG+5AufDmQ0R16Iv899jPCG++b8/FliEXHtIRM/QnZxlP7375M9S+3M
N/6jDsxqg6akC8XHtn1MbQ4GYcXXz05P8uYdnjIJfMEXuptXpGaEi0bXEgo6Qpra
2FpoKMNSEWzrLrxdIMupfmY2GK+vWmLl8vT1FwIDAQABAoIBAQC+z4EY2sQ4fhhM
hMnakZee6xmHUFPw5rZk2cdrKqIX64hQzXvt7bRt/9wYKcvCShWcvDr2ObPKPinK
chXcBqjz1TH4TfoiKNGIPW93OfgmIbakFNLK1/i3Xd1K0Yn9vI7318S9HT3nm7cI
X2yPDWDbRHNzfW2n6nQzbV/FsITPySke6k6jfgxOMxMnaVwuBjtpi5BcqtwWury/
MhOu2VbqVKUahu3V1oflQGXXWfKMsGweD0i3AtjMXoIzxAwQi3NJstzjohRtLB8c
CSvO4QN+PgZqREXnkM8jkp08x9feZE3z9mm3d+yeLtF+jVK8LFw9QFWOziSPF3wR
W9qLpG4xAoGBAPjS5VAhnHkqj8IRId8OwxzgtWyKz3tgz/mavu5kceSBMZwKg53r
L6tqDCp5ULRtvDlo051c73eUjAn2pOlenFjw41wo3KvCy9AegUB4Eb29JCHSdXRi
eGSdBNsd++D8XWovzxnREeIbu5dPJnyXcmTZSb4LOsY9D80q8JfWn/J5AoGBAOzf
uA+7oAbPbPfeSXlSqvTtntJUsldp1a2rBRNHHYPjWmpTSQwREnO3WHroa6IMFZM0
i5gf6soHt9dT+uG0OrK3o9VmBk2LlqjJ5dHkPgBYWU8f05JvFPsMifhsfb5SNg+X
s/opuYTDgGF4KjZiFw9j5IbGNrppM2q1wWwQ08APAoGBAKK+xe+4XDDADtD/Balw
bVuI+gDpdbWb58u5VCJ2niu3+Ku4EMgMWNyD+zsYqBmrII5oKujlLVeIWGP353sg
P6DIxdy0eUj6mvYfzahOIER63pQkpGAgO+CK9u8eWG2agrr+VDXtkgXS38qioWii
YdbmbB4aGDIrJBesezrEzU75AoGBAM2sv7xhdOUU7JAY4gFHR4veW55TznraOaBP
M9v2Tul62sIhyPaCSFxToa8u6m5NQdqMsH0JkauUoguFLv0VqjYOAGLF8MkaYFn0
3Omv3iocjIeIogOvEi+eRpp/UXuVFL3bQnoACsb9nhaD6VtJNq61VPO1XkXkHLIW
WXmE7uf/AoGBAPFJINMZFeA0T80ln/hIdv3dCXdUty3wgpifoxTyt+RQYitfmkqm
4WZCIYG/05+Lv4y5bbJP93uwW4Fpd2iKvJ8WgzQzR4ZDxkIBWHxfyzA6/qbVzkMi
Y8Tj5gg8KGgAl+bkGZh5QLbCZ7lB8C3vv/6ReDewH/bnSYftDRY/lA8H
-----END RSA PRIVATE KEY-----
```

### Dados de acesso AWS

```
//verifique se os dados de acesso estão validos
https://aws.amazon.com/console/
influxaws@gmail.com
Ix769b52016
```