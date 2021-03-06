<?php

namespace App\Http\Requests\User;
use App\EmployeeDetails;
use App\Http\Requests\CoreRequest;

use Illuminate\Foundation\Http\FormRequest;


class UpdateProfile extends CoreRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {   
      
        $detailID = EmployeeDetails::where('user_id', auth()->user()->id)->first();
        return [
            'email' => 'required|unique:users,email,'.$this->route('profile'),
            'name'  => 'required',
            'password'  => 'nullable|min:8',
            'image' => 'image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            'slack_username' => 'nullable|unique:employee_details,slack_username,'.$detailID->id,
            'mobile' => 'nullable|min:9',
            'id_no' => 'nullable|min:9',
            'account_number' => 'nullable|numeric',
        ];
    }

    public function messages() {
        return [
            'name' => 'alpha',
          'image.image' => 'Profile picture should be an image'
        ];
    }
}
