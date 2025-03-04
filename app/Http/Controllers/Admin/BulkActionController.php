<?php

/**
 * @author Claudio Campos
 * @link https://www.sigasmart.com.br
 * @package App\Http\Controllers
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BulkActionRequest;
use Illuminate\Support\Facades\DB;

class BulkActionController extends Controller
{
    public function __invoke(BulkActionRequest $request)
    {
        $data = $request->validated();
        $action = data_get($data, 'action');
        $model = data_get($data, 'model');
        $items = data_get($data, 'items', []);

        $query = app($model)->query();

        // Se for seleção total, aplicar filtros
        if ($action === 'select-all') {
            if (!empty($filters)) {
                foreach ($filters as $field => $value) {
                    $query->where($field, $value);
                }
            }
            $items = $query->pluck('id')->toArray();
        }

        try {
            DB::beginTransaction();

            $count = count($items);

            // Executar ação baseada no tipo
            switch ($action) {
                case 'delete':
                    $query->whereIn('id', $items)->delete();
                    $message = sprintf('%d registro(s) excluído(s) com sucesso', $count);
                    break;

                case 'export':
                    // Implementar lógica de exportação
                    $message = sprintf('Exportação de %d registro(s) iniciada', $count);
                    break;

                case 'select-all':
                    $message = sprintf('%d registro(s) selecionado(s)', $count);
                    break;

                default:
                    $message = sprintf('Ação "%s" executada em %d registro(s)', $action, $count);
                    break;
            }

            DB::commit();

            return  redirect()->back()
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Erro ao executar ação em massa: ' . $e->getMessage());
        }
    }
}
