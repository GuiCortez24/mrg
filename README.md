# 🏢 Sistema de Gerenciamento de Seguros - MRG Seguros

Um sistema web completo desenvolvido em PHP para gerenciamento de propostas e clientes de seguros, desenvolvido especificamente para a MRG Seguros.

## 📋 Índice

- [Sobre o Projeto](#sobre-o-projeto)
- [Funcionalidades](#funcionalidades)
- [Tecnologias Utilizadas](#tecnologias-utilizadas)
- [Estrutura do Projeto](#estrutura-do-projeto)
- [Instalação](#instalação)
- [Configuração](#configuração)
- [Banco de Dados](#banco-de-dados)
- [Uso do Sistema](#uso-do-sistema)
- [Arquitetura](#arquitetura)
- [Segurança](#segurança)
- [Contribuição](#contribuição)
- [Licença](#licença)

## 🎯 Sobre o Projeto

O Sistema de Gerenciamento de Seguros MRG é uma aplicação web desenvolvida para facilitar o gerenciamento de propostas de seguros, clientes e relatórios de produção. O sistema oferece uma interface intuitiva para corretores e administradores gerenciarem suas operações de seguros de forma eficiente.

### Principais Características:
- ✅ Sistema de autenticação seguro
- 📊 Dashboard com métricas em tempo real
- 📄 Gerenciamento completo de propostas
- 📈 Relatórios e análises de produção
- 🔍 Sistema avançado de busca e filtros
- 📱 Interface responsiva
- 📋 Sistema de notificações

## 🚀 Funcionalidades

### 🔐 Autenticação e Usuários
- Sistema de login seguro com hash de senhas
- Gerenciamento de usuários
- Controle de sessões
- Redirecionamento automático baseado em autenticação

### 📊 Dashboard Principal
- Visão geral da produção
- Saudação personalizada baseada no horário
- Cards de clientes com informações resumidas
- Sistema de cores para status (Emitida, Cancelada, Aguardando)
- Indicadores visuais para vigências curtas

### 👥 Gerenciamento de Clientes
- **Adicionar Propostas**: Formulário completo com validações
- **Editar Clientes**: Modificação de dados existentes
- **Excluir Registros**: Remoção segura com confirmação
- **Busca Avançada**: Por nome, CPF, item segurado, vigência
- **Paginação**: Navegação eficiente em grandes volumes
- **Upload de PDFs**: Múltiplos anexos por proposta
- **Anotações**: Sistema de notas personalizadas

### 🏢 Gestão de Seguradoras
- Cadastro de seguradoras parceiras
- Informações de acesso (usuário, senha, 0800)
- Interface de busca e paginação
- Edição e exclusão de registros

### 📈 Relatórios e Análises
- **Produção Mensal**: Análise detalhada por mês
- **Comparação Anual**: Comparativo entre anos
- **Gráficos Interativos**: Visualização de dados com Chart.js
- **Exportação PDF**: Relatórios em formato PDF
- **Business Intelligence**: Integração com Google Looker Studio

### 🗓️ Gestão de Ramos de Seguro
- Cadastro de tipos de seguro
- Organização por categorias
- Interface de gerenciamento completa

### 🔔 Sistema de Notificações
- Registro automático de ações
- Histórico de atividades
- Notificações em tempo real

## 🛠️ Tecnologias Utilizadas

### Backend
- **PHP 7.4+** - Linguagem principal
- **MySQL** - Banco de dados
- **TCPDF** - Geração de PDFs
- **JpGraph** - Gráficos e visualizações

### Frontend
- **HTML5** - Estrutura
- **CSS3** - Estilização
- **Bootstrap 5.3** - Framework CSS
- **JavaScript** - Interatividade
- **Chart.js** - Gráficos interativos
- **Bootstrap Icons** - Ícones

### Ferramentas de Desenvolvimento
- **Composer** - Gerenciamento de dependências
- **XAMPP** - Ambiente de desenvolvimento

## 📁 Estrutura do Projeto

```
mrg/
├── 📄 index.php                 # Ponto de entrada principal
├── 🔐 auth.php                  # Verificação de autenticação
├── 🗄️ db.php                    # Conexão com banco de dados
├── 🔑 login.php                 # Página de login
├── 📊 cliente_db.sql            # Estrutura do banco de dados
├── 📝 README.md                 # Este arquivo
├── 📜 LICENSE                   # Licença do projeto
│
├── 📂 PHP_PAGES/                # Páginas principais
│   ├── 📊 dashboard.php         # Painel principal
│   ├── ➕ add.php               # Adicionar proposta
│   ├── ✏️ edit.php              # Editar proposta
│   ├── 📅 months.php            # Produção mensal
│   ├── 👥 register.php          # Registro de usuários
│   ├── 🏢 info_loja.php         # Informações das seguradoras
│   ├── 🛡️ ramos_seguro.php      # Gerenciar ramos
│   ├── 📈 relatorio_bi.php      # Business Intelligence
│   └── 📊 clients_by_month.php  # Clientes por mês
│
├── 📂 PHP_ACTION/               # Processamento de ações
│   ├── ➕ handle_add.php        # Processar adição
│   ├── ✏️ handle_edit.php       # Processar edição
│   ├── 🗑️ delete.php            # Processar exclusão
│   ├── 📝 handle_notes.php      # Processar anotações
│   ├── 🏢 handle_seguradoras.php # Processar seguradoras
│   ├── 📊 summary.php           # Resumos de produção
│   ├── 📈 comparison.php        # Comparações anuais
│   ├── 📄 generate_pdf.php      # Gerar PDFs
│   ├── 📊 generate_report.php   # Gerar relatórios
│   ├── 🔄 update_status.php     # Atualizar status
│   ├── 📤 upload.php            # Upload de arquivos
│   ├── 📤 export.php            # Exportação de dados
│   ├── 🔍 verificar_proposta.php # Verificar duplicatas
│   └── 🚪 logout.php            # Logout do sistema
│
├── 📂 INCLUDES/                 # Componentes reutilizáveis
│   ├── 🎨 header.php            # Cabeçalho padrão
│   ├── 🦶 footer.php            # Rodapé padrão
│   ├── 🧭 navbar.php            # Barra de navegação
│   ├── 🔍 dashboard_search_form.php # Formulário de busca
│   ├── 📋 cliente_card.php      # Card de cliente
│   ├── 📅 month_card.php        # Card de mês
│   ├── 🏢 seguradora_card.php   # Card de seguradora
│   ├── 📄 pagination.php        # Componente de paginação
│   ├── 📝 form_fields_*.php     # Campos de formulário
│   ├── 🔧 functions.php         # Funções auxiliares
│   └── 📂 seguradoras/          # Componentes de seguradoras
│
├── 📂 CSS/                      # Folhas de estilo
│   └── 📅 months.css            # Estilos específicos
│
├── 📂 JS/                       # Scripts JavaScript
│   └── 🔍 verificar_proposta.js # Validação de propostas
│
├── 📂 IMG/                      # Imagens
│   ├── 🖼️ logo.png              # Logo principal
│   └── 🖼️ logoM.png             # Logo mobile
│
└── 📂 uploads/                  # Arquivos enviados
    └── 📄 *.pdf                 # PDFs das propostas
```

## ⚙️ Instalação

### Pré-requisitos
- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Apache/Nginx
- XAMPP (recomendado para desenvolvimento)

### Passos de Instalação

1. **Clone o repositório**
   ```bash
   git clone [URL_DO_REPOSITORIO]
   cd mrg
   ```

2. **Configure o servidor web**
   - Copie os arquivos para o diretório do seu servidor web
   - Para XAMPP: `C:\xampp\htdocs\mrg\`

3. **Instale as dependências**
    ```bash
    composer install
    ```

4. **Configure o banco de dados**
   - Crie um banco de dados MySQL chamado `mrg`
   - Importe o arquivo `cliente_db.sql`

5. **Configure as credenciais**
   - Agora o projeto lê variáveis de ambiente. Configure-as no seu servidor (ou `.env` do Docker/Apache):
   ```bash
   # Exemplo (Windows PowerShell)
   setx DB_HOST "localhost"
   setx DB_USER "root"
   setx DB_PASS "sua_senha"
   setx DB_NAME "mrg"
   ```
   - Alternativamente, edite `db.php` para valores fixos (não recomendado em produção).

## 🔧 Configuração

### Configuração do Banco de Dados

O arquivo `db.php` usa as variáveis de ambiente `DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME` e define `utf8mb4`. Caso não estejam definidas, ele usa valores padrão.

### Configuração de Upload

O sistema permite upload de PDFs. Configure as permissões da pasta `uploads/`:
```bash
chmod 755 uploads/
```

### Configuração de Sessões e CSRF

O sistema utiliza sessões PHP e proteção CSRF. Certifique-se de que as sessões estão habilitadas no seu `php.ini`:
```ini
session.auto_start = 0
session.save_path = "/tmp"
```

## 🗄️ Banco de Dados

### Tabelas Principais

#### `clientes`
Armazena todas as informações dos clientes e propostas:
- `id` - Chave primária
- `inicio_vigencia` - Data de início da vigência
- `final_vigencia` - Data final da vigência
- `apolice` - Número da apólice
- `nome` - Nome do cliente
- `cpf` - CPF/CNPJ do cliente
- `numero` - Telefone de contato
- `email` - Email do cliente
- `pdf_path` - Caminho dos PDFs (JSON)
- `premio_liquido` - Valor do prêmio
- `comissao` - Percentual de comissão
- `status` - Status da proposta
- `tipo_operacao` - NOVO ou RENOVAÇÃO
- `seguradora` - Nome da seguradora
- `tipo_seguro` - Tipo do seguro
- `item_segurado` - Descrição do item segurado
- `item_identificacao` - Placa/ID do item
- `anotacoes` - Anotações do corretor

#### `usuarios`
Gerencia os usuários do sistema:
- `id` - Chave primária
- `nome` - Nome do usuário
- `email` - Email (único)
- `senha` - Senha criptografada

#### `seguradoras`
Informações das seguradoras parceiras:
- `id` - Chave primária
- `nome` - Nome da seguradora
- `usuario` - Usuário de acesso
- `senha` - Senha de acesso
- `numero_0800` - Telefone de atendimento

#### `notificacoes`
Sistema de notificações:
- `id` - Chave primária
- `usuario_id` - ID do usuário
- `mensagem` - Texto da notificação
- `data_hora` - Data e hora da notificação

## 🎮 Uso do Sistema

### 1. Acesso ao Sistema
- Acesse `http://localhost/mrg/`
- Faça login com suas credenciais
- O sistema redirecionará automaticamente para o dashboard

### 2. Dashboard Principal
- Visualize todos os clientes em cards organizados
- Use os filtros de busca para encontrar clientes específicos
- Clique em "Saiba Mais" para ver detalhes completos

### 3. Adicionar Nova Proposta
- Clique em "Adicionar Proposta"
- Preencha todos os campos obrigatórios
- Faça upload dos PDFs necessários
- O sistema detectará automaticamente se é NOVO ou RENOVAÇÃO

### 4. Relatórios e Análises
- Acesse "Produção Mensal" para ver análises por mês
- Use "Business Intelligence" para relatórios avançados
- Exporte relatórios em PDF

### 5. Gerenciamento
- Configure seguradoras em "Informações das Seguradoras"
- Gerencie ramos de seguro conforme necessário
- Adicione anotações aos clientes

## 🏗️ Arquitetura

### Padrão MVC Simplificado
- **Model**: Classes de acesso ao banco de dados
- **View**: Templates PHP com HTML
- **Controller**: Páginas PHP que processam requisições

### Estrutura de Componentes
- **INCLUDES/**: Componentes reutilizáveis
- **PHP_PAGES/**: Páginas principais do sistema
- **PHP_ACTION/**: Processamento de ações e formulários

### Separação de Responsabilidades
- **Interface**: Bootstrap + CSS customizado
- **Lógica**: PHP com prepared statements
- **Dados**: MySQL com relacionamentos
- **Uploads**: Sistema de arquivos local

## 🔒 Segurança

### Medidas Implementadas
- ✅ **Hash de Senhas**: `password_hash()` e `password_verify()`
- ✅ **Prepared Statements**: Previne SQL Injection
- ✅ **Validação de Entrada**: Sanitização de dados de formulários
- ✅ **Controle de Sessão**: Login obrigatório em páginas internas
- ✅ **Proteção CSRF**: Token por sessão validado nos formulários sensíveis
- ✅ **Upload Seguro**: Restrições de tipo e nome de arquivos
- ✅ **XSS Protection**: `htmlspecialchars()` em saídas

### Recomendações Adicionais
- Use HTTPS em produção
- Configure headers de segurança (CSP, HSTS, X-Frame-Options)
- Implemente rate limiting para endpoints críticos
- Mantenha o sistema e dependências atualizados
- Faça backup periódico do banco e da pasta `uploads/`

## 📊 Funcionalidades Avançadas

### Sistema de Busca
- Busca por múltiplos critérios
- Filtros de data com lógica inteligente
- Paginação eficiente
- Resultados em tempo real

### Upload de Múltiplos Arquivos
- Suporte a múltiplos PDFs por proposta
- Validação de tipo de arquivo
- Nomenclatura única para evitar conflitos
- Interface drag-and-drop

### Relatórios Dinâmicos
- Gráficos interativos com Chart.js
- Comparações entre períodos
- Exportação em PDF
- Integração com BI externo

### Sistema de Notificações
- Registro automático de ações
- Histórico completo de atividades
- Notificações em tempo real
- Filtros por usuário

## 🐛 Troubleshooting

### Problemas Comuns

#### Erro de Conexão com Banco
```
Falha na conexão: Access denied for user
```
**Solução**: Verifique as credenciais no arquivo `db.php`

#### Upload de Arquivos Falha
```
Warning: move_uploaded_file failed
```
**Solução**: Verifique as permissões da pasta `uploads/`

#### Sessão Não Funciona
```
Session not started
```
**Solução**: Verifique se as sessões estão habilitadas no PHP

#### PDFs Não Carregam
```
File not found
```
**Solução**: Verifique se os arquivos estão na pasta `uploads/`

### Logs e Debug
- Ative o log de erros do PHP
- Verifique os logs do Apache/Nginx
- Use `var_dump()` para debug (remover em produção)

## 🚀 Melhorias Futuras

### Funcionalidades Planejadas
- [ ] API REST (PHP) para integrações (leitura e escrita com JWT)
- [ ] Sistema de backup automático (DB + uploads)
- [ ] Notificações por email e WhatsApp (opt-in)
- [ ] Dashboard mobile nativo
- [ ] Módulo de comissões (regras e relatórios)
- [ ] Relatórios personalizáveis e agendados
- [ ] Sistema avançado de papéis e permissões (RBAC)

### Otimizações Técnicas
- [ ] Cache de consultas
- [ ] Compressão de imagens e lazy loading
- [ ] CDN para assets estáticos
- [ ] Migração para PDO com tipagem e exceptions
- [ ] Implementação de camada DAO/Repository
- [ ] Testes automatizados (PHPUnit)

## 👥 Contribuição

### Como Contribuir
1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

### Padrões de Código
- Use PSR-12 para PHP
- Comente funções complexas
- Mantenha a consistência de nomenclatura
- Teste suas alterações

### Reportar Bugs
- Use o sistema de Issues do GitHub
- Inclua informações detalhadas
- Adicione screenshots se necessário
- Especifique a versão do PHP e MySQL

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo `LICENSE` para mais detalhes.

## 📞 Suporte

Para suporte técnico ou dúvidas:
- 📧 Email: [seu-email@exemplo.com]
- 📱 WhatsApp: [seu-numero]
- 🌐 Website: [seu-website.com]

## 🙏 Agradecimentos

- Bootstrap pela interface responsiva
- Chart.js pelos gráficos interativos
- TCPDF pela geração de PDFs
- Comunidade PHP pelo suporte
- MRG Seguros pela confiança no projeto

---

**Desenvolvido com ❤️ para MRG Seguros**

*Última atualização: Janeiro 2025*