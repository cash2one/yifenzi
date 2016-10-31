<?php
/**
 * 格式化数值显示  扩展
 *
 * 时间格式化：为空则不格式化
 * @author zhenjun_xu <412530435@qq.com>
 */

class Formatter extends CFormatter{

    /**
     * Formats the value as a date.
     * @param mixed $value the value to be formatted
     * @return string the formatted result
     * @see dateFormat
     */
    public function formatDate($value)
    {
        return empty($value) ? '': parent::formatDate($value);
    }

    /**
     * Formats the value as a time.
     * @param mixed $value the value to be formatted
     * @return string the formatted result
     * @see timeFormat
     */
    public function formatTime($value)
    {
        return empty($value) ? '': parent::formatTime($value);
    }

    /**
     * Formats the value as a date and time.
     * @param mixed $value the value to be formatted
     * @return string the formatted result
     * @see datetimeFormat
     */
    public function formatDatetime($value)
    {
        return empty($value) ? '': parent::formatDatetime($value);
    }
} 