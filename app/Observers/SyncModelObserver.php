<?php

namespace App\Observers;

use Bschmitt\Amqp\Facades\Amqp;
use Bschmitt\Amqp\Message;
use Illuminate\Database\Eloquent\Model;
use ReflectionClass;
use Str;

class SyncModelObserver
{
    public function created(Model $model)
    {
        $modelName = $this->getModelName($model);

        $data = $model->toArray();

        $action = __FUNCTION__;

        $routingKey = "model.{$modelName}.{$action}";

        try {
            $this->publish($routingKey, $data);
        } catch (\Exception $exception) {
            $id = $model->id;

            $myException =  new \Exception("The model $modelName with ID $id not synced on $action", 0, $exception);

            report($myException);
        }
    }

    public function updated(Model $model)
    {
        $modelName = $this->getModelName($model);

        $data = $model->toArray();

        $action = __FUNCTION__;

        $routingKey = "model.{$modelName}.{$action}";

        try {
            $this->publish($routingKey, $data);
        } catch (\Exception $exception) {
            $this->reportException([
                'id' => $model->id,
                'modelName' => $modelName,
                'exception' => $exception,
                'action' => $action
            ]);
        }
    }


    public function deleted(Model $model)
    {
        $modelName = $this->getModelName($model);

        $data = ['id' => $model->id];

        $action = __FUNCTION__;

        $routingKey = "model.{$modelName}.{$action}";

        try {
            $this->publish($routingKey, $data);
        } catch (\Exception $exception) {
            $this->reportException([
                'id' => $model->id,
                'modelName' => $modelName,
                'exception' => $exception,
                'action' => $action
            ]);
        }
    }


    public function restored(Model $model)
    {
        //
    }

    public function forceDeleted(Model $model)
    {
        //
    }

    protected function getModelName(Model $model)
    {
        $shortName = (new ReflectionClass($model))->getShortName();

        return  Str::snake($shortName);
    }

    protected function publish($routingKey, array $data)
    {
        $message = new Message(
            json_encode($data),
            [
                'content_type' => 'application/json',
                'delivery_mode' => 2
            ]
        );

        Amqp::publish($routingKey, $message, ['exchange_type' => 'topic', 'exchange' => 'amq.topic']);
    }

    protected function reportException(array $params)
    {
        list(
            'id' => $id,
            'modelName' => $modelName,
            'exception' => $exception,
            'action' => $action
        ) = $params;

        $myException =  new \Exception("The model $modelName with ID $id not synced on $action", 0, $exception);

        return report($myException);
    }
}
