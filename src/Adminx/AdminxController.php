<?php

/*
 * This file is part of Adminx.
 *
 * Copyright 2020-2021 parsa shahmaleki <parsampsh@gmail.com>
 *
 * Adminx project is Licensed Under MIT.
 * For more information, please see the LICENSE file.
 */

namespace Adminx;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Adminx\Models\Log;

/**
 * The Adminx Controller
 */
class AdminxController extends BaseController
{
    private $core;

    /**
     * Finds model config by slug
     *
     * @param string $slug
     * @return mixed|null
     */
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

    /**
     * Checks user is authorized
     */
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

    /**
     * The index page
     *
     * @param Request $request
     */
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

    /**
     * Shows a page
     *
     * @param Request $request
     * @param string $slug
     */
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

    /**
     * The model index page and datatable
     *
     * @param Request $request
     * @param string $slug
     */
    public function model_index(Request $request, string $slug)
    {
        $this->run_middleware();
        $model_config = $this->find_model_by_slug($slug);

        // check is a action clicked
        if ($request->method() == 'POST') {
            $action_name = $request->post('action');

            // check action exists
            if (!isset($model_config['actions'][$action_name])) {
                abort(400);
            }

            $row = $model_config['model']::find($request->post('id'));
            
            if ($row === null) {
                abort(400);
            }

            // check action middleware
            if (isset($model_config['actions'][$action_name]['middleware'])) {
                if (!call_user_func_array($model_config['actions'][$action_name]['middleware'], [auth()->user(), $row])) {
                    abort(403);
                }
            }

            // run the action
            return call_user_func_array($model_config['actions'][$action_name]['run'], [$row]);
        }

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

    /**
     * Model DELETE request handler
     *
     * @param Request $request
     * @param string $slug
     */
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

        // add the log
        Log::add_log($model_config['slug'], $row->id, auth()->id(), 'delete', 'Item ' . $row->id . ' in table ' . $model_config['slug'] . ' was deleted by user ' . auth()->id());

        // delete the item
        $row->delete();

        return redirect($request->fullUrl());
    }

    /**
     * Model Update page
     *
     * @param Request $request
     * @param string $slug
     * @param string $id
     */
    public function model_update(Request $request, string $slug, string $id)
    {
        return $this->model_create($request, $slug, true, $id);
    }

