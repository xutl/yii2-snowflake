<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace xutl\snowflake;

use Yii;
use yii\base\Component;
use yii\base\Exception;

/**
 * Class Snowflake
 * @package xutl\snowflake
 */
class Snowflake extends Component
{
    /**
     * @var int 开始时间截
     */
    public $epoch = 1414213562373;

    /**
     * @var int 工作机器ID(0~31)
     */
    public $workerId;

    /**
     * @var int 数据中心ID(0~31)
     */
    public $dataCenterId;

    /**
     * @var int 上次生成ID的时间截
     */
    private $_lastTimestamp;

    /**
     * @var int 毫秒内序列(0~4095)
     */
    private $_sequence = 0;

    const WORKER_BITS = 5;
    const DATA_CENTER_BITS = 5;
    const MAX_WORKER_ID = (-1 ^ (-1 << self::WORKER_BITS));
    const MAX_DATA_CENTER_ID = (-1 ^ (-1 << self::DATA_CENTER_BITS));
    const SEQUENCE_BITS = 12;
    const WORKER_ID_SHIFT = self::SEQUENCE_BITS;
    const DATA_CENTER_ID_SHIFT = self::SEQUENCE_BITS + self::WORKER_BITS;
    const TIMESTAMP_LEFT_SHIFT = self::SEQUENCE_BITS + self::WORKER_BITS + self::DATA_CENTER_BITS;
    const SEQUENCE_MASK = (-1 ^ (-1 << self::SEQUENCE_BITS));

    /**
     * 初始化
     * @throws Exception
     */
    public function init()
    {
        parent::init();
        if ($this->workerId > self::MAX_WORKER_ID || $this->workerId < 0) {
            throw new Exception(sprintf("worker Id can't be greater than %d or less than 0",self::MAX_WORKER_ID));
        }
        if ($this->dataCenterId > self::MAX_DATA_CENTER_ID || $this->dataCenterId < 0) {
            throw new Exception(sprintf("dataCenterId can't be greater than %d or less than 0",self::MAX_DATA_CENTER_ID));
        }
    }

    /**
     * 获得下一个ID (该方法是线程安全的)
     * @return int
     * @throws Exception
     */
    public function next()
    {
        $timestamp = $this->milliTime();
        if ($timestamp < $this->_lastTimestamp) {
            throw new Exception(sprintf("Clock moved backwards.  Refusing to generate id for %d milliseconds", $this->_lastTimestamp - $timestamp));
        }

        if ($this->_lastTimestamp == $timestamp) {
            $this->_sequence = ($this->_sequence + 1) & self::SEQUENCE_MASK;
            if ($this->_sequence == 0) {
                $timestamp = $this->waitNextMilli($this->_lastTimestamp);
            }
        } else {
            $this->_sequence = 0;
        }
        $this->_lastTimestamp = $timestamp;
        return (($timestamp - $this->epoch) << self::TIMESTAMP_LEFT_SHIFT)
            | ($this->dataCenterId << self::DATA_CENTER_ID_SHIFT)
            | ($this->workerId << self::WORKER_ID_SHIFT)
            | $this->_sequence;
    }

    /**
     * 阻塞到下一个毫秒，直到获得新的时间戳
     * @param int $lastTimestamp 上次生成ID的时间截
     * @return int 当前时间戳
     */
    protected function waitNextMilli($lastTimestamp)
    {
        $timestamp = $this->milliTime();
        while ($timestamp <= $lastTimestamp) {
            $timestamp = $this->milliTime();
        }
        return $timestamp;
    }

    /**
     * 返回以毫秒为单位的当前时间
     * @return int 当前时间(毫秒)
     */
    protected function milliTime()
    {
        $microTime = microtime();
        $comps = explode(' ', $microTime);
        // Note: Using a string here to prevent loss of precision
        // in case of "overflow" (PHP converts it to a double)
        return sprintf('%d%03d', $comps[1], $comps[0] * 1000);
    }
}