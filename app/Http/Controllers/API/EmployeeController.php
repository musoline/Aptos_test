<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Employee;

class EmployeeController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $defaultPagination = 10;

        $employee = Employee::query()->with('company');

        // dd($employee);

        return $this->success($employee->orderBy('id','desc')->paginate($defaultPagination));
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
            'first_name' => 'required',
            'last_name' => 'required',
            'company_id' => 'required|exists:companies,id',
            'email' => 'email:rfc',
        ];


        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            $result = ['success'=>false, 'result'=>'','error'=>$validator->messages()];
            return $result;
        }else{
            $employee_data = [
                'first_name' => $request->get('first_name'),
                'last_name' => $request->get('last_name'),
                'company_id' => $request->get('company_id'),
                'email' => $request->get('email'),
                'phone' => $request->get('phone'),
            ];

            $employee = Employee::create($employee_data);

            return $this->success($employee);

        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->success(Order::query()->find($id));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Employee $employee)
    {
        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'company_id' => 'required|exists:companies,id',
            'email' => 'email:rfc',
        ];


        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            $result = ['success'=>false, 'result'=>'','error'=>$validator->messages()];
            return $result;
        }else{
            $employee_data = [
                'first_name' => $request->get('first_name'),
                'last_name' => $request->get('last_name'),
                'company_id' => $request->get('company_id'),
                'email' => $request->get('email'),
                'phone' => $request->get('phone'),
            ];

            $employee->update($employee_data);

            return $this->success($employee);

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();
    }
}
