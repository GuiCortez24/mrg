# Gerenciador de Apólices de Seguro (CRM para Corretores)

Este é um sistema de CRM (Customer Relationship Management) desenvolvido em PHP e MySQL, focado nas necessidades de corretores de seguros. A plataforma permite o gerenciamento completo de clientes e suas apólices, com funcionalidades de análise, geração de relatórios e um painel de controle intuitivo.

## ✨ Funcionalidades Principais

O sistema foi estruturado em componentes e conta com as seguintes funcionalidades:

#### **Painel de Controle (Dashboard)**
-   **Visualização em Cards:** Exibição clara e organizada de todos os clientes em cards interativos.
-   **Código de Cores Dinâmico:** Os cards mudam de cor (azul, verde, amarelo, vermelho) para indicar o status da apólice e a proximidade do vencimento.
-   **Busca e Filtros Avançados:** Poderoso formulário de busca que permite filtrar clientes por Nome, CPF, Placa/ID do item e período de vencimento (Final da Vigência).
-   **Paginação Inteligente:** Navegação por páginas que mantém os filtros de busca ativos.
-   **Animações Sutis:** Efeito de "elevação" nos cards ao passar o mouse para uma experiência de usuário mais fluida.

#### **Gestão de Apólices (Clientes)**
-   **CRUD Completo:** Funcionalidades para Adicionar, Editar e Excluir apólices de clientes.
-   **Lógica de Negócio (Novo vs. Renovação):** O sistema identifica automaticamente se uma nova apólice é um **Seguro Novo** ou uma **Renovação** com base no histórico do cliente (CPF) ou do item (Placa/ID).
-   **Campos Dinâmicos:** O formulário de apólice exibe campos contextuais, como "Placa" para Seguro Auto/Moto e "ID" para outros ramos.
-   **Verificação de Proposta Duplicada:** Sistema de verificação em tempo real (via AJAX) que alerta o usuário se o número da proposta já existe no banco de dados.

#### **Análise e Relatórios**
-   **Visualização de Produção Mensal:** Uma tela dedicada para navegar pela produção de cada mês do ano.
-   **Resumo Mensal com Gráficos:** Modal interativo que exibe um resumo detalhado da produção do mês, com gráficos de pizza (gerados com Chart.js) para:
    -   Prêmio por Seguradora
    -   Clientes por Seguradora
    -   Prêmio por Tipo de Seguro
    -   Clientes por Tipo de Seguro
-   **Comparativo Anual:** Ferramenta para comparar a produção de um mês com o mesmo mês de um ano anterior, mostrando a variação percentual.
-   **Geração de PDFs Profissionais:**
    -   Relatório de **Produção Mensal** em formato de tabela.
    -   Relatório de **Renovações** por período selecionado.
    -   Relatório de **Resumo Mensal** com gráficos e totais.
-   **Integração com BI Externo:** Página dedicada para incorporar dashboards interativos feitos em **Looker Studio**.

#### **Administração**
-   **Gestão de Seguradoras:** Tela para cadastrar, editar e excluir as informações e credenciais das seguradoras parceiras.
-   **Sistema de Notificações:** Notificações em tempo real na barra de navegação sobre ações importantes (ex: novas propostas), com opção de dispensar individualmente ou todas de uma vez.
-   **Autenticação:** Sistema seguro de login e logout com sessões PHP e senhas criptografadas (hash).

## 🛠️ Tecnologias Utilizadas

-   **Backend:**
    -   **PHP 8+**
    -   **MySQL** para o banco de dados.
    -   **Composer** para gerenciamento de dependências.
    -   **TCPDF** para a geração de relatórios em PDF.
-   **Frontend:**
    -   **HTML5 / CSS3**
    -   **Bootstrap 5** para a estrutura responsiva e componentes de UI.
    -   **Bootstrap Icons** para a iconografia.
    -   **JavaScript (ES6+)** para interatividade, AJAX (Fetch API) e manipulação do DOM.
    -   **Chart.js** (via API QuickChart.io e renderização local) para os gráficos do resumo.

## 🗂️ Estrutura de Pastas

O projeto foi refatorado para uma estrutura organizada e de fácil manutenção:

-   `/**raiz**`: Contém arquivos de configuração (`db.php`), login e o roteador principal (`index.php`).
-   `/PHP_PAGES/`: Contém as páginas principais que o usuário vê (Dashboard, Formulários, etc.).
-   `/PHP_ACTION/`: Contém os scripts que processam dados (salvar formulários, gerar PDFs, etc.).
-   `/INCLUDES/`: Contém componentes reutilizáveis como `header.php`, `footer.php`, `navbar.php` e `cliente_card.php`.
-   `/CSS/`: Contém os arquivos de estilo customizados.
-   `/JS/`: Contém os arquivos JavaScript customizados.
-   `/vendor/`: Pasta gerenciada pelo Composer, contém as bibliotecas de backend (TCPDF).

## 🚀 Como Executar o Projeto

1.  **Clone o repositório** para sua pasta de servidor local (ex: `C:/xampp/htdocs/`).
2.  **Importe o Banco de Dados:** Use um cliente MySQL (como phpMyAdmin) para importar o arquivo `cliente_db.sql`.
3.  **Configure a Conexão:** Edite o arquivo `db.php` com as suas credenciais de acesso ao banco de dados (host, usuário, senha, nome do banco).
4.  **Instale as Dependências:** Navegue até a pasta raiz do projeto via terminal e rode o comando:
    ```bash
    composer install
    ```
5.  **Inicie seu Servidor:** Inicie o Apache e o MySQL no seu painel XAMPP (ou similar).
6.  **Acesse o Sistema:** Abra o navegador e acesse `http://localhost/nome_da_pasta_do_projeto/`.