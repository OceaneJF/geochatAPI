<?php
// src/Service/AddressAPIService.php
namespace App\Services;


use GuzzleHttp\Client;
use League\Csv\Reader;

class AddressAPIService
{
    public const base_uri = 'https://api-adresse.data.gouv.fr/';

    protected function getClient(): Client
    {
        return new Client(
            ['base_uri' => self::base_uri, 'verify' => false],

        );
    }

    public function getLngLat(string $address): ?array
    {
        // https://api-adresse.data.gouv.fr/search/?q=9 chemin de la croix jubile
        $response = $this->getClient()->request('GET', 'search', ['query' => ['q' => $address]]);

        $data = json_decode($response->getBody()->getContents(), true);

        if (count($data["features"]) > 0) {
            return $data["features"][0]["geometry"]["coordinates"];
        }
        return null;
    }

    public function getAddresses(array $lnglat): array
    {

        // curl -X POST -F data=@path/to/file.csv https://api-adresse.data.gouv.fr/reverse/csv/
        $csv = "lat,lon";
        foreach ($lnglat as list($lng, $lat)) {
            $csv .= "\n$lng,$lat";
        }

        $result = $this->getClient()->request('POST', 'reverse/csv/', [
            'multipart' => [
                [
                    'name' => 'data',
                    'contents' => $csv,
                    'headers' => ['Content-type' => 'text/csv'],
                    'filename' => 'data.csv'
                ]

            ]
        ]);


        $data = $result->getBody()->getContents();

        $csv = Reader::createFromString($data);

        $csv->setHeaderOffset(0);

        $result = [];
        foreach ($csv->getRecords() as $entry) {
            $result[] = $entry["result_label"];
        }

        // dd($result);
        return $result;
    }
}
