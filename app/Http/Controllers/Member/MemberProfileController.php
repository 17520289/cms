<?php

namespace App\Http\Controllers\Member;

use App\BankAccount;
use App\EmployeeDetails;
use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Requests\User\UpdateProfile;
use App\User;
use App\Skill;
use App\Country;
use App\Team;
use App\Designation;
use App\EmployeeSkill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class MemberProfileController extends MemberBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.profileSettings';
        $this->pageIcon = 'icon-user';
    }

    /**
     * Edit function to show profile settings including employee's bank account
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * 
     * edric - 25/8/2021
     */
    public function index()
    {
        $this->userDetail = auth()->user();
        $this->employeeDetail = EmployeeDetails::where('user_id', '=', $this->userDetail->id)->first();
        $this->bankAccount = BankAccount::where('user_id', '=', $this->userDetail->id)->first();
        $this->skills = Skill::all()->pluck('name')->toArray();
        $this->team = Team::find($this->employeeDetail->department_id);
        $this->designation = Designation::find($this->employeeDetail->designation_id);
        $this->countries = Country::all();
        if (!is_null($this->employeeDetail)) {
            $this->employeeDetail = $this->employeeDetail->withCustomFields();
            $this->fields = $this->employeeDetail->getCustomFieldGroupsWithFields()->fields;
        }

        return view('member.profile.edit', $this->data);
    }

    /**
     * Edit function to update profile settings including employee's bank account
     *
     * @param UpdateRequest $request
     * @param $id
     * @return array
     * 
     * edric - 25/8/2021
     */
    public function update(UpdateProfile $request, $id)
    {

        config(['filesystems.default' => 'local']);
        $user = User::withoutGlobalScope('active')->findOrFail($id);
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->gender = $request->input('gender');
        if ($request->password != '') {
            $user->password = Hash::make($request->input('password'));
        }
        $user->mobile = $request->input('mobile');
        $user->email_notifications = $request->email_notifications;

        if ($request->hasFile('image')) {
            Files::deleteFile($user->image, 'avatar');
            $user->image = Files::upload($request->image, 'avatar', 300);
        }

        $user->save();

        //update skill of member
        $tags = json_decode($request->tags);
        if (!empty($tags)) {
            EmployeeSkill::where('user_id', $user->id)->delete();
            foreach ($tags as $tag) {
                // check or store skills
                $skillData = Skill::firstOrCreate(['name' => strtolower($tag->value)]);

                // Store user skills
                $skill = new EmployeeSkill();
                $skill->user_id = $user->id;
                $skill->skill_id = $skillData->id;
                $skill->save();
            }
        }

        // // Edric - [#42] update detail of member to table employee_details
        $employee = EmployeeDetails::where('user_id', '=', $user->id)->first();
        if (empty($employee)) {
            $employee = new EmployeeDetails();
            $employee->user_id = $user->id;
        }
        
        $employee->date_of_birth = $request->date_of_birth;
        $employee->permanent_address = $request->permanent_address;
        $employee->slack_username = $request->slack_username;
        $employee->id_no = $request->id_no;
        $employee->issue_date = $request->issue_date;
        $employee->place_of_issue = $request->place_of_issue;
        $employee->temporary_address = $request->temporary_address;

        $employee->save();

        //update bank account 
        $bankAccount = BankAccount::where('user_id', '=', $user->id)->first();
        if (empty($bankAccount)) {
            $bankAccount = new BankAccount();
            $bankAccount->user_id = $user->id;
        }
        $bankAccount->account_owner = $request->account_owner;
        $bankAccount->account_number = $request->account_number;
        $bankAccount->bank_name = $request->bank_name;
        $bankAccount->branch = $request->branch;

        $bankAccount->save();
        session()->forget('user');


        $this->logUserActivity($user->id, __('messages.updatedProfile'));
        return Reply::redirect(route('member.profile.index'), __('messages.profileUpdated'));
    }

    public function updateOneSignalId(Request $request)
    {
        $user = User::find($this->user->id);
        $user->onesignal_player_id = $request->userId;
        $user->save();
    }

    public function changeLanguage(Request $request)
    {
        $setting = User::findOrFail($this->user->id);
        $setting->locale = $request->input('lang');
        $setting->save();
        session()->forget('user');
        return Reply::success('Language changed successfully.');
    }
}
