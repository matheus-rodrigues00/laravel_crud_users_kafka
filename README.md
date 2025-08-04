# Laravel CRUD Users com Arquitetura Event-Driven Kafka

Uma aplicação Laravel com arquitetura de microserviços event-driven usando Apache Kafka para comunicação em tempo real entre serviços.

## Arquitetura do Sistema

```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│   Laravel   │───▶│    Kafka    │───▶│   Node.js   │
│     API     │    │   Broker    │    │Microservice │
└─────────────┘    └─────────────┘    └─────────────┘
       │                   │                   │
       │                   │                   │
       ▼                   ▼                   ▼
   Usuário Criado      user-events      Email de Boas-vindas
   Evento Disparado      Tópico          Enviado ao Usuário
```

## Funcionalidades

- **CRUD de Usuários**: Métodos para criação/deleção/atualização/leitura de Usuários
- **Arquitetura Event-Driven**: Comunicação em tempo real entre serviços
- **Integração Apache Kafka**: Streaming e processamento de mensagens
- **Comunicação entre Microserviços**: Serviços desacoplados com mensageria Kafka
- **Automação de Email de Boas-vindas**: Envio automático de email quando usuários são criados (mock)
- **Monitoramento Kafka UI**: Interface web para monitorar tópicos e mensagens (http://localhost:8080)
- **Testes Abrangentes**: Suite completa de testes de integração
- **Containerização Docker**: Deploy e desenvolvimento facilitados

## Pré-requisitos

- Docker e Docker Compose instalados
- Pelo menos 4GB de RAM disponível (para Kafka, MySQL e serviços)

## Início Rápido

### 1. Iniciar Todos os Serviços
```bash
docker-compose up -d
```

### 2. Executar Migrações do Banco
```bash
docker-compose exec laravel php artisan migrate
```
## URLs dos Serviços

- **Laravel API**: http://localhost:8000
- **Documentação da API (Swagger)**: http://localhost:8000/api/documentation
- **Node.js Microservice**: http://localhost:3000
- **Kafka UI**: http://localhost:8080
- **MySQL Database**: localhost:3306

## API Documentation

### Documentação Swagger/OpenAPI

A API está documentada usando a especificação Swagger/OpenAPI 3.0.

- **Swagger UI**: http://localhost:8000/api/documentation

### Endpoints da API Laravel

#### Listar Usuários
```bash
curl -H "Accept: application/json" http://localhost:8000/users
```

#### Criar Usuário (Dispara Evento Kafka)
```bash
curl -X POST -H "Accept: application/json" -H "Content-Type: application/json" \
  -d '{"name": "João Silva", "email": "joao@exemplo.com", "password": "MatheusPassword1"}' \
  http://localhost:8000/users
```

#### Buscar Usuário por ID
```bash
curl -H "Accept: application/json" http://localhost:8000/users/1
```

#### Atualizar Usuário
```bash
curl -X PUT -H "Accept: application/json" -H "Content-Type: application/json" \
  -d '{"name": "João Atualizado", "email": "joao.atualizado@exemplo.com"}' \
  http://localhost:8000/users/1
```

#### Deletar Usuário
```bash
curl -X DELETE -H "Accept: application/json" http://localhost:8000/users/1
```

#### Health Check
```bash
curl -H "Accept: application/json" http://localhost:8000/health
```

### Endpoints do Microservice Node.js

#### Health Check com Status Kafka e de Emails Enviados
```bash
curl http://localhost:3000/external
```

## Testes

### Testes de Integração
```bash
docker-compose exec -e DB_DATABASE=laravel_testing laravel php artisan migrate
docker-compose exec laravel php artisan test
```

### Como os Testes Funcionam

Os testes rodam em um **banco de dados separado** (`laravel_testing`) para não interferir com os dados de desenvolvimento:

- **Banco de Teste**: `laravel_testing` (isolado do banco principal)
- **Ambiente**: `testing` (detectado automaticamente pelo Laravel)
- **Dados**: Cada teste limpa o banco antes de executar
- **Kafka**: Eventos são automaticamente ignorados durante testes

### O que os Testes Verificam

#### Testes de API (UserApiTest)
- ✅ **Criação de usuários** com validação de campos obrigatórios
- ✅ **Listagem de usuários** retorna dados corretos
- ✅ **Busca de usuário específico** por ID
- ✅ **Atualização de usuários** com dados válidos
- ✅ **Exclusão de usuários** com confirmação
- ✅ **Validação de email único** (não permite duplicatas)
- ✅ **Health check** da API retorna status correto

#### Testes de Ambiente Kafka (KafkaEnvironmentTest)
- ✅ **Detecção de ambiente de teste** funciona corretamente
- ✅ **KafkaService ignora** operações em ambiente de teste
- ✅ **Eventos não são enviados** para Kafka durante testes

#### Testes de Exemplo (ExampleTest)
- ✅ **Resposta básica da aplicação** funciona
- ✅ **Estrutura de testes** está configurada corretamente

### Executando Testes Específicos

```bash
# Todos os testes
docker-compose exec laravel php artisan test

# Apenas testes de API
docker-compose exec laravel php artisan test --filter=UserApiTest

# Apenas testes de Kafka
docker-compose exec laravel php artisan test --filter=KafkaEnvironmentTest

# Teste específico
docker-compose exec laravel php artisan test --filter=test_can_create_user
```

### Passos de Teste Manual

1. **Criar um Usuário**:
   ```bash
   curl -X POST -H "Accept: application/json" -H "Content-Type: application/json" \
     -d '{"name": "Usuário Teste", "email": "teste@exemplo.com"}' \
     http://localhost:8000/users
   ```

2. **Verificar Kafka UI** (http://localhost:8080):
   - Navegar para Topics → user-events
   - Visualizar a mensagem com evento de criação de usuário

3. **Verificar Logs do Microservice**:
   ```bash
   docker-compose logs node-microservice
   ```



## Cenários de Teste de Erro

### Erro de Validação (Campos Obrigatórios Ausentes)
```bash
curl -X POST -H "Accept: application/json" -H "Content-Type: application/json" \
  -d '{"name": "Teste"}' \
  http://localhost:8000/users
```

### Erro de Email Duplicado
```bash
curl -X POST -H "Accept: application/json" -H "Content-Type: application/json" \
  -d '{"name": "Usuário Duplicado", "email": "existente@exemplo.com"}' \
  http://localhost:8000/users
```

### Usuário Não Encontrado (Erro 404)
```bash
curl -H "Accept: application/json" http://localhost:8000/users/999
```

## Monitoramento

### Kafka UI
- Acesso: http://localhost:8080
- Monitorar tópicos, mensagens e grupos de consumidores
- Visualizar payloads de mensagens e metadados
- Acompanhar lag de consumidores e performance

### Health Checks dos Serviços
- Laravel API: `GET /health`
- Microservice: `GET /external` 

## Componentes da Arquitetura

### Aplicação Laravel
- **KafkaService**: Gerencia produção de mensagens para tópicos Kafka
- **UserCreated Event**: Disparado quando um usuário é criado
- **SendUserCreatedToKafka Listener**: Processa eventos e envia para Kafka
- **EventServiceProvider**: Registra event listeners
- **Proteção de Ambiente**: Detecta ambiente de teste e evita envio de eventos Kafka

### Microservice Node.js
- **Kafka Consumer**: Escuta o tópico `user-events`
- **Mock Email Service**: Simula envio de emails de boas-vindas
- **Health Endpoints**: Monitora status do serviço e estatísticas

### Infraestrutura Kafka
- **Zookeeper**: Gerencia metadados do cluster Kafka
- **Kafka Broker**: Gerencia armazenamento e distribuição de mensagens
- **Kafka UI**: Interface web para monitoramento

## Fluxo de Eventos

1. **Criação de Usuário**: Requisição POST para API Laravel
2. **Dispatch do Evento**: Laravel dispara evento `UserCreated`
3. **Produção Kafka**: Listener envia mensagem para tópico `user-events`
4. **Consumo de Mensagem**: Microservice Node.js consome a mensagem
5. **Processamento de Email**: Email de boas-vindas é "enviado" para o usuário
6. **Confirmação**: Confirmação de email enviado é registrada


## Solução de Problemas

### Problemas Comuns

1. **Falha na Conexão Kafka**
   ```bash
   docker-compose logs kafka
   docker-compose logs zookeeper
   ```

2. **Mensagens Não Recebidas**
   ```bash
   docker-compose logs node-microservice
   ```

3. **Evento Laravel Não Disparado**
   ```bash
   docker-compose logs laravel
   ```

## Considerações de Performance

- Mensagens Kafka são persistidas e sobrevivem a reinicializações de serviços
- Grupos de consumidores garantem entrega de mensagens com múltiplas instâncias
- Processamento em lote configurado para performance otimizada
- Mecanismos de tratamento de erro e retry implementados

## Notas de Segurança

- Configuração atual usa comunicação em texto plano (apenas desenvolvimento)
- Para produção: implementar criptografia SSL/TLS
- Adicionar mecanismos de autenticação e autorização
- Usar variáveis de ambiente seguras para configuração sensível