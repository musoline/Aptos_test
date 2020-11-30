<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;

use App\Company;
use App\Employee;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['only'=>'index']);
        $this->middleware('auth',['only'=>'admin']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $companies = Company::all()->toArray();

 
        return view('welcome',['companies'=>$companies]);
    }


    public function companyDetailed(Company $company){
        $company = Company::with('employees')->find($company->id);

        return view('company-detailed',[
            'company'=>$company,
            'employees'=>$company->employees
            ]);

    }

    public function employeeDetailed(Employee $employee){
        
        $employee = Employee::with('company')->find($employee->id);
        

        return view('employee-detailed',[
            'employee'=>$employee,
            'company'=>$employee->company
            ]);

    }

    public function admin(){
        return view('admin.home');
    }
}
