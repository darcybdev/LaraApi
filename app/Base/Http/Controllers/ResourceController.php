<?php

namespace App\Base\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

abstract class ResourceController extends Controller
{
    abstract protected function getModel();

    public function index(Request $request)
    {
        $query = $this->buildIndexQuery($request);
        return $query->paginate();
    }

    protected function buildIndexQuery(Request $request)
    {
        $class = $this->getModel();
        $model = new $class;
        $query = $model->query();

        // Handle query params for search, filter and order by
        foreach ($request->all() as $key => $value) {
            switch ($key) {
                case 'orderby':
                    $direction = $request->get('orderdir') == 'desc' ? 'desc' : 'asc';
                    $query->orderBy($value, $direction);
                break;
                case 'trashed':
                    if ( ! empty($value)) {
                        if ($value == 'only') {
                            $query->onlyTrashed();
                        } else {
                            $query->withTrashed();
                        }
                    }
                break;
                default:
                    $operator = '=';
                    if (preg_match('~_like$~', $key)) {
                        $key = preg_replace('~(_like)$~', '', $key);
                        $operator = 'LIKE';
                        $value = '%' . $value . '%';
                    }
                    if (Schema::hasColumn($model->getTable(), $key)) {
                        $query->where($key, $operator, $value);
                    }
            }
        }
        return $query;
    }

    public function show($id)
    {
        $class = $this->getModel();
        return $class::findOrFail($id);
    }

    public function store(Request $request)
    {
        $class = $this->getModel();
        $model = new $class;
        $model->fill($request->all());
        $model->save();
        return $model;
    }

    public function update($id, Request $request)
    {
        $class = $this->getModel();
        $model = $class::findOrFail($id);
        $model->fill($request->all());
        $model->save();
        return $model;
    }

    public function destroy($id)
    {
        $class = $this->getModel();
        $model = $class::findOrFail($id);
        $model->delete();
        return ['success' => 'OK'];
    }
}
