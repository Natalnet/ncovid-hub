<?php

namespace App\Jobs;

use App\Models\DataSource;
use Elasticsearch\ClientBuilder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FetchData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $dataSource;
    public $elasticClient;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(DataSource $dataSource)
    {
        $this->dataSource = $dataSource;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $this->elasticClient = ClientBuilder::create()
//                ->setHosts(['172.105.150.137'])
            ->setElasticCloudId('N-Covid:dXMtZWFzdC0xLmF3cy5mb3VuZC5pbyRiMTg3ZGZhZTg5Nzc0Yjg0YWUwZmU4ZTM5ZTkzODk3ZSRmN2UxYzFlMmZlYmU0MDIzODcwOWI2NzBjMzUwYjMzZA==')
            ->setBasicAuthentication('elastic', 'Ou9kBKtbMI0iiXKsKkOh2dmY')
            ->build();

        $params = [
            'index' => $this->dataSource->index_name,
            'body' => []
        ];

        $mappings = [
            'date' => 'date',
            'state' => 'state',
            'newDeaths' => 'newDeaths'
        ];

        $header = fgetcsv(fopen($this->dataSource->csv_path, "r"), 1000, ",");

        $row = 1;
        if (($handle = fopen($this->dataSource->csv_path, "r")) !== FALSE) {
            $params = [
                'index' => $this->dataSource->index_name,
            ];
            $this->elasticClient->indices()->create($params);

//            $responses = $this->elasticClient->deleteByQuery([
//                'index' => $this->dataSource->index_name,
//                'body' => [
//                    'query' => [
//                        'match_all' => new \stdClass()
//                    ]
//                ]
//            ]);

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $line = [];
                foreach($mappings as $mapped => $original) {
                    $line[$mapped] = $data[array_search($original, $header)];
                }
                $params['body'][] = [
                    'index' => [
                        '_index' => $this->dataSource->index_name,
                    ]
                ];
                $params['body'][] = $line;
                var_dump($line);

                if ($row % 100 == 0) {
                    $responses = $this->elasticClient->bulk($params);

                    // erase the old bulk request
                    $params['body'] = [];

                    // unset the bulk response when you are done to save memory
                    unset($responses);
                }

                $row++;
            }
            fclose($handle);

            if (!empty($params['body'])) {
                $responses = $this->elasticClient->bulk($params);
            }

            var_dump($this->elasticClient->indices()->stats(['index' => $this->dataSource->index_name]));
        }
    }
}
