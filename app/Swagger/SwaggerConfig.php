<?php

namespace App\Swagger;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="API Laravel Kafka Challenge",
 *     description="Documentação da API para o projeto Laravel Kafka Challenge",
 *     contact=@OA\Contact(
 *         email="admin@example.com",
 *         name="Suporte da API"
 *     ),
 *     license=@OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="http://localhost:8000",
 *     description="Servidor da API (Local)"
 * )
 * 
 * @OA\Server(
 *     url="http://127.0.0.1:8000",
 *     description="Servidor da API (Local IP)"
 * )
 * 
 * @OA\Tag(
 *     name="Health",
 *     description="Endpoints de verificação de saúde"
 * )
 * 
 * @OA\Tag(
 *     name="Users",
 *     description="Endpoints de gerenciamento de usuários"
 * )
 * 
 * @OA\Tag(
 *     name="External",
 *     description="Endpoints de integração com serviços externos"
 * )
 * 
 * @OA\Schema(
 *     schema="User",
 *     required={"name", "email"},
 *     @OA\Property(property="id", type="integer", example=1, description="ID do usuário"),
 *     @OA\Property(property="name", type="string", example="João Silva", description="Nome completo do usuário"),
 *     @OA\Property(property="email", type="string", format="email", example="joao@exemplo.com", description="Endereço de email do usuário"),
 *     @OA\Property(property="password", type="string", example="password123", description="Senha do usuário"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T00:00:00.000000Z", description="Data de criação"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T00:00:00.000000Z", description="Data da última atualização")
 * )
 * 
 * @OA\Schema(
 *     schema="UserCreateRequest",
 *     required={"name", "email", "password"},
 *     @OA\Property(property="name", type="string", example="João Silva", description="Nome completo do usuário"),
 *     @OA\Property(property="email", type="string", format="email", example="joao@exemplo.com", description="Endereço de email do usuário"),
 *     @OA\Property(property="password", type="string", example="password123", description="Senha do usuário")
 * )
 * 
 * @OA\Schema(
 *     schema="UserUpdateRequest",
 *     @OA\Property(property="name", type="string", example="João Silva", description="Nome completo do usuário"),
 *     @OA\Property(property="email", type="string", format="email", example="joao@exemplo.com", description="Endereço de email do usuário"),
 *     @OA\Property(property="password", type="string", example="password123", description="Senha do usuário")
 * )
 * 
 * @OA\Schema(
 *     schema="ApiResponse",
 *     @OA\Property(property="success", type="boolean", example=true, description="Status de sucesso da requisição"),
 *     @OA\Property(property="message", type="string", example="Operação realizada com sucesso", description="Mensagem de resposta"),
 *     @OA\Property(property="data", description="Dados da resposta"),
 *     @OA\Property(property="error", type="string", example=null, description="Mensagem de erro, se houver")
 * )
 * 
 * @OA\Schema(
 *     schema="ErrorResponse",
 *     @OA\Property(property="success", type="boolean", example=false, description="Status de sucesso da requisição"),
 *     @OA\Property(property="message", type="string", example="Ocorreu um erro", description="Mensagem de erro"),
 *     @OA\Property(property="error", type="string", example="Informações detalhadas do erro", description="Informações detalhadas do erro")
 * )
 * 
 * @OA\Schema(
 *     schema="HealthResponse",
 *     @OA\Property(property="success", type="boolean", example=true, description="Status de sucesso da requisição"),
 *     @OA\Property(property="message", type="string", example="Serviço está saudável", description="Mensagem de resposta"),
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(property="status", type="string", example="ok", description="Status de saúde"),
 *         @OA\Property(property="timestamp", type="string", format="date-time", example="2024-01-01T00:00:00.000000Z", description="Timestamp atual")
 *     )
 * )
 * 
 * @OA\Schema(
 *     schema="ExternalResponse",
 *     @OA\Property(property="success", type="boolean", example=true, description="Status de sucesso da requisição"),
 *     @OA\Property(property="message", type="string", example="Dados recuperados do microserviço externo", description="Mensagem de resposta"),
 *     @OA\Property(property="data", description="Dados do microserviço externo")
 * )
 * 
 * @OA\Get(
 *     path="/health",
 *     operationId="healthCheck",
 *     tags={"Health"},
 *     summary="Endpoint de verificação de saúde",
 *     description="Verifica se o serviço está rodando e saudável",
 *     @OA\Response(
 *         response=200,
 *         description="Serviço está saudável",
 *         @OA\JsonContent(ref="#/components/schemas/HealthResponse")
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Serviço não está saudável",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     )
 * )
 * 
 * @OA\Get(
 *     path="/external",
 *     operationId="externalService",
 *     tags={"External"},
 *     summary="Integração com microserviço externo",
 *     description="Recupera dados do microserviço Node.js externo",
 *     @OA\Response(
 *         response=200,
 *         description="Dados recuperados com sucesso do serviço externo",
 *         @OA\JsonContent(ref="#/components/schemas/ExternalResponse")
 *     ),
 *     @OA\Response(
 *         response=502,
 *         description="Erro do serviço externo",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Serviço externo retornou um erro"),
 *             @OA\Property(property="error", type="string", example="Código de status: 500")
 *         )
 *     ),
 *     @OA\Response(
 *         response=503,
 *         description="Serviço externo indisponível",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Falha ao conectar com o microserviço externo"),
 *             @OA\Property(property="error", type="string", example="Timeout de conexão")
 *         )
 *     )
 * )
 * 
 * @OA\Get(
 *     path="/users",
 *     operationId="getUsers",
 *     tags={"Users"},
 *     summary="Listar todos os usuários",
 *     description="Recupera uma lista de todos os usuários",
 *     @OA\Response(
 *         response=200,
 *         description="Usuários recuperados com sucesso",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Usuários recuperados com sucesso"),
 *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/User"))
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erro do servidor",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     )
 * )
 * 
 * @OA\Post(
 *     path="/users",
 *     operationId="createUser",
 *     tags={"Users"},
 *     summary="Criar um novo usuário",
 *     description="Cria um novo usuário e dispara evento Kafka",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/UserCreateRequest")
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Usuário criado com sucesso",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Usuário criado com sucesso"),
 *             @OA\Property(property="data", ref="#/components/schemas/User")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Erro de validação",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Falha na validação"),
 *             @OA\Property(property="errors", type="object", example={"email": {"O campo email é obrigatório."}})
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erro do servidor",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     )
 * )
 * 
 * @OA\Get(
 *     path="/users/{id}",
 *     operationId="getUser",
 *     tags={"Users"},
 *     summary="Obter um usuário específico",
 *     description="Recupera um usuário pelo seu ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID do usuário",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Usuário recuperado com sucesso",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Usuário recuperado com sucesso"),
 *             @OA\Property(property="data", ref="#/components/schemas/User")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Usuário não encontrado",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Usuário não encontrado")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erro do servidor",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     )
 * )
 * 
 * @OA\Put(
 *     path="/users/{id}",
 *     operationId="updateUser",
 *     tags={"Users"},
 *     summary="Atualizar um usuário",
 *     description="Atualiza as informações de um usuário existente",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID do usuário",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/UserUpdateRequest")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Usuário atualizado com sucesso",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Usuário atualizado com sucesso"),
 *             @OA\Property(property="data", ref="#/components/schemas/User")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Requisição inválida",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Nenhum dado fornecido para atualização")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Usuário não encontrado",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Usuário não encontrado")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Erro de validação",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Falha na validação"),
 *             @OA\Property(property="errors", type="object", example={"email": {"O email deve ser um endereço válido."}})
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erro do servidor",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     )
 * )
 * 
 * @OA\Delete(
 *     path="/users/{id}",
 *     operationId="deleteUser",
 *     tags={"Users"},
 *     summary="Excluir um usuário",
 *     description="Exclui um usuário pelo seu ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID do usuário",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Usuário excluído com sucesso",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Usuário excluído com sucesso"),
 *             @OA\Property(property="data", type="null", example=null)
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Usuário não encontrado",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Usuário não encontrado")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erro do servidor",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     )
 * )
 */
class SwaggerConfig
{
} 