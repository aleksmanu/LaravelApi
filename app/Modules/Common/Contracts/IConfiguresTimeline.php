<?php
namespace App\Modules\Common\Contracts;

interface IConfiguresTimeline
{
    public function constructTimelineDataPacket(string $type, array $extraData): array;
}
