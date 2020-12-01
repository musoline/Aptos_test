<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

use App\Company;

class CompanyController extends Controller
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

        $companies = Company::query();



        return $this->success($companies->orderBy('id','desc')->with(['employees'])->paginate($defaultPagination));
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
            'name' => 'required',
            'email' => 'email:rfc',
            'logo' => 'file|mimes:jpeg,jpg,png,gif|required|max:2048|dimensions:min-width=100,min-height=100',
            'website'=> 'url'
        ];


        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            $result = ['success'=>false, 'result'=>'','error'=>$validator->messages()];
            return $result;
        }else{
            $file = $request->file("logo");
            $name = $file->getClientOriginalName();
            $ext = $file->getClientOriginalExtension();
            $tmpName = $name;
            $fileName = $file->storeAs('public', $tmpName, ['disk'=>'local']);
            $url = URL::asset('storage/'.$fileName);

            $company_data = [
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'logo' => $tmpName,
                'website' => $request->get('website'),
            ];

            $company = Company::create($company_data);
            
            
            return $this->success($company);
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
        //
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
    public function update(Request $request, Company $company)
    {


        $rules = [
            'name' => 'required',
            'email' => 'email:rfc',
            // 'logo' => 'file|mimes:jpeg,jpg,png,gif|required|max:2048|dimensions:min-width=100,min-height=100',
            'website'=> 'url'
        ];

        $validator = Validator::make($request->all(), $rules);

        $all = $request->all();


        if($validator->fails()){
            $result = ['success'=>false, 'result'=>'','error'=>$validator->messages()];
            return $result;
        }else{
            if($request->hasFile('logo')){
                $file = $request->file("logo");
                $name = $file->getClientOriginalName();
                $ext = $file->getClientOriginalExtension();
                $tmpName = $name;
                $fileName = $file->storeAs('public', $tmpName, ['disk'=>'local']);
                $url = URL::asset('storage/'.$fileName);
            }else{
                unset($all['logo']);
            }


            $company->update($all);
            
            
            return $this->success($company);
        }








        $all = $request->all();

        $all["password"] = Hash::make($all["password"]);
        
        $company->update($all);
    }




    public function showAll(){
        $companies = Company::all("id","name");
        return $this->success($companies);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Company $company)
    {
        $company->delete();
    }
}
