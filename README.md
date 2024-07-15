# Log Reader
Este projeto é uma aplicação Laravel para processar arquivos de log, armazenar os dados processados no banco de dados e gerar relatórios com base nesses dados.


## Estrutura de Containers
O projeto utiliza Docker para isolar e gerenciar os serviços necessários. Abaixo está um diagrama que ilustra a estrutura de containers do projeto.

![Docker Diagram](https://kroki.io/plantuml/svg/eNptUT1Pw0AM3f0rrEwwBEVMUQbUoUJdikAqU5XBJKaJkvvo-aJSof53LpeWtAhPd37Pfs82LMST84PqAaRrtSVHCvtWsz9aRuN8YwC0qRmTpak6drgy4hP8BgxxA7ywPxjXXbAxtnrX6q-Cehs6lkiCMTHjtrFF_vCYflqVXrFCeuaoo-z7wMoiFH8z6Lhu5VogJuCPgxLT9CmqlVhg8mzcgVwduPuBxUtyY-hMjkKRviRPHySMldGaK98a_W9FlI4VbwMPjIo07Vix9hP9BCeAqjdDjcm4RVxT1QTfl41t34VdOZKm19T1PEDoutpsXn89450N18E8y7P7BG7GnIrnArFGC4cpYcG6Hm_9A9U7l8Q=)

- __nginx:__ Servidor web NGINX que encaminha as requisições para o container PHP.
- __php:8.2-fpm-alpine:__ Container PHP que roda a aplicação Laravel.
- __mysql:8.0:__ Container MySQL usado como banco de dados.
- __redis:__ Container Redis usado para gerenciamento de filas.

## Estrutura do Banco de Dados

Optei por uma estrutura de banco de dados simplificada, focada nos requisitos do teste. As tabelas consumers e services armazenam informações sobre consumidores e serviços, respectivamente, enquanto a tabela requests registra os detalhes de cada requisição. Os atributos selecionados para as tabelas foram escolhidos com base nos requisitos do teste e nos dados presentes no log de exemplo. Incluí informações como ID do consumidor, ID do serviço, método HTTP, URI, status da resposta, tempos de processamento e timestamp da requisição. Esses dados são suficientes para gerar os relatórios solicitados e permitem uma análise básica do tráfego da API.

Em um ambiente de produção, poderia ser considerada a adição de outras tabelas e campos para armazenar informações mais detalhadas sobre as requisições, como cabeçalhos, parâmetros de consulta, dados do corpo da requisição, etc. No entanto, para os fins deste teste, a estrutura simplificada atual é suficiente e permite focar no processamento e análise dos dados que seriam relevantes para o teste.

![Database Diagram](https://kroki.io/plantuml/svg/eNrFk8FuwjAMhu95iohLt2nwAFOFOO2yh4hCY1pLbQqOs4Fg7z43tGWwHtB22KWN_TvJ5z-JWgW2xLGplarQgS6QihqUAs_IBz0rWh9iAxRm2gY9RvqotH5Cp1_0Gkv0rPO8BA9kGdxyKeJ8Lp8YU0n65Xn0uIuQ1IKgqzSWRWZsQDCabTdj66aEzwtRAHrHAs5AQ_CPPASySOAzzxDcyTP4aa7BXt9SSd_ctNgAV22XDkzoyw6V8HsooByDZGR3CXsy0_UwJrfU7g_XqVIIP-xNMl2SKXv-4FvdlmaDNRiBkBMM_Yn-TN_p5dpyURl0kx6Aj81D1i8pWvassw16DBW4NLaypcseZQ4QtWQEONiyc4Bhz7_r9PJaTqfFoj2Ot0ON1_ZWUCvwLr3GYfAF6h01Bw==)

## Como Executar o Projeto

### Pré-requisitos
- Docker e Docker Compose
- Git

## Passos
1. Clone o repositório:
```
git clone https://github.com/ruan-lucas/log-reader.git
cd log-reader
```
2. Configure o arquivo `.env`:
```
cp .env.example .env
```
3. Estando na raiz do projeto, execute:
```
docker-compose up
```
4. Execute o seguinte comando para finalizar a instalação:
````
docker exec log_reader_app bash -c "
    composer install &&
    php artisan migrate &&
    php artisan config:clear &&
    php artisan cache:clear &&
    npm install &&
    npm run dev
"
5. Acesse a aplicação:
-  A aplicação estará disponível em `http://localhost:8080`.

## Como testar
Ao acessar o projeto, você verá uma interface com duas tabelas principais:
1. __Arquivos:__
    - Esta tabela exibe todos os arquivos de log que foram enviados.
    - Também há uma opção para enviar novos arquivos de log.
    - A tabela de arquivos de log verifica o status do processamento a cada 10 segundos. Isso foi implementado como uma solução simples de polling, onde o frontend periodicamente consulta o backend para verificar o status. Embora pudéssemos usar websockets para uma comunicação em tempo real, optamos por seguir com essa abordagem para simplificar a implementação.
2. __Relatórios:__
    - Esta tabela exibe os relatórios disponíveis para baixar assim que o processamento estiver concluído.

## Funcionamento do Envio de Arquivos
- Divisão em Chunks
    - Quando um arquivo de log é enviado, ele é dividido em chunks de 25.000 linhas.
    - Cada chunk é processado por um job separado para melhorar a eficiência e reduzir a carga no sistema.
- Processamento por Jobs:
    - Os chunks são gerenciados e processados por __dois workers__ simultaneamente.
    - Isso permite que o processamento de grandes arquivos de log seja mais eficiente. Ex: Um arquivo de 100.000 linhas, é processado em cerca de 2 minutos. Poderiam haver mais para dividir o processamento, porém resolvi colocar poucos workers para garantir que não haverá nenhum empecilho relacionado a memória da máquina.

## Executando os Testes Automatizados
Para executar os testes do Laravel, você pode usar o comando Artisan `test`:

```
php artisan test
```
Isso executará todos os testes definidos na aplicação.