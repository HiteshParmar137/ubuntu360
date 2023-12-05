<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\Models\User;

class UserReportExport implements WithHeadings, FromQuery, WithMapping
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
        $userLists = User::with(
            'hasUserDetails',
            'hasSdgsDetails.hasSdgs',
            'hasInterestDetails.hasCategory',
            'hasProjects',
            'hasDonations',
            'hasProjectFollows'
        )->orderBy('id', 'DESC');

        (isset($this->input['email']) && $this->input['email'] != '') ?
            $userLists->where('email', 'LIKE', '%' . $this->input['email'] . '%') : '';

        if (isset($this->input['position']) && $this->input['position'] != '') {
            $position = $this->input['position'];
            $userLists = $userLists->whereHas('hasUserDetails', function ($query) use ($position) {
                $query->where('position', 'LIKE', '%' . $position . '%');
            });
        }

        (isset($this->input['sponserType']) && $this->input['sponserType'] != '') ?
            $userLists->where('user_type', $this->input['sponserType']) : '';

        return $userLists;
    }

    /* Mapping for result */
    public function map($inputMap): array
    {
        $arrayData = [];
        $arrayData[] = $this->i++;

        $arrayData[] = !empty($inputMap->name) ? $inputMap->name : '-';
        $arrayData[] = !empty($inputMap->email) ? $inputMap->email : '-';
        $arrayData[] = !empty($inputMap->hasUserDetails) ?
            $inputMap->hasUserDetails->dob : '-';

        $arrayData[]  = !empty($inputMap->hasUserDetails) ? $inputMap->hasUserDetails->location : '-';
        $arrayData[]  = !empty($inputMap->hasUserDetails) ? $inputMap->hasUserDetails->contact_number : '-';

        if ($inputMap->user_type == '1') {
            $arrayData[] = 'Individual';
        } elseif ($inputMap->user_type == '2') {
            $arrayData[] = 'Corporate';
        }


        $arrayData[] = (!empty($inputMap->hasUserDetails)
            && $inputMap->hasUserDetails->corporation_name != null) ?
            $inputMap->hasUserDetails->corporation_name : '-';

        $arrayData[] = (!empty($inputMap->hasUserDetails)
            && $inputMap->hasUserDetails->industry != null) ?
            $inputMap->hasUserDetails->industry : '-';

        $arrayData[] = (!empty($inputMap->hasUserDetails)
            && $inputMap->hasUserDetails->city != null) ?
            $inputMap->hasUserDetails->city : '-';

        $arrayData[] = (!empty($inputMap->hasUserDetails)
            && $inputMap->hasUserDetails->country != null) ?
            $inputMap->hasUserDetails->country : '-';

        $arrayData[] = (!empty($inputMap->hasUserDetails)
            && $inputMap->hasUserDetails->contact_name != null) ?
            $inputMap->hasUserDetails->contact_name : '-';

        $arrayData[] = (!empty($inputMap->hasUserDetails)
            && $inputMap->hasUserDetails->position != null) ?
            $inputMap->hasUserDetails->position : '-';

        $sdgsName = '';
        foreach ($inputMap->hasSdgsDetails as $sdgs) {
            $sdgsName .= $sdgs->hasSdgs->name . ',';
        }
        $arrayData[] = rtrim($sdgsName, ',');


        if ((!empty($inputMap->hasUserDetails) && $inputMap->hasUserDetails->twitter != null)
            && $inputMap->hasUserDetails->twitter == '1'
        ) {
            $arrayData[] = config('constants.common_not_at_all');
        } elseif ((!empty($inputMap->hasUserDetails) && $inputMap->hasUserDetails->twitter != null)
            && $inputMap->hasUserDetails->twitter == '2'
        ) {
            $arrayData[] = config('constants.common_rarely');
        } elseif ((!empty($inputMap->hasUserDetails) && $inputMap->hasUserDetails->twitter != null)
            && $inputMap->hasUserDetails->twitter == '3'
        ) {
            $arrayData[] = config('constants.common_regular');
        } else {
            $arrayData[] = '-';
        }


        if ((!empty($inputMap->hasUserDetails) && $inputMap->hasUserDetails->facebook != null)
            && $inputMap->hasUserDetails->facebook == '1'
        ) {
            $arrayData[] = config('constants.common_not_at_all');
        } elseif ((!empty($inputMap->hasUserDetails) && $inputMap->hasUserDetails->facebook != null)
            && $inputMap->hasUserDetails->facebook == '2'
        ) {
            $arrayData[] = config('constants.common_rarely');
        } elseif ((!empty($inputMap->hasUserDetails) && $inputMap->hasUserDetails->facebook != null)
            && $inputMap->hasUserDetails->facebook == '3'
        ) {
            $arrayData[] = config('constants.common_regular');
        } else {
            $arrayData[] = '-';
        }

        $userArray['linkedin_id'] = !empty($inputMap->hasUserDetails)
            ? $inputMap->hasUserDetails->linkedin : '';

        if ((!empty($inputMap->hasUserDetails) && $inputMap->hasUserDetails->linkedin != null)
            && $inputMap->hasUserDetails->linkedin == '1'
        ) {
            $arrayData[] = config('constants.common_not_at_all');
        } elseif ((!empty($inputMap->hasUserDetails) && $inputMap->hasUserDetails->linkedin != null)
            && $inputMap->hasUserDetails->linkedin == '2'
        ) {
            $arrayData[] = config('constants.common_rarely');
        } elseif ((!empty($inputMap->hasUserDetails) && $inputMap->hasUserDetails->linkedin != null)
            && $inputMap->hasUserDetails->linkedin == '3'
        ) {
            $arrayData[] = config('constants.common_regular');
        } else {
            $arrayData[] = '-';
        }

        $hasInterestName = '';
        foreach ($inputMap->hasInterestDetails as $interests) {
            $hasInterestName .= $interests->hasCategory->name . ',';
        }
        $arrayData[] = rtrim($hasInterestName, ',');

        $arrayData[] = (!empty($inputMap->hasProjects)
            && $inputMap->hasProjects->count() > 0)
            ? $inputMap->hasProjects->count() : '0';


        $arrayData[] = (!empty($inputMap->hasDonations)
            && $inputMap->hasDonations->count() > 0)
            ?  number_format($inputMap->hasDonations->sum('donation_amount'), 2, '.', ',')
            : '0';

        $arrayData[] = (!empty($inputMap->hasDonations)
            && $inputMap->hasDonations->count() > 0)
            ?  number_format($inputMap->hasDonations->sum('tips_amount'), 2, '.', ',')
            : '0';

        $arrayData[] = (!empty($inputMap->hasProjectFollows)
            && $inputMap->hasProjectFollows->count() > 0)
            ? $inputMap->hasProjectFollows->count() : '0';
        $arrayData['signup_completed'] = $inputMap->is_signup_completed == 1 ? 'Completed' : 'Not Completed';
        return $arrayData;
    }


    /* for Handing In Excel File*/
    public function headings(): array
    {
        return [
            'Sr. No.',
            'Name',
            'Email',
            'DOB',
            'Location',
            'Contact No.',
            'Sponsor Type',
            'Corporation Name',
            'Industry name',
            'City',
            'Countury',
            'Contact name',
            'Position',
            'SDG',
            'Twitter',
            'Facebook',
            'LinkedIn',
            'Category',
            'Total Project',
            'Total Donation',
            'Tips Amount',
            'Total Project Followed',
            'Signup Completed'
        ];
    }
}
