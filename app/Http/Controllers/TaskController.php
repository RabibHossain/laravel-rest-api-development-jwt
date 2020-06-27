<?php

namespace App\Http\Controllers;

use App\HelperService\Helpers;
use App\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    private $loginAfterSignUp = true;
    private $helpers;
    private $created;
    private $createdCode;
    private $badRequest;
    private $badRequestCode;
    private $unauthorized;
    private $unauthorizedCode;
    private $accepted;
    private $acceptedCode;
    private $internalError;
    private $internalErrorCode;


    public function __construct(
        Helpers $helpers
    )
    {
        $this->created = 'Created';
        $this->createdCode = 201;
        $this->badRequest = 'Bad Request';
        $this->badRequestCode = 400;
        $this->unauthorized = 'Unauthorized';
        $this->unauthorizedCode = 401;
        $this->accepted = 'Accepted';
        $this->acceptedCode = 200;
        $this->internalError = 'Internal Error';
        $this->internalErrorCode = 500;
        $this->helpers = $helpers;
    }



    public function index()
    {
        $tasks = Task::get();
        $response = [
            'tasks' => $tasks
        ];
        return $this->helpers->response(true, $this->accepted, $response, null, $this->acceptedCode);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'title.required' => 'Title can not be empty.',
            'details.required' => 'Details can not be empty.'
        ];
 
        $validator = Validator::make($request->all(), [
            'title' => 'bail|required',
            'details' => 'required',
        ], $rules);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->helpers->response(false, $this->badRequest, null, $errors, $this->badRequestCode);
        }

        $task = new Task();
        $task->title = $request->title;
        $task->details = $request->details;
        

        if ($success = $task->save()) {
            return $this->helpers->response(true, $this->created, $task, null, $this->createdCode);
        } else {
            $details = "Task insertion failed!";
            return $this->helpers->response(false, $this->internalError, $details, null, $this->internalErrorCode);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (Task::where('id', $id)->exists()) {
            $task = Task::firstWhere('id', $id);
            $response = [
                'task' => $task
            ];
        } else {
            $response = 'Task not found';
        }

        return $this->helpers->response(true, $this->accepted, $response, null, $this->acceptedCode);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $response = null;

        $rules = [
            'title.required' => 'Title name can not be empty.',
            'details.required' => 'Details name can not be empty.'
        ];
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'details' => 'required'
        ], $rules);

        if ($validator->fails()) {
            return $this->helpers->response(false, $this->badRequest, null, null, $this->badRequestCode);
        }

        $title = $request->title;
        $details = $request->details;

        $taskId = Task::where('id', $id);

        if ($taskId->exists()) {
            $task = $taskId->update(['title' => $title, 'details' => $details]);
            if ($task) {
                
                $response = [
                    'id' => $id,
                    'title' => $title,
                    'details' => $details
                ];
            }
        } else {
            $response = 'Task not found';
        }

        return $this->helpers->response(true, $this->accepted, $response, null, $this->acceptedCode);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $taskId = Task::where('id', $id);
        if ($taskId->exists()) {
            $taskId->delete();
            $response = [
                'deleted' => $id
            ];
        } else {
            $response = "Task not found";
        }

        return $this->helpers->response(true, $this->accepted, $response, null, $this->acceptedCode);
    }
}