    /**
     * Model create page
     *
     * @param Request $request
     * @param string $slug
     * @param bool $is_update
     * @param null $update_id
     */
    public function model_create(Request $request, string $slug, bool $is_update=false, $update_id=null)
    {
        $this->run_middleware();
        $model_config = $this->find_model_by_slug($slug);

        if(!$this->core->check_super_user(auth()->user())) {
            // has user create/update permission
            $permission = '.create';
            if ($is_update) {
                $permission = '.update';
            }

            if (!\Adminx\Access::user_has_permission(auth()->user(), $slug . $permission)) {
                abort(403);
            }

            // check create_middleware/update_middleware
            if ($is_update) {
                $row = $model_config['model']::find($update_id);

                if ($row === null) {
                    abort(404);
                }

                $middleware_result = call_user_func_array($model_config['update_middleware'], [auth()->user(), $row]);
            } else {
                $middleware_result = call_user_func_array($model_config['create_middleware'], [auth()->user()]);
            }

            if (!$middleware_result) {
                abort(403);
            }
        }

        if ($is_update) {
            if (!isset($row)) {
                $row = $model_config['model']::find($update_id);

                if ($row === null) {
                    abort(404);
                }
            }
        }

        // load model fields
        $columns = $this->core->get_model_columns($model_config, false);
        $new_columns = [];
        $table_name = (new $model_config['model'])->getTable();

        // filter the columns
        foreach($columns as $column){
            if($column !== 'id') {
                if ($is_update) {
                    $is_show = !in_array($column, $model_config['readonly_fields']) || in_array($column, $model_config['only_editable_fields']);
                } else {
                    $is_show = !in_array($column, $model_config['readonly_fields']) || in_array($column, $model_config['only_addable_fields']);
                }

                if ($is_show) {
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
        if($request->method() === 'POST' || $request->method() === 'PUT') {
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
                    $r = $model_config['foreign_keys'][$column['name']]['model']::find($id);
                    $foreign_rows[$column['name']] = $r;

                    if (!$column['is_null']) {
                        if ($foreign_rows[$column['name']] === null) {
                            abort(400);
                        } else {
                            $tmp = call_user_func_array($model_config['foreign_keys'][$column['name']]['list'], [])->where('id', $foreign_rows[$column['name']]->id)->first();
                            if ($tmp === null) {
                                abort(400);
                            }
                        }
                    }
                }
            }

            if (!$is_update) {
                $row = new $model_config['model'];
            }

            if ($is_update) {
                $old_row = clone $row;
            }

            foreach($columns as $column) {
                if (isset($model_config['foreign_keys'][$column['name']])) {
                    if ($foreign_rows[$column['name']] !== null) {
                        $row->{$column['name']} = $foreign_rows[$column['name']]->id;
                    }
                } else {
                    $row->{$column['name']} = $request->post($column['name']);
                }
            }

            if ($is_update) {
                $row = call_user_func_array($model_config['filter_update_data'], [$old_row, $row]);
            } else {
                $row = call_user_func_array($model_config['filter_create_data'], [$row]);
            }

            $row->save();

            // handle n2n relations
            $i = 0;
            foreach ($model_config['n2n'] as $item) {
                $list = $item['list']();
                $current_selected = [];

                if ($is_update) {
                    $current_selected = $item['pivot']::where($item['pivot_keys'][0], $row->id)->get();
                    $current_selected_ids = [];
                    foreach ($current_selected as $cs) {
                        array_push($current_selected_ids, $cs->{$item['pivot_keys'][1]});
                    }
                    $current_selected = $current_selected_ids;
                }

                $i++;

                $input = $request->post('n2n' . $i);

                if (!is_array($input)) {
                    $input = [];
                }

                // delete old items
                if ($is_update) {
                    $item['pivot']::where($item['pivot_keys'][0], $row->id)->delete();
                }

                // set new items
                foreach ($input as $input_item) {
                    $tmp_list = clone $list;
                    $input_row = $tmp_list->where('id', $input_item)->first();
                    if ($input_row !== null) {
                        $pivot_row = new $item['pivot'];
                        $pivot_row->{$item['pivot_keys'][0]} = $row->id;
                        $pivot_row->{$item['pivot_keys'][1]} = $input_row->id;
                        $pivot_row->save();
                    }
                }
            }

            if ($is_update) {
                $next_step = $model_config['after_update_go_to'];
            } else {
                $next_step = $model_config['after_create_go_to'];
            }

            // add the log
            if ($is_update) {
                Log::add_log($model_config['slug'], $row->id, auth()->id(), 'update', 'Item ' . $row->id . ' in table ' . $model_config['slug'] . ' was updated by user ' . auth()->id());
            } else {
                Log::add_log($model_config['slug'], $row->id, auth()->id(), 'create', 'Item ' . $row->id . ' in table ' . $model_config['slug'] . ' was created by user ' . auth()->id());
            }

            if ($next_step === 'create') {
                return redirect($request->fullUrl());
            } else if ($next_step === 'table') {
                if ($request->get('back')) {
                    return redirect($request->get('back'));
                } else {
                    return redirect($this->core->url('/model/' . $model_config['slug']));
                }
            } else if ($next_step === 'update') {
                return redirect($this->core->url('/model/' . $model_config['slug'] . '/update/' . $row->id));
            }
        }

        if ($is_update) {
            if (!isset($row)) {
                $row = $model_config['model']::find($update_id);
            }
            if ($row === null) {
                abort(404);
            }
            return view('adminx.create', [
                'core' => $this->core,
                'model_config' => $model_config,
                'columns' => $columns,
                'is_update' => true,
                'row' => $row,
            ]);
        } else {
            return view('adminx.create', [
                'core' => $this->core,
                'model_config' => $model_config,
                'columns' => $columns,
                'is_update' => false,
            ]);
        }
    }
}
