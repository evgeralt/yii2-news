<?php


namespace app\components;

use app\cases\notifications\MessageDto;
use app\cases\notifications\Transport;
use yii\base\BaseObject;

class Notifications extends BaseObject
{
    /** @var Transport[]|string[] */
    public $transports = [];

    public function notify(MessageDto $dto, array $transports = []): void
    {
        $transports = $transports ?: $this->transports;
        foreach ($transports as $transport) {
            (new $transport)->notify($dto);
        }
    }
}