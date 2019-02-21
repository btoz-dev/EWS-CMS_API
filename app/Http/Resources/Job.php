<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class Job extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'JobCode' => $this->JobCode,
            'CatID' => $this->CatID,
            'SubCatID' => $this->SubCatID,
            'Descriptions' => $this->Description,
        ];
    }
}
