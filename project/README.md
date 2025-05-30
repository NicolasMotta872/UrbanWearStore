# StoreScobars - Loja Virtual

Sistema de e-commerce completo para a loja de roupas StoreScobars, desenvolvido com PHP e MySQL.

## Funcionalidades

### Área Pública
- Listagem de produtos por categoria
- Página de detalhes de produto
- Carrinho de compras
- Finalização de compra
- Página de agradecimento

### Área Administrativa
- Login e logout com controle de sessão
- CRUD de categorias
- CRUD de produtos (com upload de imagens)
- Visualização de vendas

## Tecnologias Utilizadas
- PHP (sem frameworks)
- MySQL
- HTML, CSS, JavaScript
- Sessões PHP para autenticação e carrinho

## Requisitos
- PHP 7.0 ou superior
- MySQL 5.7 ou superior
- Servidor web (Apache, Nginx, etc.)

## Instalação
1. Clone ou baixe o repositório
2. Configure o banco de dados no arquivo `config/database.php`
3. Acesse `config/setup.php` pelo navegador para criar as tabelas
4. Acesse a aplicação pelo navegador

## Usuário Admin Padrão
- Email: admin@loja.com
- Senha: admin123

## Estrutura do Banco de Dados
- categorias (id, nome)
- produtos (id, nome, descricao, preco, imagem, estoque, categoria_id)
- vendas (id, nome_cliente, email_cliente, endereco_cliente, total, data_venda)
- vendas_itens (id, venda_id, produto_id, quantidade, preco_unitario)
- usuarios (id, nome, email, senha, created_at)

## Autores
- StoreScobars Team