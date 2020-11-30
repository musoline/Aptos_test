<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name',
        'email',
        'logo',
        'website'
    ];

    protected $appends = ["total_employee"];

    public function employees(){
        return $this->hasMany('App\Employee');
    }

    public function getTotalEmployeeAttribute(){
        return $this->employees->count();
    }



}
