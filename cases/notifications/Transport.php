<?php

namespace app\cases\notifications;

interface Transport
{
    public function getId(): string;

    public function notify(MessageDto $messageDto): bool;
}