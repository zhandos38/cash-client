<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "log".
 *
 * @property int $id
 * @property int $source_id
 * @property string $message
 * @property int $started_at
 * @property int $finished_at
 * @property int $status
 */
class Log extends \yii\db\ActiveRecord
{
    const STATUS_SUCCESS = 0;
    const STATUS_VALIDATE_ERROR = 1;
    const STATUS_EXCEPTION = 2;

    const SOURCE_EXPORT_ORDER = 0;
    const SOURCE_EXPORT_INVOICE = 1;
    const SOURCE_EXPORT_PRODUCT = 2;
    const SOURCE_EXPORT_CUSTOMER = 3;
    const SOURCE_EXPORT_SUPPLIER = 4;
    const SOURCE_EXPORT_SHIFT = 5;
    const SOURCE_EXPORT_STAFF = 6;
    const SOURCE_EXPORT_BARCODE = 7;
    const SOURCE_EXPORT_BARCODE_TEMP = 8;
    const SOURCE_EXPORT_SETTINGS = 9;
    const SOURCE_CHECK_EXPIRE_DATE = 10;
    const SOURCE_INVALID_EXPIRE_DATE = 11;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['source_id', 'started_at', 'finished_at', 'status'], 'integer'],
            [['message'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'source_id' => 'Target ID',
            'message' => 'Message',
            'started_at' => 'Started At',
            'finished_at' => 'Finished At',
            'status' => 'Status',
        ];
    }

    public static function createLog($source_id, $message, $status, $started_at)
    {
        $log = new self();
        $log->source_id = $source_id;
        $log->message = $message;
        $log->status = $status;
        $log->started_at = $started_at;
        $log->finished_at = time();
        $log->save();
    }
}
