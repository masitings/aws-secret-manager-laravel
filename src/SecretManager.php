<?php

namespace Masitings\AwsSecretManager;

use Aws\SecretsManager\SecretsManagerClient;

class SecretManager
{
    protected $client;
    public function loadSecret()
    {
        if (config('aws-secret.debug')) {
            $start = microtime(true);
        }

        if (in_array(config('app.env'), config('aws-secret.environments'))) {
            if (!$this->checkCache()) {
                return $this->getVariables();
            }
        }

        if (config('aws-secret.debug')) {
            $timeElapsed = microtime(true) - $start;
            error_log('Datastore secret request time : '.$timeElapsed);
        }
    }

    protected function checkCache()
    {
        if (cache()->has(config('aws-secret.cache.key'))) {
            $caches = cache()->get(config('aws-secret.cache.key'));
            if (is_array($caches) && count($caches) > 0) {
                foreach ($caches as $key => $value) {
                    config([$key => $value]);
                }
                return true;
            }
        }
        return false;
    }

    protected function getVariables()
    {
        try {
            $client = new SecretsManagerClient([
                'version' => '2017-10-17',
                'region' => config('aws-secret.region')
            ]);

            $secrets = $client->listSecrets([
                'Filters' => [
                    [
                        'Key' => 'tag-key',
                        'Values' => [config('aws-secret.tag.name')]
                    ],
                    [
                        'Key' => 'tag-value',
                        'Values' => [config('aws-secret.tag.value')],
                    ],
                ]
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error($e->getMessage());
            return false;
        }
        $arr = [];
        foreach ($secrets['SecretList'] as $secret) {
            if (isset($secret['ARN'])) {
                $result = $client->getSecretValue([
                    'SecretId' => $secret['ARN']
                ]);

                $secretValues = json_decode($result['SecretString'], true);
                $arr[] = $secretValues;
                if (is_array($secretValues) && count($secretValues) > 0) {
                    foreach ($secretValues as $name => $vals) {
                        config([$name => $vals]);
                    }
                }
                $this->storeToCache('aws_sm', $secretValues);
            }
        }
        return $arr;
    }

    protected function storeToCache($name, $val)
    {
        if (config('aws-secret.cache.enable')) {
            \Illuminate\Support\Facades\Cache::store(config('aws-secret.cache.store'))->put($name, $val, config('aws-secret.cache.expiry') * 60);
        }
    }
}
