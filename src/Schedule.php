<?php
namespace Alliv\Iamport;

class Schedule
{
    protected $response;
    protected $customData;

    public function __construct($response)
    {
        $this->response = $response;
        $this->customData = json_decode($response->custom_data);
    }

    public function __get($name)
    {
        if (isset($this->response->{$name})) {
            return $this->response->{$name};
        }
        return null;
    }

    public function getCustomData($name = null)
    {
        if (is_null($name)) {
            return $this->customData;
        }
        return $this->customData->{$name};
    }

    public static function fromArray($items)
    {
        $schedules = [];
        foreach ($items as $item) {
            $schedules[] = new self($item);
        }

        return $schedules;
    }
}