<?php

namespace app\models\base;

use Yii;

/**
 * This is the base model class for table "{{%project}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $start_date
 * @property string $end_date
 * @property string $dead_line
 * @property string $status
 * @property string $note
 *
 * @property \app\models\Expence[] $expences
 * @property \app\models\Income[] $incomes
 * @property \app\models\ProjectProfile[] $projectProfiles
 * @property \app\models\Task[] $tasks
 */
class Project extends \yii\db\ActiveRecord
{

    use \mootensai\relation\RelationTrait;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'start_date', 'end_date', 'dead_line', 'status'], 'required'],
            [['start_date', 'end_date', 'dead_line'], 'safe'],
            [['note'], 'string'],
            [['name', 'status'], 'string', 'max' => 255]
        ];
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%project}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'dead_line' => 'Dead Line',
            'status' => 'Status',
            'note' => 'Note',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExpences()
    {
        return $this->hasMany(\app\models\Expence::className(), ['project_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIncomes()
    {
        return $this->hasMany(\app\models\Income::className(), ['project_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectProfiles()
    {
        return $this->hasMany(\app\models\ProjectProfile::className(), ['project_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasks()
    {
        return $this->hasMany(\app\models\Task::className(), ['project_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return \app\models\ProjectQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\ProjectQuery(get_called_class());
    }
}
