<?php

namespace App\Services;

use App\Models\Shipment;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ShipmentService
{
    private $shipment;

    public function __construct(Shipment $shipment)
    {
        $this->shipment = $shipment;
    }

    public function downloadTcn()
    {
        $storagePath = storage_path('policies/tcn.xlsx');
        $newFilePath = storage_path('data/shipment_'.$this->shipment->id.'_tcn.xlsx');

        // Load the existing file
        $spreadsheet = IOFactory::load($storagePath);

        // ID
        $spreadsheet->getActiveSheet()->setCellValue('B3', '# '.str_pad($this->shipment->id, 3, '0', STR_PAD_LEFT));

        // Date
        $spreadsheet->getActiveSheet()->setCellValue('H3', now()->format('d/m/Y'));

        // Shipper
        $spreadsheet->getActiveSheet()->setCellValue('B7', $this->shipment->user->name);

        // Receiver
        $spreadsheet->getActiveSheet()->setCellValue(
            'B10',
            $this->shipment->toArea->name.",\n".
                $this->shipment->toCity->name.",\n".
                $this->shipment->receiver_address.",\n".
                'رقم الهاتف/'.$this->shipment->receiver_phone
        );

        // Driver
        $spreadsheet->getActiveSheet()->setCellValue('G10', $this->shipment->driver->name);
        $spreadsheet->getActiveSheet()->setCellValue('G11', $this->shipment->truck->plate_number);
        $spreadsheet->getActiveSheet()->setCellValue(
            'G12',
            'DOC#'.str_pad($this->shipment->driver->id, 3, '0', STR_PAD_LEFT)
        );

        // AWB and truck
        $spreadsheet->getActiveSheet()->setCellValue('D17', $this->shipment->awb);
        $spreadsheet->getActiveSheet()->setCellValue('D18', $this->shipment->truck->name);
        $spreadsheet->getActiveSheet()->setCellValue('D19', '1');

        // Pickup details
        $spreadsheet->getActiveSheet()->setCellValue('G17', $this->shipment->fromArea->name);
        $spreadsheet->getActiveSheet()->setCellValue('G18', $this->shipment->toArea->name);
        $spreadsheet->getActiveSheet()->setCellValue('G19', $this->shipment->pickup_date);

        // Notes
        $note = '';
        foreach ($this->shipment->packages as $package) {
            $note .= '- '.$package->note."\n";
        }
        $spreadsheet->getActiveSheet()->setCellValue('D21', $note);

        // Packages
        $index = 4;
        foreach ($this->shipment->packages as $package) {
            $spreadsheet->getActiveSheet()->setCellValue('B2'.$index, $package->quantity);
            $spreadsheet->getActiveSheet()->setCellValue('D2'.$index, $package->description);
            $spreadsheet->getActiveSheet()->setCellValue('G2'.$index, $package->weight);
            $spreadsheet->getActiveSheet()->setCellValue(
                'H2'.(string) $index,
                ' '.$package->length.' x '.$package->width.' x '.$package->height.' CM'
            );

            $index += 1;
        }

        // Write the new file
        $writer = new Xlsx($spreadsheet);
        $writer->save($newFilePath);

        // Return the file as a download response
        return $newFilePath;
    }

    public function downloadDn()
    {
        $storagePath = storage_path('policies/dn.xlsx');
        $newFilePath = storage_path('data/shipment_'.$this->shipment->id.'_dn.xlsx');

        // Load the existing file
        $spreadsheet = IOFactory::load($storagePath);

        // ID
        $spreadsheet->getActiveSheet()->setCellValue(
            'B8',
            '# '.str_pad($this->shipment->id, 3, '0', STR_PAD_LEFT)
        );

        // Date
        $spreadsheet->getActiveSheet()->setCellValue('H7', now()->format('d/m/Y'));

        // Shipper
        $spreadsheet->getActiveSheet()->setCellValue('G11', $this->shipment->user->name);

        // Receiver
        $spreadsheet->getActiveSheet()->setCellValue(
            'B11',
            $this->shipment->toArea->name.",\n".
                $this->shipment->toCity->name.",\n".
                $this->shipment->receiver_address.",\n".
                'رقم الهاتف/'.$this->shipment->receiver_phone
        );

        // Driver
        $spreadsheet->getActiveSheet()->setCellValue('G14', $this->shipment->driver->name);
        $spreadsheet->getActiveSheet()->setCellValue('G15', $this->shipment->truck->plate_number);
        $spreadsheet->getActiveSheet()->setCellValue(
            'G12',
            'DOC#'.str_pad($this->shipment->driver->id, 3, '0', STR_PAD_LEFT)
        );

        // AWB and truck
        $spreadsheet->getActiveSheet()->setCellValue('D14', '#'.str_pad($this->shipment->id, 3, '0', STR_PAD_LEFT));
        $spreadsheet->getActiveSheet()->setCellValue('D15', $this->shipment->awb);
        $spreadsheet->getActiveSheet()->setCellValue('D16', $this->shipment->truck->name);

        // Packages
        $index = 19;
        $spreadsheet->getActiveSheet()->setCellValue('B'.$index, 0);
        $spreadsheet->getActiveSheet()->setCellValue('D'.$index, 0);
        $spreadsheet->getActiveSheet()->setCellValue(
            'E'.$index,
            ' '
        );
        $spreadsheet->getActiveSheet()->setCellValue('F'.$index, ' ');
        $spreadsheet->getActiveSheet()->setCellValue('H'.$index, ' ');

        foreach ($this->shipment->packages as $package) {

            $spreadsheet->getActiveSheet()->setCellValue('B'.$index, $package->quantity);
            $spreadsheet->getActiveSheet()->setCellValue('D'.$index, $package->weight);
            $spreadsheet->getActiveSheet()->setCellValue(
                'E'.$index,
                ' '.$package->length.' x '.$package->width.' x '.$package->height.' CM'
            );
            $spreadsheet->getActiveSheet()->setCellValue('F'.$index, $package->description);
            $spreadsheet->getActiveSheet()->setCellValue('H'.$index, $index - 18);

            $index += 1;
        }

        // Write the new file
        $writer = new Xlsx($spreadsheet);
        $writer->save($newFilePath);

        // Return the file as a download response
        return $newFilePath;
    }

    public function downloadMis()
    {
        $storagePath = storage_path('policies/mis.xlsx');
        $newFilePath = storage_path('data/shipment_'.$this->shipment->id.'_mis.xlsx');

        // Load the existing file
        $spreadsheet = IOFactory::load($storagePath);

        // ID
        $spreadsheet->getActiveSheet()
            ->setCellValue(
                'B8',
                '# '.str_pad($this->shipment->id, 3, '0', STR_PAD_LEFT)
            )
            ->setCellValue(
                'B55',
                '# '.str_pad($this->shipment->id, 3, '0', STR_PAD_LEFT)
            );

        // Date
        $spreadsheet->getActiveSheet()
            ->setCellValue('H7', now()->format('d/m/Y'))
            ->setCellValue('H54', now()->format('d/m/Y'));

        // Shipper
        $spreadsheet->getActiveSheet()
            ->setCellValue('G11', $this->shipment->toArea->name)
            ->setCellValue('G60', $this->shipment->toArea->name);

        // Receiver
        $spreadsheet->getActiveSheet()
            ->setCellValue(
                'B11',
                $this->shipment->toArea->name.",\n".
                    $this->shipment->toCity->name.",\n".
                    $this->shipment->receiver_address.",\n".
                    'رقم الهاتف/'.$this->shipment->receiver_phone
            )
            ->setCellValue(
                'B60',
                $this->shipment->toArea->name.",\n".
                    $this->shipment->toCity->name.",\n".
                    $this->shipment->receiver_address.",\n".
                    'رقم الهاتف/'.$this->shipment->receiver_phone
            );

        // Driver
        $spreadsheet->getActiveSheet()
            ->setCellValue('G14', $this->shipment->driver->name)
            ->setCellValue('G63', $this->shipment->driver->name);
        $spreadsheet->getActiveSheet()
            ->setCellValue('G15', $this->shipment->truck->plate_number)
            ->setCellValue('G64', $this->shipment->truck->plate_number);
        $spreadsheet->getActiveSheet()
            ->setCellValue(
                'G12',
                'DOC#'.str_pad($this->shipment->driver->id, 3, '0', STR_PAD_LEFT)
            )->setCellValue(
                'G65',
                'DOC#'.str_pad($this->shipment->driver->id, 3, '0', STR_PAD_LEFT)
            );

        // AWB and truck
        $spreadsheet->getActiveSheet()
            ->setCellValue('D14', '# '.str_pad($this->shipment->id, 3, '0', STR_PAD_LEFT))
            ->setCellValue('D63', '# '.str_pad($this->shipment->id, 3, '0', STR_PAD_LEFT));
        $spreadsheet->getActiveSheet()
            ->setCellValue('D15', $this->shipment->awb)
            ->setCellValue('D64', $this->shipment->awb);
        $spreadsheet->getActiveSheet()
            ->setCellValue('D16', $this->shipment->truck->name)
            ->setCellValue('D65', $this->shipment->truck->name);

        // Packages
        $index = 19;

        $spreadsheet->getActiveSheet()
            ->setCellValue('B'.(string) $index, 0)
            ->setCellValue('B'.(string) ($index + 49), 0);
        $spreadsheet->getActiveSheet()
            ->setCellValue('D'.(string) $index, 0)
            ->setCellValue('D'.(string) ($index + 49), 0);
        $spreadsheet->getActiveSheet()
            ->setCellValue(
                'E'.(string) $index,
                ' '
            )->setCellValue(
                'E'.(string) ($index + 49),
                ' '
            );
        $spreadsheet->getActiveSheet()
            ->setCellValue('F'.(string) $index, ' ')
            ->setCellValue('F'.(string) ($index + 49), ' ');
        $spreadsheet->getActiveSheet()
            ->setCellValue('H'.(string) $index, ' ')
            ->setCellValue('H'.(string) ($index + 49), ' ');

        foreach ($this->shipment->packages as $package) {
            if ($index == 28) {
                break;
            }
            $spreadsheet->getActiveSheet()
                ->setCellValue('B'.(string) $index, $package->quantity)
                ->setCellValue('B'.(string) ($index + 49), $package->quantity);
            $spreadsheet->getActiveSheet()
                ->setCellValue('D'.(string) $index, $package->weight)
                ->setCellValue('D'.(string) ($index + 49), $package->weight);
            $spreadsheet->getActiveSheet()
                ->setCellValue(
                    'E'.(string) $index,
                    ' '.$package->length.' x '.$package->width.' x '.$package->height.' CM'
                )->setCellValue(
                    'E'.(string) ($index + 49),
                    ' '.$package->length.' x '.$package->width.' x '.$package->height.' CM'
                );
            $spreadsheet->getActiveSheet()
                ->setCellValue('F'.(string) $index, $package->description)
                ->setCellValue('F'.(string) ($index + 49), $package->description);
            $spreadsheet->getActiveSheet()
                ->setCellValue('H'.(string) $index, $index - 18)
                ->setCellValue('H'.(string) ($index + 49), $index - 18);

            $index += 1;
        }

        // Write the new file
        $writer = new Xlsx($spreadsheet);
        $writer->save($newFilePath);

        // Return the file as a download response
        return $newFilePath;
    }

    public function downloadInvoice()
    {
        $storagePath = storage_path('policies/invoice.xlsx');
        $newFilePath = storage_path('data/shipment_'.$this->shipment->id.'_invoice.xlsx');

        // Load the existing file
        $spreadsheet = IOFactory::load($storagePath);

        // ID
        $spreadsheet->getActiveSheet()->setCellValue(
            'B4',
            'NO. '.str_pad($this->shipment->id, 2, '0', STR_PAD_LEFT)
        );

        // Date
        $spreadsheet->getActiveSheet()->setCellValue('F3', now()->format('d/m/Y'));

        // Shipper
        $spreadsheet->getActiveSheet()->setCellValue('D6', $this->shipment->user->name);
        $spreadsheet->getActiveSheet()
            ->getStyle('D7')
            ->getNumberFormat()
            ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
        $spreadsheet->getActiveSheet()
            ->getStyle('D8')
            ->getNumberFormat()
            ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
        $spreadsheet->getActiveSheet()->setCellValue('D7', '# '.str_pad($this->shipment->id, 3, '0', STR_PAD_LEFT));
        $spreadsheet->getActiveSheet()->setCellValue('D8', '# '.str_pad($this->shipment->id, 3, '0', STR_PAD_LEFT));
        $spreadsheet->getActiveSheet()->setCellValue('D9', $this->shipment->awb);
        $spreadsheet->getActiveSheet()->setCellValue('D15', '01');
        $spreadsheet->getActiveSheet()->setCellValue('E15', 'Transportasion');
        $spreadsheet->getActiveSheet()->setCellValue('F15', 'EGP      '.$this->shipment->price);
        $spreadsheet->getActiveSheet()->setCellValue('F18', 'EGP      '.$this->shipment->price);

        // Write the new file
        $writer = new Xlsx($spreadsheet);
        $writer->save($newFilePath);

        // Return the file as a download response
        return $newFilePath;
    }
}
