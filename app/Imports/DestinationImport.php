<?php

namespace App\Imports;

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

        foreach ($rows as $row) {

            $from_city = $this->getCity($this->company, $row['From City']);
            $to_city = $this->getCity($this->company, $row['To City']);
            $from_area = $this->getAreaId($from_city, $row['From Area']);
            $to_area = $this->getAreaId($to_city, $row['To Area']);

            foreach ($this->trucks as $truck) {
                $truck_id = $this->getTruckId($truck);

                // Check if the destination exists
                $destinationExists = $this->company->destinations()
                    ->where('from_city', $from_city->id)
                    ->where('from_area', $from_area)
                    ->where('to_city', $to_city->id)
                    ->where('to_area', $to_area)
                    ->where('truck_id', $truck_id)
                    ->exists();

                if ($destinationExists) {
                    continue; // Skip if destination exists
                }

                // Create a new destination
                Destination::create([
                    'company_id' => $this->company->id,
                    'from_city' => $from_city->id,
                    'from_area' => $from_area,
                    'to_city' => $to_city->id,
                    'to_area' => $to_area,
                    'truck_id' => $truck_id,
                    'price' => $row[$truck],
                ]);
            }
        }
    }

    private function getCity($company, $city)
    {

        return $company->cities()->where('name', $city)->firstOrCreate(['name' => $city]);
    }

    private function getAreaId($city, $area)
    {

        return $city->areas()->where('name', $area)->firstOrCreate(['name' => $area])->id;
    }

    private function getTruckId($truck)
    {

        Log::error($truck);

        return Truck::where('name', $truck)->firstOrFail()->id;
    }

    public function sheets(): array
    {
        return [
            0 => $this,
        ];
    }
}
