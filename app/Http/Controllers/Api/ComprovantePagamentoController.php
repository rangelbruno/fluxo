<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreComprovantePagamentoRequest;
use App\Http\Resources\ComprovantePagamentoResource;
use App\Models\ComprovantePagamento;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;

class ComprovantePagamentoController extends Controller
{
    /**
     * Lista todos os comprovantes com paginação e filtros.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = ComprovantePagamento::query();

        // Filtros opcionais
        if ($request->has('status')) {
            $query->status($request->status);
        }

        if ($request->has('forma_pagamento')) {
            $query->formaPagamento($request->forma_pagamento);
        }

        if ($request->has('data_inicio') && $request->has('data_fim')) {
            $query->periodo($request->data_inicio, $request->data_fim);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('pagador', 'ilike', "%{$search}%")
                    ->orWhere('favorecido', 'ilike', "%{$search}%")
                    ->orWhere('descricao', 'ilike', "%{$search}%")
                    ->orWhere('identificador_transacao', 'ilike', "%{$search}%");
            });
        }

        $comprovantes = $query->orderByDesc('created_at')->paginate(15);

        return ComprovantePagamentoResource::collection($comprovantes);
    }

    /**
     * Armazena um novo comprovante (endpoint principal para n8n).
     */
    public function store(StoreComprovantePagamentoRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            // Se não vier origem, assume n8n
            if (! isset($data['origem'])) {
                $data['origem'] = 'n8n';
            }

            // Se não vier status, assume processado
            if (! isset($data['status'])) {
                $data['status'] = 'processado';
            }

            $comprovante = ComprovantePagamento::create($data);

            Log::info('Comprovante criado via API', [
                'id' => $comprovante->id,
                'origem' => $data['origem'],
                'workflow_execution_id' => $data['workflow_execution_id'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Comprovante criado com sucesso',
                'data' => new ComprovantePagamentoResource($comprovante),
            ], 201);
        } catch (\Exception $e) {
            Log::error('Erro ao criar comprovante via API', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar comprovante',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Exibe um comprovante específico.
     */
    public function show(ComprovantePagamento $comprovante): ComprovantePagamentoResource
    {
        return new ComprovantePagamentoResource($comprovante);
    }

    /**
     * Atualiza um comprovante existente.
     */
    public function update(StoreComprovantePagamentoRequest $request, ComprovantePagamento $comprovante): JsonResponse
    {
        try {
            $comprovante->update($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Comprovante atualizado com sucesso',
                'data' => new ComprovantePagamentoResource($comprovante),
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar comprovante', [
                'id' => $comprovante->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar comprovante',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove um comprovante (soft delete).
     */
    public function destroy(ComprovantePagamento $comprovante): JsonResponse
    {
        try {
            $comprovante->delete();

            return response()->json([
                'success' => true,
                'message' => 'Comprovante removido com sucesso',
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao remover comprovante', [
                'id' => $comprovante->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover comprovante',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Endpoint de verificação de saúde para n8n.
     */
    public function health(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'service' => 'comprovantes-api',
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
