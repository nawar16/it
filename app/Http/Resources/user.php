<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class user extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return  [
            'Status' => 1,
            'Result' => [
                'name' => $this->name,
                'universityID' => $this->universityID,
                'password' => $this->password,
                'OtherCourses' => $this->OtherCourses,
                'Year' => $this->Year,
                'Class' => $this->Class,
                'IsAdmin' => $this->IsAdmin,
                'SeasonCourses' => getStudentCourses($this->Year),
                'OtherCourses' => getStudentOtherCourses($this->OtherCourses),
            ]
        ];
        //return parent::toArray($request);
    }
}
