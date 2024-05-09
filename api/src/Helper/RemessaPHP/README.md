Para cadastrar a remessa de novos bancos, ver na documentação padrão do banco se é cnab240 ou cnab400
Ver qual é o padrão para os arquivos de remessa (header de arquivo, header de lote, detalhes segmentos, etc)
Devem ser visto os arquivos da pasta Yaml que contém o padrão de posicionamento dos caracteres das remessas
Primeiro vai ser procurado na pasta com o código do banco pelos campos e posições, e onde não houver, vai ser considerado
    os campos e posiçẽos dos arquivos da pasta 'generic'
O processo pra gerar a remessa que deve ser visto para alterar e gerar os dados, começa no RemessaFacade.php -> imprimirRemessaBanco