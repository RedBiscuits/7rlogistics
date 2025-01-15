<?php

namespace App\Imports;

use App\Models\Area;
use App\Models\Destination;
use App\Models\Truck;
use App\Models\Company;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DestinationImport implements SkipsEmptyRows, ToCollection, WithCalculatedFormulas, WithHeadingRow, WithMultipleSheets
{
    private Company $company;

    /**
     * @param  array  $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    private $trucks;

    public function __construct($company, $trucks)
    {
        $this->company = $company;
        $this->trucks = $trucks;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            // Match the source city and area to their IDs
            $area_1 = $this->getArea($row['City']);
            $area_2 = $this->getArea($row['Area']);

            // Iterate through all truck columns (skipping the first two: 'City' and 'Area')
            foreach ($row as $column => $price) {
                if (in_array($column, ['City', 'Area', '#']) || is_numeric($column)) {
                    continue; // Skip 'City' and 'Area' columns
                }

                // Find the truck matching the column header
                $truck_id = $this->getTruckId("%{$column}%");
                if (!$truck_id) {
                    continue; // Skip if no matching truck is found
                }

                // Check if the destination already exists
                $destinationExists = $this->company->destinations()
                    ->where(function ($query) use ($area_1) {
                        $query->where('from_area', $area_1)
                            ->orWhere('to_area', $area_1);
                    })->where(function ($query) use ($area_2) {
                        $query->where('from_area', $area_2)
                            ->orWhere('to_area', $area_2);
                    })
                    ->where('truck_id', $truck_id)
                    ->first();

                if ($destinationExists) {
                    $destinationExists->update([
                        'price' => $price,
                    ]);
                    continue; // Skip if destination exists
                }

                // Create a new destination
                Destination::create([
                    'company_id' => $this->company->id,
                    'from_city' => $area_1->city_id,
                    'from_area' => $area_1->id,
                    'to_city' => $area_2->city_id,
                    'to_area' => $area_2->id,
                    'truck_id' => $truck_id,
                    'price' => $price,
                ]);
            }
        }
    }

    private function getArea($area)
    {
        $area = trim($area);
        return Area::where('name', 'like', "%{$area}%")->firstOrCreate(['name' => $area, 'city_id' => City::where('name', 'like', "%Cairo%")->firstOrFail()->id]);
    }

    private function getTruckId($truck)
    {
        $truck = trim($truck);
        return Truck::where('name', 'like', "%{$truck}%")->firstOrFail()->id;
    }

    public function sheets(): array
    {
        return [
            0 => $this,
        ];
    }
}
