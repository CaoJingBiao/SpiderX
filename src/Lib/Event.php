<?php
/**
 * Short description for Event.php
 *
 * @package Event
 * @author zhenyangze <zhenyangze@gmail.com>
 * @version 0.1
 * @copyright (C) 2018 zhenyangze <zhenyangze@gmail.com>
 * @license MIT
 */
namespace SpiderX\Lib;

/**
 *
 */
class Event
{
    /**
     * array
     *
     * @return
     */
    protected static $listens = array();

    /**
     * listen
     *
     * @param $event
     * @param $callback
     * @param $once
     *
     * @return
     */
    public static function listen($event, $callback, $once = false)
    {
        if (!is_callable($callback)) {
            return false;
        }
        self::$listens[$event][]    = array('callback'=>$callback, 'once'=>$once);
        return true;
    }

    /**
     * one
     *
     * @param $event
     * @param $callback
     *
     * @return
     */
    public static function one($event, $callback)
    {
        return self::listen($event, $callback, true);
    }

    /**
     * remove
     *
     * @param $event
     * @param $index
     *
     * @return
     */
    public static function remove($event, $index = null)
    {
        if (is_null($index)) {
            unset(self::$listens[$event]);
        } else {
            unset(self::$listens[$event][$index]);
        }
    }

    /**
     * trigger
     *
     * @return
     */
    public static function trigger()
    {
        if (!func_num_args()) {
            return;
        }
        $args = func_get_args();
        $event = array_shift($args);
        if (!isset(self::$listens[$event])) {
            return false;
        }
        foreach ((array) self::$listens[$event] as $index => $listen) {
            $callback               = $listen['callback'];
            $listen['once'] && self::remove($event, $index);
            call_user_func_array($callback, $args);
        }
    }
}
