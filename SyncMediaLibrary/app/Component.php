<?php


namespace app;


class Component
{
    private $events = [];

    public function on(string $name, callable $handler, $data = null)
    {
        $this->events[$name][] = [$handler, $data];
    }

    public function off(string $name, callable $handler = null)
    {
        if (empty($this->events[$name])) {
            return false;
        }
        if ($handler === null) {
            unset($this->events[$name]);
            return true;
        }
        $removed = false;
        if (isset($this->events[$name])) {
            foreach ($this->events[$name] as $i => $event) {
                if ($event[0] === $handler) {
                    unset($this->events[$name][$i]);
                    $removed = true;
                }
            }
            if ($removed) {
                $this->events[$name] = array_values($this->events[$name]);
                return $removed;
            }
        }
        return $removed;
    }

    public function trigger(string $name, Event $event = null)
    {
        if (isset($this->events[$name])) {
            if ($event === null) {
                $event = new Event();
            }
            if ($event->sender === null) {
                $event->sender = $this;
            }
            $event->name = $name;
            foreach ($this->events[$name] as $handler) {
                $event->data = $handler[1];
                call_user_func($handler[0], $event);
            }
        }
    }
}
