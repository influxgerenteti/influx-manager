# -*- coding: utf-8 -*-

import json
import os
from datetime import datetime

# Buscar todas as branches com prefixo "V-" ou "v-"
branches = [branch for branch in os.popen('git branch -r').read().splitlines() if branch.strip().startswith('origin/V-') or branch.strip().startswith('origin/v-')]

# Lista para armazenar as informações das branches formatadas
formatted_branches = []

# Iterar sobre as branches encontradas
for branch in branches:
    branch_name = branch.strip().replace('origin/', '')
    branch_name_formatted = branch_name.replace('V-', '').replace('v-', '').replace('.', '-')
    release_url = "https://beta.manager.influx.com.br/release/{}/".format(branch_name_formatted)
    notes_url = "https://s3.sa-east-1.amazonaws.com/beta.manager.influx.com.br/versions/{}.md".format(branch_name)
    
    # Use o comando 'git show' para obter a data da última modificação da branch
    branch_date_info = os.popen('git show --format="%cd" --date=local {}'.format(branch)).read().strip()
    

    data_original = branch_date_info.splitlines()[0]

    data_obj = datetime.strptime(data_original, "%a %b %d %H:%M:%S %Y")

    # Formatar a data no novo formato
    branch_date = data_obj.strftime("%d/%m/%Y - %H:%M")

    
    branch_info = {
        "nome": branch_name,
        "data": branch_date,
        "url": release_url,
        "notes": notes_url
    }
    
    formatted_branches.append(branch_info)

# Criar um dicionário com as informações formatadas
output_data = formatted_branches

# Converter o dicionário para JSON
json_data = json.dumps(output_data, indent=4)

# Exibir o JSON (opcional)
print(json_data)

# Salvar o JSON em um arquivo
with open('output/versions.json', 'w') as json_file:
    json_file.write(json_data)
