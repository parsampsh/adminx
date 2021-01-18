<?php

/*
 * This file is part of Adminx.
 *
 * Copyright 2020-2021 parsa shahmaleki <parsampsh@gmail.com>
 *
 * Adminx project is Licensed Under MIT.
 * For more information, please see the LICENSE file.
 */

namespace Adminx\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * The Adminx Controller
 */
class AdminxController extends BaseController
{
    private $core;

    private function find_model_by_slug(string $slug)
    {
        $model_config = null;
        foreach($this->core->get_menu() as $item)
        {
            if($item['type'] === 'model'){
                if($item['config']['slug'] === $slug){
                    $model_config = $item['config'];
                }
            }
        }

        if($model_config === null){
            abort(404);
        }

        if(!$this->core->check_super_user(auth()->user())) {
            $middleware_result = call_user_func_array($model_config['middleware'], [auth()->user()]);
            if ($middleware_result !== true) {
                abort(403);
            }
        }

        return $model_config;
    }

    public function __construct()
    {
        $this->core = \Adminx\Core::$core;
    }

    public function run_middleware()
    {
        if ($this->core->check_super_user(auth()->user())){
            return;
        }
        $result = $this->core->run_middleware();
        if ($result === false) {
            abort(403);
        }
    }

    public function index(Request $request)
    {
        $this->run_middleware();

        $action = null;
        $page_title = null;
        foreach ($this->core->get_menu() as $item) {
            if ($item['type'] === 'page') {
                if ($item['slug'] === '.') {
                    $action = $item['action'];
                    $page_title = $item['title'];
                }
            }
        }

        if (!is_callable($action)) {
            // use adminx default index page
            return view('adminx.index', ['core' => $this->core]);
        }

        // run the page action
        $output = call_user_func_array($action, [$request]);

        // return the view
        return view('adminx.page', ['output' => $output, 'core' => $this->core, 'page_title' => $page_title]);
    }

    public function show_page(Request $request, string $slug)
    {
        $this->run_middleware();
        // check page exists
        $action = null;
        $page_title = null;
        foreach ($this->core->get_menu() as $item) {
            if ($item['type'] === 'page') {
                if ($item['slug'] === $slug) {
                    $action = $item['action'];
                    $page_title = $item['title'];
                }
            }
        }

        if (!is_callable($action)) {
            abort(404);
        }

        // run the page action
        $output = call_user_func_array($action, [$request]);

        // return the view
        return view('adminx.page', ['output' => $output, 'core' => $this->core, 'page_title' => $page_title]);
    }

    public function model_index(Request $request, string $slug)
    {
        $this->run_middleware();
        $model_config = $this->find_model_by_slug($slug);

        // load model table rows
        $rows = $model_config['model']::query();
        $rows = $model_config['filter_data']($rows);

        // handle search filter
        if($request->get('search')){
            if(is_callable($model_config['search'])){
                $rows = call_user_func_array($model_config['search'], [$rows, $request->get('search')]);
            }
        }

        // paginate and get the final data
        $rows = $rows->paginate($model_config['per_page']);

        return view('adminx.model', ['core' => $this->core, 'model_config' => $model_config, 'rows' => $rows]);
    }

    public function model_delete(Request $request, string $slug)
    {
        $this->run_middleware();
        $model_config = $this->find_model_by_slug($slug);

        // has user delete permission
        if(!$this->core->check_super_user(auth()->user())) {
            if (!\Adminx\Access::user_has_permission(auth()->user(), $slug . '.delete')) {
                abort(403);
            }
        }

        // load the row
        $row = $model_config['model']::find($request->post('delete'));

        if(!$row){
            abort(404);
        }

        // check the delete middleware
        if(!$this->core->check_super_user(auth()->user())) {
            if (!call_user_func_array($model_config['delete_middleware'], [auth()->user(), $row])) {
                abort(403);
            }
        }

        // delete the item
        $row->delete();

        return redirect($request->fullUrl());
    }

    public function model_create(Request $request, string $slug)
    {
        $this->run_middleware();
        $model_config = $this->find_model_by_slug($slug);

        if(!$this->core->check_super_user(auth()->user())) {
            // has user create permission
            if (!\Adminx\Access::user_has_permission(auth()->user(), $slug . '.create')) {
                abort(403);
            }

            // check create_middleware
            if (call_user_func_array($model_config['create_middleware'], [auth()->user()]) !== true) {
                abort(403);
            }
        }

        // load model fields
        $columns = $this->core->get_model_columns($model_config, false);
        $new_columns = [];
        $table_name = (new $model_config['model'])->getTable();

        // filter the columns
        foreach($columns as $column){
            if($column !== 'id') {
                if (!in_array($column, $model_config['readonly_fields']) || in_array($column, $model_config['only_addable_fields'])) {
                    $column_data = DB::connection()->getDoctrineColumn($table_name, $column);
                    $type = $column_data->getType()->getName();
                    $maxlength = $column_data->getLength();
                    $default = $column_data->getDefault();
                    if ($maxlength === null && ($type === 'text' or $type === 'string')) {
                        $maxlength = 255;
                    }
                    $is_null = !$column_data->getNotnull();
                    $comment = '';
                    if (isset($model_config['fields_comments'][$column])) {
                        $comment = $model_config['fields_comments'][$column];
                    } else {
                        $comment = $column_data->getComment();
                    }
                    array_push($new_columns, [
                        'name' => $column,
                        'type' => $type,
                        'max' => $maxlength,
                        'is_null' => $is_null,
                        'default' => $default,
                        'comment' => $comment,
                    ]);
                }
            }
        }

        $columns = $new_columns;

        // handle post action
        if($request->method() === 'POST') {
            // validate data
            $validate_options = [];

            foreach($columns as $column) {
                $validate_options[$column['name']] = '';
                if (!$column['is_null']) {
                    $validate_options[$column['name']] .= 'required|';
                }
                if ($column['max'] !== null) {
                    $validate_options[$column['name']] .= 'max:' . $column['max'] . '|';
                }
            }

            $request->validate($validate_options);

            // check foreign keys
            $foreign_rows = [];
            foreach($columns as $column) {
                if (isset($model_config['foreign_keys'][$column['name']])) {
                    $id = $request->post($column['name']);
                    $row = $model_config['foreign_keys'][$column['name']]['model']::find($id);
                    $foreign_rows[$column['name']] = $row;

                    if (!$column['is_null']) {
                        if ($foreign_rows[$column['name']] === null) {
                            abort(400);
                        }
                    }
                }
            }

            $row = new $model_config['model'];

            foreach($columns as $column) {
                if (isset($model_config['foreign_keys'][$column['name']])) {
                    if ($foreign_rows[$column['name']] !== null) {
                        $row->{$column['name']} = $foreign_rows[$column['name']]->id;
                    }
                } else {
                    $row->{$column['name']} = $request->post($column['name']);
                }
            }

            $row = $model_config['filter_create_data']($row);

            $row->save();

            if ($model_config['after_create_go_to'] === 'create') {
                return redirect($request->fullUrl());
            } else if ($model_config['after_create_go_to'] === 'table' || $model_config['after_create_go_to'] === 'update') {
                // TODO : handle `update`
                if ($request->get('back')) {
                    return redirect($request->get('back'));
                } else {
                    return redirect($this->core->url('/model/' . $model_config['slug']));
                }
            }
        }

        return view('adminx.create', [
            'core' => $this->core,
            'model_config' => $model_config,
            'columns' => $columns
        ]);
    }
}
