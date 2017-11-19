<?php

namespace App\Consumers;

use App\ES\ESIndexServiceException;
use App\ES\ESIndexServiceFactory;
use Illuminate\Support\Facades\Log;

class ESIndexConsumer extends Consumer
{
    /**
     * @var \App\ES\ESIndexServiceFactory
     */
    private $serviceFactory;

    public function __construct(ESIndexServiceFactory $serviceFactory)
    {
        $this->serviceFactory = $serviceFactory;
        parent::__construct(env('ES_INDEX_QUEUE'));
    }

    public function callback($message, $resolver)
    {
        $details = json_decode(base64_decode($message->body), true);
        try {
            if (empty($details['type'])) {
                throw new ESIndexServiceException('Missing type on es index task.');
            }

            $esIndexService = $this->serviceFactory->createService($details['type']);

            $esIndexService->process($details);
        } catch (ESIndexServiceException $e) {
            //disregard message
            Log::error('Error with task passed to : ' . $e->getMessage());
            $resolver->acknowledge($message);
            return;
        }

        $resolver->acknowledge($message);
    }
}
