<?php

namespace App\Forgerock\Formatter;

class JsonFormatter extends \Monolog\Formatter\JsonFormatter
{
    public function __construct(int $batchMode = \Monolog\Formatter\JsonFormatter::BATCH_MODE_JSON, bool $appendNewline = true)
    {
        parent::__construct($batchMode, $appendNewline);
    }

    /**
     * {@inheritdoc}
     *
     * @suppress PhanTypeComparisonToArray
     */
    public function format(array $record): string
    {
        $normalized = $this->normalize($record);
        $normalized['log_date'] = $normalized['datetime']->setTimezone(new \DateTimeZone('Asia/Jakarta'))->format(\DateTime::RFC3339_EXTENDED);
        unset($normalized['datetime']);//remove because efk error on EFK
        if (isset($normalized['context']) && $normalized['context'] === []) {
            $normalized['context'] = new \stdClass;
        }
        if (isset($normalized['extra']) && $normalized['extra'] === []) {
            $normalized['extra'] = new \stdClass;
        }

        return $this->toJson($normalized, true) . ($this->appendNewline ? "\n" : '');
    }
}
