<?php namespace VacStatus\Http\Requests;

use Illuminate\Validation\Validator;

use VacStatus\Http\Requests\Request;

class UserListRequest extends Request
{
	public function authorize()
	{
		return true;
	}

	public function rules()
	{
		return [
			'title' => 'required|max:15',
			'privacy' => 'required|numeric'
		];
	}

	protected function formatErrors(Validator $validator)
	{
	    return ['error' => $validator->errors()->all()[0]];
	}

}
