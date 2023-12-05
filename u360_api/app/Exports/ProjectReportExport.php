<?php

namespace App\Exports;

use App\Models\Country;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\Models\Project;
use App\Models\Sdg;

class ProjectReportExport implements WithHeadings, FromQuery, WithMapping
{
    use Exportable;

    private $i = 1;
    protected $input = [];

    public function __construct(array $input)
    {
        $this->input  = $input;
    }

    /*prepare the Query for export with Filter  */
    public function query()
    {
        $projectLists =  Project::query()->with(
            'hasUser',
            'hasUser.hasUserDetails',
            'hasDonations',
            'hasProjectCategory',
            'hasVolunteer',
            'hasCountry'
        )->orderBy('id', 'DESC');

        if (isset($this->input['email']) && $this->input['email'] != '') {
            $email = $this->input['email'];
            $projectLists = $projectLists->whereHas('hasUser', function ($query) use ($email) {
                $query->where('email', 'LIKE', '%' . $email . '%');
            });
        }

        (isset($this->input['project_type']) && $this->input['project_type'] != '') ?
            $projectLists->where('project_type', $this->input['project_type']) : '';

        (isset($this->input['donation_type']) && $this->input['donation_type'] != '') ?
            $projectLists->where('project_donation_type', 'LIKE', '%' . $this->input['donation_type'] . '%') : '';

        (isset($this->input['category']) && $this->input['category'] != '') ?
            $projectLists->where('category_id', $this->input['category']) : '';

        (isset($this->input['status']) && $this->input['status'] != '') ?
            $projectLists->where('status', $this->input['status']) : '';

        (isset($this->input['created_at']) && $this->input['created_at'] != '') ?
            $projectLists->where('created_at', 'LIKE', '%' . $this->input['created_at'] . '%') : '';

        return $projectLists;
    }

    /* Mapping for result */
    public function map($inputMap): array
    {
        $arrayData = [];
        $arrayData[] = $this->i++;
        $arrayData[] = !empty($inputMap->hasUser) ? $inputMap->hasUser->name : '-';
        $arrayData[] = !empty($inputMap->hasUser) ? $inputMap->hasUser->email : '-';
        $arrayData[] = !empty($inputMap->hasUser->hasUserDetails) ?
            $inputMap->hasUser->hasUserDetails->contact_number : '-';
        $arrayData[]  = !empty($inputMap->title) ? $inputMap->title : '-';
        $arrayData[]  = !empty($inputMap->description) ? $inputMap->description : '-';
        $arrayData[]  = getProjectType($inputMap->project_type);
        $arrayData[]  = getProjectDonationType($inputMap->project_type);
        $arrayData[] = !empty($inputMap->hasProjectCategory) ?
            $inputMap->hasProjectCategory->name : '-';
        $arrayData[] = !empty($inputMap->amount) ?
            number_format($inputMap->amount, 2, '.', ',') : '-';
        $arrayData[]= $inputMap->hasDonations->count() > 0 ? number_format(
            $inputMap->hasDonations->sum('donation_amount'),
            2,
            '.',
            ','
        ) : 0;
        $arrayData['volunteer'] =  $inputMap->volunteer ?? 0;

        $arrayData['joined_volunteer'] =  ($inputMap->hasVolunteer->count() > 0)
            ? $inputMap->hasVolunteer->count('id')
            : 0;
        $arrayData[] = ($inputMap->hasDonations->count() > 0) ? number_format(
            $inputMap->hasDonations->sum('tips_amount'),
            2,
            '.',
            ','
        ) : 0;
        $arrayData[] = !empty($inputMap->city) ? $inputMap->city : '-';
        $arrayData[] =  !empty($inputMap->hasCountry) ? $inputMap->hasCountry->name : '-';
        $arrayData[] = getSdg($inputMap->sdg_ids);
        $arrayData[] = !empty($inputMap->status) ? $inputMap->status : "";
        $arrayData[] = !empty($inputMap->created_at) ? dateformat($inputMap->created_at) : "";
        return $arrayData;
    }


    /* for Handing In Excel File*/
    public function headings(): array
    {
        return [
            'Sr. No.',
            'User name',
            'User email',
            'User contact',
            'Project title',
            'Project description',
            'Project type',
            'Project donation type',
            'Category name',
            'Amount',
            'Donation Amount',
            'Volunteer',
            'Joined Volunteer',
            'Tips Amount',
            'City',
            'Countury',
            'SDG',
            'Status',
            'Created Date',
        ];
    }
}
