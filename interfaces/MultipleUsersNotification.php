<?php

namespace app\interfaces;

interface MultipleUsersNotification
{
    public function getUsersForNotify(): array;
}